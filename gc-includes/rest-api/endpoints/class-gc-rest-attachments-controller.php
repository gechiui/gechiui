<?php
/**
 * REST API: GC_REST_Attachments_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Core controller used to access attachments via the REST API.
 *
 *
 *
 * @see GC_REST_Posts_Controller
 */
class GC_REST_Attachments_Controller extends GC_REST_Posts_Controller {

	/**
	 * Whether the controller supports batching.
	 *
	 * @var false
	 */
	protected $allow_batch = false;

	/**
	 * Registers the routes for attachments.
	 *
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		parent::register_routes();
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/post-process',
			array(
				'methods'             => GC_REST_Server::CREATABLE,
				'callback'            => array( $this, 'post_process_item' ),
				'permission_callback' => array( $this, 'post_process_item_permissions_check' ),
				'args'                => array(
					'id'     => array(
						'description' => __( '附件的唯一标识符。' ),
						'type'        => 'integer',
					),
					'action' => array(
						'type'     => 'string',
						'enum'     => array( 'create-image-subsizes' ),
						'required' => true,
					),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/edit',
			array(
				'methods'             => GC_REST_Server::CREATABLE,
				'callback'            => array( $this, 'edit_media_item' ),
				'permission_callback' => array( $this, 'edit_media_item_permissions_check' ),
				'args'                => $this->get_edit_media_item_args(),
			)
		);
	}

	/**
	 * Determines the allowed query_vars for a get_items() response and
	 * prepares for GC_Query.
	 *
	 *
	 * @param array           $prepared_args Optional. Array of prepared arguments. Default empty array.
	 * @param GC_REST_Request $request       Optional. Request to prepare items for.
	 * @return array Array of query arguments.
	 */
	protected function prepare_items_query( $prepared_args = array(), $request = null ) {
		$query_args = parent::prepare_items_query( $prepared_args, $request );

		if ( empty( $query_args['post_status'] ) ) {
			$query_args['post_status'] = 'inherit';
		}

		$media_types = $this->get_media_types();

		if ( ! empty( $request['media_type'] ) && isset( $media_types[ $request['media_type'] ] ) ) {
			$query_args['post_mime_type'] = $media_types[ $request['media_type'] ];
		}

		if ( ! empty( $request['mime_type'] ) ) {
			$parts = explode( '/', $request['mime_type'] );
			if ( isset( $media_types[ $parts[0] ] ) && in_array( $request['mime_type'], $media_types[ $parts[0] ], true ) ) {
				$query_args['post_mime_type'] = $request['mime_type'];
			}
		}

		// Filter query clauses to include filenames.
		if ( isset( $query_args['s'] ) ) {
			add_filter( 'posts_clauses', '_filter_query_attachment_filenames' );
		}

		return $query_args;
	}

	/**
	 * Checks if a given request has access to create an attachment.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error Boolean true if the attachment may be created, or a GC_Error if not.
	 */
	public function create_item_permissions_check( $request ) {
		$ret = parent::create_item_permissions_check( $request );

		if ( ! $ret || is_gc_error( $ret ) ) {
			return $ret;
		}

		if ( ! current_user_can( 'upload_files' ) ) {
			return new GC_Error(
				'rest_cannot_create',
				__( '抱歉，您不能在此站点上传媒体。' ),
				array( 'status' => 400 )
			);
		}

		// Attaching media to a post requires ability to edit said post.
		if ( ! empty( $request['post'] ) && ! current_user_can( 'edit_post', (int) $request['post'] ) ) {
			return new GC_Error(
				'rest_cannot_edit',
				__( '抱歉，您不能给这篇文章上传媒体。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Creates a single attachment.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, GC_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['post'] ) && in_array( get_post_type( $request['post'] ), array( 'revision', 'attachment' ), true ) ) {
			return new GC_Error(
				'rest_invalid_param',
				__( '无效的父类型。' ),
				array( 'status' => 400 )
			);
		}

		$insert = $this->insert_attachment( $request );

		if ( is_gc_error( $insert ) ) {
			return $insert;
		}

		$schema = $this->get_item_schema();

		// Extract by name.
		$attachment_id = $insert['attachment_id'];
		$file          = $insert['file'];

		if ( isset( $request['alt_text'] ) ) {
			update_post_meta( $attachment_id, '_gc_attachment_image_alt', sanitize_text_field( $request['alt_text'] ) );
		}

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $attachment_id );

			if ( is_gc_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$attachment    = get_post( $attachment_id );
		$fields_update = $this->update_additional_fields_for_object( $attachment, $request );

		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		/**
		 * Fires after a single attachment is completely created or updated via the REST API.
		 *
		 *
		 * @param GC_Post         $attachment Inserted or updated attachment object.
		 * @param GC_REST_Request $request    Request object.
		 * @param bool            $creating   True when creating an attachment, false when updating.
		 */
		do_action( 'rest_after_insert_attachment', $attachment, $request, true );

		gc_after_insert_post( $attachment, false, null );

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			// Set a custom header with the attachment_id.
			// Used by the browser/client to resume creating image sub-sizes after a PHP fatal error.
			header( 'X-GC-Upload-Attachment-ID: ' . $attachment_id );
		}

		// Include media and image functions to get access to gc_generate_attachment_metadata().
		require_once ABSPATH . 'gc-admin/includes/media.php';
		require_once ABSPATH . 'gc-admin/includes/image.php';

		// Post-process the upload (create image sub-sizes, make PDF thumbnails, etc.) and insert attachment meta.
		// At this point the server may run out of resources and post-processing of uploaded images may fail.
		gc_update_attachment_metadata( $attachment_id, gc_generate_attachment_metadata( $attachment_id, $file ) );

		$response = $this->prepare_item_for_response( $attachment, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $attachment_id ) ) );

		return $response;
	}

	/**
	 * Inserts the attachment post in the database. Does not update the attachment meta.
	 *
	 *
	 * @param GC_REST_Request $request
	 * @return array|GC_Error
	 */
	protected function insert_attachment( $request ) {
		// Get the file via $_FILES or raw data.
		$files   = $request->get_file_params();
		$headers = $request->get_headers();

		if ( ! empty( $files ) ) {
			$file = $this->upload_from_file( $files, $headers );
		} else {
			$file = $this->upload_from_data( $request->get_body(), $headers );
		}

		if ( is_gc_error( $file ) ) {
			return $file;
		}

		$name       = gc_basename( $file['file'] );
		$name_parts = pathinfo( $name );
		$name       = trim( substr( $name, 0, -( 1 + strlen( $name_parts['extension'] ) ) ) );

		$url  = $file['url'];
		$type = $file['type'];
		$file = $file['file'];

		// Include image functions to get access to gc_read_image_metadata().
		require_once ABSPATH . 'gc-admin/includes/image.php';

		// Use image exif/iptc data for title and caption defaults if possible.
		$image_meta = gc_read_image_metadata( $file );

		if ( ! empty( $image_meta ) ) {
			if ( empty( $request['title'] ) && trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
				$request['title'] = $image_meta['title'];
			}

			if ( empty( $request['caption'] ) && trim( $image_meta['caption'] ) ) {
				$request['caption'] = $image_meta['caption'];
			}
		}

		$attachment = $this->prepare_item_for_database( $request );

		$attachment->post_mime_type = $type;
		$attachment->guid           = $url;

		if ( empty( $attachment->post_title ) ) {
			$attachment->post_title = preg_replace( '/\.[^.]+$/', '', gc_basename( $file ) );
		}

		// $post_parent is inherited from $attachment['post_parent'].
		$id = gc_insert_attachment( gc_slash( (array) $attachment ), $file, 0, true, false );

		if ( is_gc_error( $id ) ) {
			if ( 'db_update_error' === $id->get_error_code() ) {
				$id->add_data( array( 'status' => 500 ) );
			} else {
				$id->add_data( array( 'status' => 400 ) );
			}

			return $id;
		}

		$attachment = get_post( $id );

		/**
		 * Fires after a single attachment is created or updated via the REST API.
		 *
		 *
		 * @param GC_Post         $attachment Inserted or updated attachment
		 *                                    object.
		 * @param GC_REST_Request $request    The request sent to the API.
		 * @param bool            $creating   True when creating an attachment, false when updating.
		 */
		do_action( 'rest_insert_attachment', $attachment, $request, true );

		return array(
			'attachment_id' => $id,
			'file'          => $file,
		);
	}

	/**
	 * Updates a single attachment.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, GC_Error object on failure.
	 */
	public function update_item( $request ) {
		if ( ! empty( $request['post'] ) && in_array( get_post_type( $request['post'] ), array( 'revision', 'attachment' ), true ) ) {
			return new GC_Error(
				'rest_invalid_param',
				__( '无效的父类型。' ),
				array( 'status' => 400 )
			);
		}

		$attachment_before = get_post( $request['id'] );
		$response          = parent::update_item( $request );

		if ( is_gc_error( $response ) ) {
			return $response;
		}

		$response = rest_ensure_response( $response );
		$data     = $response->get_data();

		if ( isset( $request['alt_text'] ) ) {
			update_post_meta( $data['id'], '_gc_attachment_image_alt', $request['alt_text'] );
		}

		$attachment = get_post( $request['id'] );

		$fields_update = $this->update_additional_fields_for_object( $attachment, $request );

		if ( is_gc_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		/** This action is documented in gc-includes/rest-api/endpoints/class-gc-rest-attachments-controller.php */
		do_action( 'rest_after_insert_attachment', $attachment, $request, false );

		gc_after_insert_post( $attachment, true, $attachment_before );

		$response = $this->prepare_item_for_response( $attachment, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Performs post processing on an attachment.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, GC_Error object on failure.
	 */
	public function post_process_item( $request ) {
		switch ( $request['action'] ) {
			case 'create-image-subsizes':
				require_once ABSPATH . 'gc-admin/includes/image.php';
				gc_update_image_subsizes( $request['id'] );
				break;
		}

		$request['context'] = 'edit';

		return $this->prepare_item_for_response( get_post( $request['id'] ), $request );
	}

	/**
	 * Checks if a given request can perform post processing on an attachment.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has access to update the item, GC_Error object otherwise.
	 */
	public function post_process_item_permissions_check( $request ) {
		return $this->update_item_permissions_check( $request );
	}

	/**
	 * Checks if a given request has access to editing media.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function edit_media_item_permissions_check( $request ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			return new GC_Error(
				'rest_cannot_edit_image',
				__( '抱歉，您不能在此站点上传媒体。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return $this->update_item_permissions_check( $request );
	}

	/**
	 * Applies edits to a media item and creates a new attachment record.
	 *
	 *
	 * @param GC_REST_Request $request Full details about the request.
	 * @return GC_REST_Response|GC_Error Response object on success, GC_Error object on failure.
	 */
	public function edit_media_item( $request ) {
		require_once ABSPATH . 'gc-admin/includes/image.php';

		$attachment_id = $request['id'];

		// This also confirms the attachment is an image.
		$image_file = gc_get_original_image_path( $attachment_id );
		$image_meta = gc_get_attachment_metadata( $attachment_id );

		if (
			! $image_meta ||
			! $image_file ||
			! gc_image_file_matches_image_meta( $request['src'], $image_meta, $attachment_id )
		) {
			return new GC_Error(
				'rest_unknown_attachment',
				__( '无法获取文件的元信息。' ),
				array( 'status' => 404 )
			);
		}

		$supported_types = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
		$mime_type       = get_post_mime_type( $attachment_id );
		if ( ! in_array( $mime_type, $supported_types, true ) ) {
			return new GC_Error(
				'rest_cannot_edit_file_type',
				__( '无法编辑此类文件。' ),
				array( 'status' => 400 )
			);
		}

		// The `modifiers` param takes precedence over the older format.
		if ( isset( $request['modifiers'] ) ) {
			$modifiers = $request['modifiers'];
		} else {
			$modifiers = array();

			if ( ! empty( $request['rotation'] ) ) {
				$modifiers[] = array(
					'type' => 'rotate',
					'args' => array(
						'angle' => $request['rotation'],
					),
				);
			}

			if ( isset( $request['x'], $request['y'], $request['width'], $request['height'] ) ) {
				$modifiers[] = array(
					'type' => 'crop',
					'args' => array(
						'left'   => $request['x'],
						'top'    => $request['y'],
						'width'  => $request['width'],
						'height' => $request['height'],
					),
				);
			}

			if ( 0 === count( $modifiers ) ) {
				return new GC_Error(
					'rest_image_not_edited',
					__( '图片未被编辑。请在应用更改前编辑图片。' ),
					array( 'status' => 400 )
				);
			}
		}

		/*
		 * If the file doesn't exist, attempt a URL fopen on the src link.
		 * This can occur with certain file replication plugins.
		 * Keep the original file path to get a modified name later.
		 */
		$image_file_to_edit = $image_file;
		if ( ! file_exists( $image_file_to_edit ) ) {
			$image_file_to_edit = _load_image_to_edit_path( $attachment_id );
		}

		$image_editor = gc_get_image_editor( $image_file_to_edit );

		if ( is_gc_error( $image_editor ) ) {
			return new GC_Error(
				'rest_unknown_image_file_type',
				__( '无法编辑此图片。' ),
				array( 'status' => 500 )
			);
		}

		foreach ( $modifiers as $modifier ) {
			$args = $modifier['args'];
			switch ( $modifier['type'] ) {
				case 'rotate':
					// Rotation direction: clockwise vs. counter clockwise.
					$rotate = 0 - $args['angle'];

					if ( 0 !== $rotate ) {
						$result = $image_editor->rotate( $rotate );

						if ( is_gc_error( $result ) ) {
							return new GC_Error(
								'rest_image_rotation_failed',
								__( '无法旋转此图片。' ),
								array( 'status' => 500 )
							);
						}
					}

					break;

				case 'crop':
					$size = $image_editor->get_size();

					$crop_x = round( ( $size['width'] * $args['left'] ) / 100.0 );
					$crop_y = round( ( $size['height'] * $args['top'] ) / 100.0 );
					$width  = round( ( $size['width'] * $args['width'] ) / 100.0 );
					$height = round( ( $size['height'] * $args['height'] ) / 100.0 );

					if ( $size['width'] !== $width && $size['height'] !== $height ) {
						$result = $image_editor->crop( $crop_x, $crop_y, $width, $height );

						if ( is_gc_error( $result ) ) {
							return new GC_Error(
								'rest_image_crop_failed',
								__( '无法裁剪此图片。' ),
								array( 'status' => 500 )
							);
						}
					}

					break;

			}
		}

		// Calculate the file name.
		$image_ext  = pathinfo( $image_file, PATHINFO_EXTENSION );
		$image_name = gc_basename( $image_file, ".{$image_ext}" );

		// Do not append multiple `-edited` to the file name.
		// The user may be editing a previously edited image.
		if ( preg_match( '/-edited(-\d+)?$/', $image_name ) ) {
			// Remove any `-1`, `-2`, etc. `gc_unique_filename()` will add the proper number.
			$image_name = preg_replace( '/-edited(-\d+)?$/', '-edited', $image_name );
		} else {
			// Append `-edited` before the extension.
			$image_name .= '-edited';
		}

		$filename = "{$image_name}.{$image_ext}";

		// Create the uploads sub-directory if needed.
		$uploads = gc_upload_dir();

		// Make the file name unique in the (new) upload directory.
		$filename = gc_unique_filename( $uploads['path'], $filename );

		// Save to disk.
		$saved = $image_editor->save( $uploads['path'] . "/$filename" );

		if ( is_gc_error( $saved ) ) {
			return $saved;
		}

		// Create new attachment post.
		$new_attachment_post = array(
			'post_mime_type' => $saved['mime-type'],
			'guid'           => $uploads['url'] . "/$filename",
			'post_title'     => $image_name,
			'post_content'   => '',
		);

		// Copy post_content, post_excerpt, and post_title from the edited image's attachment post.
		$attachment_post = get_post( $attachment_id );

		if ( $attachment_post ) {
			$new_attachment_post['post_content'] = $attachment_post->post_content;
			$new_attachment_post['post_excerpt'] = $attachment_post->post_excerpt;
			$new_attachment_post['post_title']   = $attachment_post->post_title;
		}

		$new_attachment_id = gc_insert_attachment( gc_slash( $new_attachment_post ), $saved['path'], 0, true );

		if ( is_gc_error( $new_attachment_id ) ) {
			if ( 'db_update_error' === $new_attachment_id->get_error_code() ) {
				$new_attachment_id->add_data( array( 'status' => 500 ) );
			} else {
				$new_attachment_id->add_data( array( 'status' => 400 ) );
			}

			return $new_attachment_id;
		}

		// Copy the image alt text from the edited image.
		$image_alt = get_post_meta( $attachment_id, '_gc_attachment_image_alt', true );

		if ( ! empty( $image_alt ) ) {
			// update_post_meta() expects slashed.
			update_post_meta( $new_attachment_id, '_gc_attachment_image_alt', gc_slash( $image_alt ) );
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			// Set a custom header with the attachment_id.
			// Used by the browser/client to resume creating image sub-sizes after a PHP fatal error.
			header( 'X-GC-Upload-Attachment-ID: ' . $new_attachment_id );
		}

		// Generate image sub-sizes and meta.
		$new_image_meta = gc_generate_attachment_metadata( $new_attachment_id, $saved['path'] );

		// Copy the EXIF metadata from the original attachment if not generated for the edited image.
		if ( isset( $image_meta['image_meta'] ) && isset( $new_image_meta['image_meta'] ) && is_array( $new_image_meta['image_meta'] ) ) {
			// Merge but skip empty values.
			foreach ( (array) $image_meta['image_meta'] as $key => $value ) {
				if ( empty( $new_image_meta['image_meta'][ $key ] ) && ! empty( $value ) ) {
					$new_image_meta['image_meta'][ $key ] = $value;
				}
			}
		}

		// Reset orientation. At this point the image is edited and orientation is correct.
		if ( ! empty( $new_image_meta['image_meta']['orientation'] ) ) {
			$new_image_meta['image_meta']['orientation'] = 1;
		}

		// The attachment_id may change if the site is exported and imported.
		$new_image_meta['parent_image'] = array(
			'attachment_id' => $attachment_id,
			// Path to the originally uploaded image file relative to the uploads directory.
			'file'          => _gc_relative_upload_path( $image_file ),
		);

		/**
		 * Filters the meta data for the new image created by editing an existing image.
		 *
		 *
		 * @param array $new_image_meta    Meta data for the new image.
		 * @param int   $new_attachment_id Attachment post ID for the new image.
		 * @param int   $attachment_id     Attachment post ID for the edited (parent) image.
		 */
		$new_image_meta = apply_filters( 'gc_edited_image_metadata', $new_image_meta, $new_attachment_id, $attachment_id );

		gc_update_attachment_metadata( $new_attachment_id, $new_image_meta );

		$response = $this->prepare_item_for_response( get_post( $new_attachment_id ), $request );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $new_attachment_id ) ) );

		return $response;
	}

	/**
	 * Prepares a single attachment for create or update.
	 *
	 *
	 * @param GC_REST_Request $request Request object.
	 * @return stdClass|GC_Error Post object.
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared_attachment = parent::prepare_item_for_database( $request );

		// Attachment caption (post_excerpt internally).
		if ( isset( $request['caption'] ) ) {
			if ( is_string( $request['caption'] ) ) {
				$prepared_attachment->post_excerpt = $request['caption'];
			} elseif ( isset( $request['caption']['raw'] ) ) {
				$prepared_attachment->post_excerpt = $request['caption']['raw'];
			}
		}

		// Attachment description (post_content internally).
		if ( isset( $request['description'] ) ) {
			if ( is_string( $request['description'] ) ) {
				$prepared_attachment->post_content = $request['description'];
			} elseif ( isset( $request['description']['raw'] ) ) {
				$prepared_attachment->post_content = $request['description']['raw'];
			}
		}

		if ( isset( $request['post'] ) ) {
			$prepared_attachment->post_parent = (int) $request['post'];
		}

		return $prepared_attachment;
	}

	/**
	 * Prepares a single attachment output for response.
	 *
	 *
	 * @param GC_Post         $item    Attachment object.
	 * @param GC_REST_Request $request Request object.
	 * @return GC_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$post     = $item;
		$response = parent::prepare_item_for_response( $post, $request );
		$fields   = $this->get_fields_for_response( $request );
		$data     = $response->get_data();

		if ( in_array( 'description', $fields, true ) ) {
			$data['description'] = array(
				'raw'      => $post->post_content,
				/** This filter is documented in gc-includes/post-template.php */
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
		}

		if ( in_array( 'caption', $fields, true ) ) {
			/** This filter is documented in gc-includes/post-template.php */
			$caption = apply_filters( 'get_the_excerpt', $post->post_excerpt, $post );

			/** This filter is documented in gc-includes/post-template.php */
			$caption = apply_filters( 'the_excerpt', $caption );

			$data['caption'] = array(
				'raw'      => $post->post_excerpt,
				'rendered' => $caption,
			);
		}

		if ( in_array( 'alt_text', $fields, true ) ) {
			$data['alt_text'] = get_post_meta( $post->ID, '_gc_attachment_image_alt', true );
		}

		if ( in_array( 'media_type', $fields, true ) ) {
			$data['media_type'] = gc_attachment_is_image( $post->ID ) ? 'image' : 'file';
		}

		if ( in_array( 'mime_type', $fields, true ) ) {
			$data['mime_type'] = $post->post_mime_type;
		}

		if ( in_array( 'media_details', $fields, true ) ) {
			$data['media_details'] = gc_get_attachment_metadata( $post->ID );

			// Ensure empty details is an empty object.
			if ( empty( $data['media_details'] ) ) {
				$data['media_details'] = new stdClass;
			} elseif ( ! empty( $data['media_details']['sizes'] ) ) {

				foreach ( $data['media_details']['sizes'] as $size => &$size_data ) {

					if ( isset( $size_data['mime-type'] ) ) {
						$size_data['mime_type'] = $size_data['mime-type'];
						unset( $size_data['mime-type'] );
					}

					// Use the same method image_downsize() does.
					$image_src = gc_get_attachment_image_src( $post->ID, $size );
					if ( ! $image_src ) {
						continue;
					}

					$size_data['source_url'] = $image_src[0];
				}

				$full_src = gc_get_attachment_image_src( $post->ID, 'full' );

				if ( ! empty( $full_src ) ) {
					$data['media_details']['sizes']['full'] = array(
						'file'       => gc_basename( $full_src[0] ),
						'width'      => $full_src[1],
						'height'     => $full_src[2],
						'mime_type'  => $post->post_mime_type,
						'source_url' => $full_src[0],
					);
				}
			} else {
				$data['media_details']['sizes'] = new stdClass;
			}
		}

		if ( in_array( 'post', $fields, true ) ) {
			$data['post'] = ! empty( $post->post_parent ) ? (int) $post->post_parent : null;
		}

		if ( in_array( 'source_url', $fields, true ) ) {
			$data['source_url'] = gc_get_attachment_url( $post->ID );
		}

		if ( in_array( 'missing_image_sizes', $fields, true ) ) {
			require_once ABSPATH . 'gc-admin/includes/image.php';
			$data['missing_image_sizes'] = array_keys( gc_get_missing_image_subsizes( $post->ID ) );
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';

		$data = $this->filter_response_by_context( $data, $context );

		$links = $response->get_links();

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		foreach ( $links as $rel => $rel_links ) {
			foreach ( $rel_links as $link ) {
				$response->add_link( $rel, $link['href'], $link['attributes'] );
			}
		}

		/**
		 * Filters an attachment returned from the REST API.
		 *
		 * Allows modification of the attachment right before it is returned.
		 *
		 *
		 * @param GC_REST_Response $response The response object.
		 * @param GC_Post          $post     The original attachment post.
		 * @param GC_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_attachment', $response, $post, $request );
	}

	/**
	 * Retrieves the attachment's schema, conforming to JSON Schema.
	 *
	 *
	 * @return array Item schema as an array.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = parent::get_item_schema();

		$schema['properties']['alt_text'] = array(
			'description' => __( '在附件未能显示时显示的替换文本。' ),
			'type'        => 'string',
			'context'     => array( 'view', 'edit', 'embed' ),
			'arg_options' => array(
				'sanitize_callback' => 'sanitize_text_field',
			),
		);

		$schema['properties']['caption'] = array(
			'description' => __( '附件说明文字。' ),
			'type'        => 'object',
			'context'     => array( 'view', 'edit', 'embed' ),
			'arg_options' => array(
				'sanitize_callback' => null, // Note: sanitization implemented in self::prepare_item_for_database().
				'validate_callback' => null, // Note: validation implemented in self::prepare_item_for_database().
			),
			'properties'  => array(
				'raw'      => array(
					'description' => __( '附件的说明文字，存放于数据库。' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
				),
				'rendered' => array(
					'description' => __( '附件的 HTML 说明文字，经转换后用于显示。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
			),
		);

		$schema['properties']['description'] = array(
			'description' => __( '附件的描述。' ),
			'type'        => 'object',
			'context'     => array( 'view', 'edit' ),
			'arg_options' => array(
				'sanitize_callback' => null, // Note: sanitization implemented in self::prepare_item_for_database().
				'validate_callback' => null, // Note: validation implemented in self::prepare_item_for_database().
			),
			'properties'  => array(
				'raw'      => array(
					'description' => __( '附件的描述，存放于数据库中。' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
				),
				'rendered' => array(
					'description' => __( '附件的 HTML 描述，经转换后用于显示。' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		$schema['properties']['media_type'] = array(
			'description' => __( '附件类型。' ),
			'type'        => 'string',
			'enum'        => array( 'image', 'file' ),
			'context'     => array( 'view', 'edit', 'embed' ),
			'readonly'    => true,
		);

		$schema['properties']['mime_type'] = array(
			'description' => __( '附件的MIME类型。' ),
			'type'        => 'string',
			'context'     => array( 'view', 'edit', 'embed' ),
			'readonly'    => true,
		);

		$schema['properties']['media_details'] = array(
			'description' => __( '媒体文件具体到类型的详情。' ),
			'type'        => 'object',
			'context'     => array( 'view', 'edit', 'embed' ),
			'readonly'    => true,
		);

		$schema['properties']['post'] = array(
			'description' => __( '附件所属文章的ID。' ),
			'type'        => 'integer',
			'context'     => array( 'view', 'edit' ),
		);

		$schema['properties']['source_url'] = array(
			'description' => __( '原始附件文件的URL。' ),
			'type'        => 'string',
			'format'      => 'uri',
			'context'     => array( 'view', 'edit', 'embed' ),
			'readonly'    => true,
		);

		$schema['properties']['missing_image_sizes'] = array(
			'description' => __( '附件缺少的图片尺寸列表。' ),
			'type'        => 'array',
			'items'       => array( 'type' => 'string' ),
			'context'     => array( 'edit' ),
			'readonly'    => true,
		);

		unset( $schema['properties']['password'] );

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Handles an upload via raw POST data.
	 *
	 *
	 * @param array $data    Supplied file data.
	 * @param array $headers HTTP headers from the request.
	 * @return array|GC_Error Data from gc_handle_sideload().
	 */
	protected function upload_from_data( $data, $headers ) {
		if ( empty( $data ) ) {
			return new GC_Error(
				'rest_upload_no_data',
				__( '未提供数据。' ),
				array( 'status' => 400 )
			);
		}

		if ( empty( $headers['content_type'] ) ) {
			return new GC_Error(
				'rest_upload_no_content_type',
				__( '未提供Content-Type。' ),
				array( 'status' => 400 )
			);
		}

		if ( empty( $headers['content_disposition'] ) ) {
			return new GC_Error(
				'rest_upload_no_content_disposition',
				__( '未提供Content-Disposition标头值。' ),
				array( 'status' => 400 )
			);
		}

		$filename = self::get_filename_from_disposition( $headers['content_disposition'] );

		if ( empty( $filename ) ) {
			return new GC_Error(
				'rest_upload_invalid_disposition',
				__( '提供的Content-Disposition标头值无效。Content-Disposition标头值格式应为`attachment; filename="image.png"`或类似内容。' ),
				array( 'status' => 400 )
			);
		}

		if ( ! empty( $headers['content_md5'] ) ) {
			$content_md5 = array_shift( $headers['content_md5'] );
			$expected    = trim( $content_md5 );
			$actual      = md5( $data );

			if ( $expected !== $actual ) {
				return new GC_Error(
					'rest_upload_hash_mismatch',
					__( '内容哈希与期望值不匹配。' ),
					array( 'status' => 412 )
				);
			}
		}

		// Get the content-type.
		$type = array_shift( $headers['content_type'] );

		// Include filesystem functions to get access to gc_tempnam() and gc_handle_sideload().
		require_once ABSPATH . 'gc-admin/includes/file.php';

		// Save the file.
		$tmpfname = gc_tempnam( $filename );

		$fp = fopen( $tmpfname, 'w+' );

		if ( ! $fp ) {
			return new GC_Error(
				'rest_upload_file_error',
				__( '未能打开文件句柄。' ),
				array( 'status' => 500 )
			);
		}

		fwrite( $fp, $data );
		fclose( $fp );

		// Now, sideload it in.
		$file_data = array(
			'error'    => null,
			'tmp_name' => $tmpfname,
			'name'     => $filename,
			'type'     => $type,
		);

		$size_check = self::check_upload_size( $file_data );
		if ( is_gc_error( $size_check ) ) {
			return $size_check;
		}

		$overrides = array(
			'test_form' => false,
		);

		$sideloaded = gc_handle_sideload( $file_data, $overrides );

		if ( isset( $sideloaded['error'] ) ) {
			@unlink( $tmpfname );

			return new GC_Error(
				'rest_upload_sideload_error',
				$sideloaded['error'],
				array( 'status' => 500 )
			);
		}

		return $sideloaded;
	}

	/**
	 * Parses filename from a Content-Disposition header value.
	 *
	 * As per RFC6266:
	 *
	 *     content-disposition = "Content-Disposition" ":"
	 *                            disposition-type *( ";" disposition-parm )
	 *
	 *     disposition-type    = "inline" | "attachment" | disp-ext-type
	 *                         ; case-insensitive
	 *     disp-ext-type       = token
	 *
	 *     disposition-parm    = filename-parm | disp-ext-parm
	 *
	 *     filename-parm       = "filename" "=" value
	 *                         | "filename*" "=" ext-value
	 *
	 *     disp-ext-parm       = token "=" value
	 *                         | ext-token "=" ext-value
	 *     ext-token           = <the characters in token, followed by "*">
	 *
	 *
	 * @link https://tools.ietf.org/html/rfc2388
	 * @link https://tools.ietf.org/html/rfc6266
	 *
	 * @param string[] $disposition_header List of Content-Disposition header values.
	 * @return string|null Filename if available, or null if not found.
	 */
	public static function get_filename_from_disposition( $disposition_header ) {
		// Get the filename.
		$filename = null;

		foreach ( $disposition_header as $value ) {
			$value = trim( $value );

			if ( strpos( $value, ';' ) === false ) {
				continue;
			}

			list( $type, $attr_parts ) = explode( ';', $value, 2 );

			$attr_parts = explode( ';', $attr_parts );
			$attributes = array();

			foreach ( $attr_parts as $part ) {
				if ( strpos( $part, '=' ) === false ) {
					continue;
				}

				list( $key, $value ) = explode( '=', $part, 2 );

				$attributes[ trim( $key ) ] = trim( $value );
			}

			if ( empty( $attributes['filename'] ) ) {
				continue;
			}

			$filename = trim( $attributes['filename'] );

			// Unquote quoted filename, but after trimming.
			if ( substr( $filename, 0, 1 ) === '"' && substr( $filename, -1, 1 ) === '"' ) {
				$filename = substr( $filename, 1, -1 );
			}
		}

		return $filename;
	}

	/**
	 * Retrieves the query params for collections of attachments.
	 *
	 *
	 * @return array Query parameters for the attachment collection as an array.
	 */
	public function get_collection_params() {
		$params                            = parent::get_collection_params();
		$params['status']['default']       = 'inherit';
		$params['status']['items']['enum'] = array( 'inherit', 'private', 'trash' );
		$media_types                       = $this->get_media_types();

		$params['media_type'] = array(
			'default'     => null,
			'description' => __( '将结果集限制为某一媒体类型的附件。' ),
			'type'        => 'string',
			'enum'        => array_keys( $media_types ),
		);

		$params['mime_type'] = array(
			'default'     => null,
			'description' => __( '将结果集限制为某一MIME类型的附件。' ),
			'type'        => 'string',
		);

		return $params;
	}

	/**
	 * Handles an upload via multipart/form-data ($_FILES).
	 *
	 *
	 * @param array $files   Data from the `$_FILES` superglobal.
	 * @param array $headers HTTP headers from the request.
	 * @return array|GC_Error Data from gc_handle_upload().
	 */
	protected function upload_from_file( $files, $headers ) {
		if ( empty( $files ) ) {
			return new GC_Error(
				'rest_upload_no_data',
				__( '未提供数据。' ),
				array( 'status' => 400 )
			);
		}

		// Verify hash, if given.
		if ( ! empty( $headers['content_md5'] ) ) {
			$content_md5 = array_shift( $headers['content_md5'] );
			$expected    = trim( $content_md5 );
			$actual      = md5_file( $files['file']['tmp_name'] );

			if ( $expected !== $actual ) {
				return new GC_Error(
					'rest_upload_hash_mismatch',
					__( '内容哈希与期望值不匹配。' ),
					array( 'status' => 412 )
				);
			}
		}

		// Pass off to GC to handle the actual upload.
		$overrides = array(
			'test_form' => false,
		);

		// Bypasses is_uploaded_file() when running unit tests.
		if ( defined( 'DIR_TESTDATA' ) && DIR_TESTDATA ) {
			$overrides['action'] = 'gc_handle_mock_upload';
		}

		$size_check = self::check_upload_size( $files['file'] );
		if ( is_gc_error( $size_check ) ) {
			return $size_check;
		}

		// Include filesystem functions to get access to gc_handle_upload().
		require_once ABSPATH . 'gc-admin/includes/file.php';

		$file = gc_handle_upload( $files['file'], $overrides );

		if ( isset( $file['error'] ) ) {
			return new GC_Error(
				'rest_upload_unknown_error',
				$file['error'],
				array( 'status' => 500 )
			);
		}

		return $file;
	}

	/**
	 * Retrieves the supported media types.
	 *
	 * Media types are considered the MIME type category.
	 *
	 *
	 * @return array Array of supported media types.
	 */
	protected function get_media_types() {
		$media_types = array();

		foreach ( get_allowed_mime_types() as $mime_type ) {
			$parts = explode( '/', $mime_type );

			if ( ! isset( $media_types[ $parts[0] ] ) ) {
				$media_types[ $parts[0] ] = array();
			}

			$media_types[ $parts[0] ][] = $mime_type;
		}

		return $media_types;
	}

	/**
	 * Determine if uploaded file exceeds space quota on multisite.
	 *
	 * Replicates check_upload_size().
	 *
	 *
	 * @param array $file $_FILES array for a given file.
	 * @return true|GC_Error True if can upload, error for errors.
	 */
	protected function check_upload_size( $file ) {
		if ( ! is_multisite() ) {
			return true;
		}

		if ( get_site_option( 'upload_space_check_disabled' ) ) {
			return true;
		}

		$space_left = get_upload_space_available();

		$file_size = filesize( $file['tmp_name'] );

		if ( $space_left < $file_size ) {
			return new GC_Error(
				'rest_upload_limited_space',
				/* translators: %s: Required disk space in kilobytes. */
				sprintf( __( '上传空间不足，需要%s KB。' ), number_format( ( $file_size - $space_left ) / KB_IN_BYTES ) ),
				array( 'status' => 400 )
			);
		}

		if ( $file_size > ( KB_IN_BYTES * get_site_option( 'fileupload_maxk', 1500 ) ) ) {
			return new GC_Error(
				'rest_upload_file_too_big',
				/* translators: %s: Maximum allowed file size in kilobytes. */
				sprintf( __( '此文件太大，文件必须小于%s KB。' ), get_site_option( 'fileupload_maxk', 1500 ) ),
				array( 'status' => 400 )
			);
		}

		// Include multisite admin functions to get access to upload_is_user_over_quota().
		require_once ABSPATH . 'gc-admin/includes/ms.php';

		if ( upload_is_user_over_quota( false ) ) {
			return new GC_Error(
				'rest_upload_user_quota_exceeded',
				__( '您已经用尽了您的空间配额，请在上传前删除一些文件。' ),
				array( 'status' => 400 )
			);
		}

		return true;
	}

	/**
	 * Gets the request args for the edit item route.
	 *
	 *
	 * @return array
	 */
	protected function get_edit_media_item_args() {
		return array(
			'src'       => array(
				'description' => __( '已经编辑的图片文件 的URL。' ),
				'type'        => 'string',
				'format'      => 'uri',
				'required'    => true,
			),
			'modifiers' => array(
				'description' => __( '图片编辑数组。' ),
				'type'        => 'array',
				'minItems'    => 1,
				'items'       => array(
					'description' => __( '图片编辑' ),
					'type'        => 'object',
					'required'    => array(
						'type',
						'args',
					),
					'oneOf'       => array(
						array(
							'title'      => __( '旋转' ),
							'properties' => array(
								'type' => array(
									'description' => __( '旋转类型。' ),
									'type'        => 'string',
									'enum'        => array( 'rotate' ),
								),
								'args' => array(
									'description' => __( '旋转参数。' ),
									'type'        => 'object',
									'required'    => array(
										'angle',
									),
									'properties'  => array(
										'angle' => array(
											'description' => __( '顺时针旋转的角度。' ),
											'type'        => 'number',
										),
									),
								),
							),
						),
						array(
							'title'      => __( '裁剪' ),
							'properties' => array(
								'type' => array(
									'description' => __( '裁剪方式。' ),
									'type'        => 'string',
									'enum'        => array( 'crop' ),
								),
								'args' => array(
									'description' => __( '裁剪参数。' ),
									'type'        => 'object',
									'required'    => array(
										'left',
										'top',
										'width',
										'height',
									),
									'properties'  => array(
										'left'   => array(
											'description' => __( '从左边开始裁剪的水平位置以占用图片宽度的百分比计算。' ),
											'type'        => 'number',
										),
										'top'    => array(
											'description' => __( '从顶部开始裁剪的垂直位置以占用图片高度的百分比计算。' ),
											'type'        => 'number',
										),
										'width'  => array(
											'description' => __( '裁剪宽度以占用图片宽度的百分比计算。' ),
											'type'        => 'number',
										),
										'height' => array(
											'description' => __( '裁剪的高度以占用图片高度的百分比计算。' ),
											'type'        => 'number',
										),
									),
								),
							),
						),
					),
				),
			),
			'rotation'  => array(
				'description'      => __( '顺时针旋转的角度量。已弃用：请改为使用`modifiers`参数。' ),
				'type'             => 'integer',
				'minimum'          => 0,
				'exclusiveMinimum' => true,
				'maximum'          => 360,
				'exclusiveMaximum' => true,
			),
			'x'         => array(
				'description' => __( '从 X 轴开始裁剪的位置以占用图片的百分比计算。已弃用。使用`modifiers`代替。' ),
				'type'        => 'number',
				'minimum'     => 0,
				'maximum'     => 100,
			),
			'y'         => array(
				'description' => __( '从 Y 轴开始裁剪的位置以占用图片的百分比计算。已弃用。使用`modifiers`代替。' ),
				'type'        => 'number',
				'minimum'     => 0,
				'maximum'     => 100,
			),
			'width'     => array(
				'description' => __( '裁剪宽度以图片所占用的百分比计算。已弃用。使用`modifiers`代替。' ),
				'type'        => 'number',
				'minimum'     => 0,
				'maximum'     => 100,
			),
			'height'    => array(
				'description' => __( '裁剪高度以图片所站用的百分比计算。已弃用。使用`modifiers`代替。' ),
				'type'        => 'number',
				'minimum'     => 0,
				'maximum'     => 100,
			),
		);
	}

}
