<?php
/**
 * GC_oEmbed_Controller class, used to provide an oEmbed endpoint.
 *
 * @package GeChiUI
 * @subpackage Embeds
 */

/**
 * oEmbed API endpoint controller.
 *
 * Registers the REST API route and delivers the response data.
 * The output format (XML or JSON) is handled by the REST API.
 *
 */
#[AllowDynamicProperties]
final class GC_oEmbed_Controller {
	/**
	 * Register the oEmbed REST API route.
	 *
	 * @since 4.4.0
	 */
	public function register_routes() {
		/**
		 * Filters the maxwidth oEmbed parameter.
		 *
		 * @since 4.4.0
		 *
		 * @param int $maxwidth Maximum allowed width. Default 600.
		 */
		$maxwidth = apply_filters( 'oembed_default_width', 600 );

		register_rest_route(
			'oembed/1.0',
			'/embed',
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'url'      => array(
							'description' => __( '需要获取oEmbed数据的链接。' ),
							'required'    => true,
							'type'        => 'string',
							'format'      => 'uri',
						),
						'format'   => array(
							'default'           => 'json',
							'sanitize_callback' => 'gc_oembed_ensure_format',
						),
						'maxwidth' => array(
							'default'           => $maxwidth,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);

		register_rest_route(
			'oembed/1.0',
			'/proxy',
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'get_proxy_item' ),
					'permission_callback' => array( $this, 'get_proxy_item_permissions_check' ),
					'args'                => array(
						'url'       => array(
							'description' => __( '需要获取oEmbed数据的链接。' ),
							'required'    => true,
							'type'        => 'string',
							'format'      => 'uri',
						),
						'format'    => array(
							'description' => __( '使用的oEmbed格式。' ),
							'type'        => 'string',
							'default'     => 'json',
							'enum'        => array(
								'json',
								'xml',
							),
						),
						'maxwidth'  => array(
							'description'       => __( '嵌入的元素的最大宽度（像素）。' ),
							'type'              => 'integer',
							'default'           => $maxwidth,
							'sanitize_callback' => 'absint',
						),
						'maxheight' => array(
							'description'       => __( '嵌入的元素的最大高度（像素）。' ),
							'type'              => 'integer',
							'sanitize_callback' => 'absint',
						),
						'discover'  => array(
							'description' => __( '对未经批准的提供者是否进行oEmbed发现请求。' ),
							'type'        => 'boolean',
							'default'     => true,
						),
					),
				),
			)
		);
	}

	/**
	 * Callback for the embed API endpoint.
	 *
	 * Returns the JSON object for the post.
	 *
	 * @since 4.4.0
	 *
	 * @param GC_REST_Request $request Full data about the request.
	 * @return array|GC_Error oEmbed response data or GC_Error on failure.
	 */
	public function get_item( $request ) {
		$post_id = url_to_postid( $request['url'] );

		/**
		 * Filters the determined post ID.
		 *
		 * @since 4.4.0
		 *
		 * @param int    $post_id The post ID.
		 * @param string $url     The requested URL.
		 */
		$post_id = apply_filters( 'oembed_request_post_id', $post_id, $request['url'] );

		$data = get_oembed_response_data( $post_id, $request['maxwidth'] );

		if ( ! $data ) {
			return new GC_Error( 'oembed_invalid_url', get_status_header_desc( 404 ), array( 'status' => 404 ) );
		}

		return $data;
	}

	/**
	 * Checks if current user can make a proxy oEmbed request.
	 *
	 * @since 4.8.0
	 *
	 * @return true|GC_Error True if the request has read access, GC_Error object otherwise.
	 */
	public function get_proxy_item_permissions_check() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new GC_Error( 'rest_forbidden', __( '抱歉，您不能发起使用代理的oEmbed请求。' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Callback for the proxy API endpoint.
	 *
	 * Returns the JSON object for the proxied item.
	 *
	 * @since 4.8.0
	 *
	 * @see GC_oEmbed::get_html()
	 * @global GC_Embed   $gc_embed
	 * @global GC_Scripts $gc_scripts
	 *
	 * @param GC_REST_Request $request Full data about the request.
	 * @return object|GC_Error oEmbed response data or GC_Error on failure.
	 */
	public function get_proxy_item( $request ) {
		global $gc_embed, $gc_scripts;

		$args = $request->get_params();

		// Serve oEmbed data from cache if set.
		unset( $args['_gcnonce'] );
		$cache_key = 'oembed_' . md5( serialize( $args ) );
		$data      = get_transient( $cache_key );
		if ( ! empty( $data ) ) {
			return $data;
		}

		$url = $request['url'];
		unset( $args['url'] );

		// Copy maxwidth/maxheight to width/height since GC_oEmbed::fetch() uses these arg names.
		if ( isset( $args['maxwidth'] ) ) {
			$args['width'] = $args['maxwidth'];
		}
		if ( isset( $args['maxheight'] ) ) {
			$args['height'] = $args['maxheight'];
		}

		// Short-circuit process for URLs belonging to the current site.
		$data = get_oembed_response_data_for_url( $url, $args );

		if ( $data ) {
			return $data;
		}

		$data = _gc_oembed_get_object()->get_data( $url, $args );

		if ( false === $data ) {
			// Try using a classic embed, instead.
			/* @var GC_Embed $gc_embed */
			$html = $gc_embed->get_embed_handler_html( $args, $url );

			if ( $html ) {
				// Check if any scripts were enqueued by the shortcode, and include them in the response.
				$enqueued_scripts = array();

				foreach ( $gc_scripts->queue as $script ) {
					$enqueued_scripts[] = $gc_scripts->registered[ $script ]->src;
				}

				return (object) array(
					'provider_name' => __( '嵌入处理程序' ),
					'html'          => $html,
					'scripts'       => $enqueued_scripts,
				);
			}

			return new GC_Error( 'oembed_invalid_url', get_status_header_desc( 404 ), array( 'status' => 404 ) );
		}

		/** This filter is documented in gc-includes/class-gc-oembed.php */
		$data->html = apply_filters( 'oembed_result', _gc_oembed_get_object()->data2html( (object) $data, $url ), $url, $args );

		/**
		 * Filters the oEmbed TTL value (time to live).
		 *
		 * Similar to the {@see 'oembed_ttl'} filter, but for the REST API
		 * oEmbed proxy endpoint.
		 *
		 * @since 4.8.0
		 *
		 * @param int    $time    Time to live (in seconds).
		 * @param string $url     The attempted embed URL.
		 * @param array  $args    An array of embed request arguments.
		 */
		$ttl = apply_filters( 'rest_oembed_ttl', DAY_IN_SECONDS, $url, $args );

		set_transient( $cache_key, $data, $ttl );

		return $data;
	}
}
