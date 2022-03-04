<?php
/**
 * GeChiUI Administration Privacy Tools API.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Resend an existing request and return the result.
 *
 *
 * @access private
 *
 * @param int $request_id Request ID.
 * @return true|GC_Error Returns true if sending the email was successful, or a GC_Error object.
 */
function _gc_privacy_resend_request( $request_id ) {
	$request_id = absint( $request_id );
	$request    = get_post( $request_id );

	if ( ! $request || 'user_request' !== $request->post_type ) {
		return new GC_Error( 'privacy_request_error', __( '无效的个人数据请求。' ) );
	}

	$result = gc_send_user_request( $request_id );

	if ( is_gc_error( $result ) ) {
		return $result;
	} elseif ( ! $result ) {
		return new GC_Error( 'privacy_request_error', __( '无法为个人数据请求发起确认。' ) );
	}

	return true;
}

/**
 * Marks a request as completed by the admin and logs the current timestamp.
 *
 *
 * @access private
 *
 * @param int $request_id Request ID.
 * @return int|GC_Error Request ID on success, or a GC_Error on failure.
 */
function _gc_privacy_completed_request( $request_id ) {
	// Get the request.
	$request_id = absint( $request_id );
	$request    = gc_get_user_request( $request_id );

	if ( ! $request ) {
		return new GC_Error( 'privacy_request_error', __( '无效的个人数据请求。' ) );
	}

	update_post_meta( $request_id, '_gc_user_request_completed_timestamp', time() );

	$result = gc_update_post(
		array(
			'ID'          => $request_id,
			'post_status' => 'request-completed',
		)
	);

	return $result;
}

/**
 * Handle list table actions.
 *
 *
 * @access private
 */
function _gc_personal_data_handle_actions() {
	if ( isset( $_POST['privacy_action_email_retry'] ) ) {
		check_admin_referer( 'bulk-privacy_requests' );

		$request_id = absint( current( array_keys( (array) gc_unslash( $_POST['privacy_action_email_retry'] ) ) ) );
		$result     = _gc_privacy_resend_request( $request_id );

		if ( is_gc_error( $result ) ) {
			add_settings_error(
				'privacy_action_email_retry',
				'privacy_action_email_retry',
				$result->get_error_message(),
				'error'
			);
		} else {
			add_settings_error(
				'privacy_action_email_retry',
				'privacy_action_email_retry',
				__( '确认请求重新发送成功。' ),
				'success'
			);
		}
	} elseif ( isset( $_POST['action'] ) ) {
		$action = ! empty( $_POST['action'] ) ? sanitize_key( gc_unslash( $_POST['action'] ) ) : '';

		switch ( $action ) {
			case 'add_export_personal_data_request':
			case 'add_remove_personal_data_request':
				check_admin_referer( 'personal-data-request' );

				if ( ! isset( $_POST['type_of_action'], $_POST['username_or_email_for_privacy_request'] ) ) {
					add_settings_error(
						'action_type',
						'action_type',
						__( '无效的个人数据操作。' ),
						'error'
					);
				}
				$action_type               = sanitize_text_field( gc_unslash( $_POST['type_of_action'] ) );
				$username_or_email_address = sanitize_text_field( gc_unslash( $_POST['username_or_email_for_privacy_request'] ) );
				$email_address             = '';
				$status                    = 'pending';

				if ( ! isset( $_POST['send_confirmation_email'] ) ) {
					$status = 'confirmed';
				}

				if ( ! in_array( $action_type, _gc_privacy_action_request_types(), true ) ) {
					add_settings_error(
						'action_type',
						'action_type',
						__( '无效的个人数据操作。' ),
						'error'
					);
				}

				if ( ! is_email( $username_or_email_address ) ) {
					$user = get_user_by( 'login', $username_or_email_address );
					if ( ! $user instanceof GC_User ) {
						add_settings_error(
							'username_or_email_for_privacy_request',
							'username_or_email_for_privacy_request',
							__( '无法添加该请求，您必须提供有效的电子邮箱或用户名。' ),
							'error'
						);
					} else {
						$email_address = $user->user_email;
					}
				} else {
					$email_address = $username_or_email_address;
				}

				if ( empty( $email_address ) ) {
					break;
				}

				$request_id = gc_create_user_request( $email_address, $action_type, array(), $status );
				$message    = '';

				if ( is_gc_error( $request_id ) ) {
					$message = $request_id->get_error_message();
				} elseif ( ! $request_id ) {
					$message = __( '无法发起确认请求。' );
				}

				if ( $message ) {
					add_settings_error(
						'username_or_email_for_privacy_request',
						'username_or_email_for_privacy_request',
						$message,
						'error'
					);
					break;
				}

				if ( 'pending' === $status ) {
					gc_send_user_request( $request_id );

					$message = __( '确认请求发起成功。' );
				} elseif ( 'confirmed' === $status ) {
					$message = __( '已成功添加请求。' );
				}

				if ( $message ) {
					add_settings_error(
						'username_or_email_for_privacy_request',
						'username_or_email_for_privacy_request',
						$message,
						'success'
					);
					break;
				}
		}
	}
}

/**
 * Cleans up failed and expired requests before displaying the list table.
 *
 *
 * @access private
 */
function _gc_personal_data_cleanup_requests() {
	/** This filter is documented in gc-includes/user.php */
	$expires = (int) apply_filters( 'user_request_key_expiration', DAY_IN_SECONDS );

	$requests_query = new GC_Query(
		array(
			'post_type'      => 'user_request',
			'posts_per_page' => -1,
			'post_status'    => 'request-pending',
			'fields'         => 'ids',
			'date_query'     => array(
				array(
					'column' => 'post_modified_gmt',
					'before' => $expires . ' seconds ago',
				),
			),
		)
	);

	$request_ids = $requests_query->posts;

	foreach ( $request_ids as $request_id ) {
		gc_update_post(
			array(
				'ID'            => $request_id,
				'post_status'   => 'request-failed',
				'post_password' => '',
			)
		);
	}
}

/**
 * Generate a single group for the personal data export report.
 *
 *
 *
 *
 * @param array  $group_data {
 *     The group data to render.
 *
 *     @type string $group_label  The user-facing heading for the group, e.g. 'Comments'.
 *     @type array  $items        {
 *         An array of group items.
 *
 *         @type array  $group_item_data  {
 *             An array of name-value pairs for the item.
 *
 *             @type string $name   The user-facing name of an item name-value pair, e.g. 'IP Address'.
 *             @type string $value  The user-facing value of an item data pair, e.g. '50.60.70.0'.
 *         }
 *     }
 * }
 * @param string $group_id     The group identifier.
 * @param int    $groups_count The number of all groups
 * @return string The HTML for this group and its items.
 */
function gc_privacy_generate_personal_data_export_group_html( $group_data, $group_id = '', $groups_count = 1 ) {
	$group_id_attr = sanitize_title_with_dashes( $group_data['group_label'] . '-' . $group_id );

	$group_html  = '<h2 id="' . esc_attr( $group_id_attr ) . '">';
	$group_html .= esc_html( $group_data['group_label'] );

	$items_count = count( (array) $group_data['items'] );
	if ( $items_count > 1 ) {
		$group_html .= sprintf( ' <span class="count">(%d)</span>', $items_count );
	}

	$group_html .= '</h2>';

	if ( ! empty( $group_data['group_description'] ) ) {
		$group_html .= '<p>' . esc_html( $group_data['group_description'] ) . '</p>';
	}

	$group_html .= '<div>';

	foreach ( (array) $group_data['items'] as $group_item_id => $group_item_data ) {
		$group_html .= '<table>';
		$group_html .= '<tbody>';

		foreach ( (array) $group_item_data as $group_item_datum ) {
			$value = $group_item_datum['value'];
			// If it looks like a link, make it a link.
			if ( false === strpos( $value, ' ' ) && ( 0 === strpos( $value, 'http://' ) || 0 === strpos( $value, 'https://' ) ) ) {
				$value = '<a href="' . esc_url( $value ) . '">' . esc_html( $value ) . '</a>';
			}

			$group_html .= '<tr>';
			$group_html .= '<th>' . esc_html( $group_item_datum['name'] ) . '</th>';
			$group_html .= '<td>' . gc_kses( $value, 'personal_data_export' ) . '</td>';
			$group_html .= '</tr>';
		}

		$group_html .= '</tbody>';
		$group_html .= '</table>';
	}

	if ( $groups_count > 1 ) {
		$group_html .= '<div class="return-to-top">';
		$group_html .= '<a href="#top"><span aria-hidden="true">&uarr; </span> ' . esc_html__( '返回顶部' ) . '</a>';
		$group_html .= '</div>';
	}

	$group_html .= '</div>';

	return $group_html;
}

/**
 * Generate the personal data export file.
 *
 *
 *
 * @param int $request_id The export request ID.
 */
function gc_privacy_generate_personal_data_export_file( $request_id ) {
	if ( ! class_exists( 'ZipArchive' ) ) {
		gc_send_json_error( __( '无法生成个人数据导出文件。ZipArchive不可用。' ) );
	}

	// Get the request.
	$request = gc_get_user_request( $request_id );

	if ( ! $request || 'export_personal_data' !== $request->action_name ) {
		gc_send_json_error( __( '生成个人数据导出文件时遇到了无效的请求ID。' ) );
	}

	$email_address = $request->email;

	if ( ! is_email( $email_address ) ) {
		gc_send_json_error( __( '生成个人数据导出文件时的电子邮箱无效。' ) );
	}

	// Create the exports folder if needed.
	$exports_dir = gc_privacy_exports_dir();
	$exports_url = gc_privacy_exports_url();

	if ( ! gc_mkdir_p( $exports_dir ) ) {
		gc_send_json_error( __( '无法创建个人数据导出目录。' ) );
	}

	// Protect export folder from browsing.
	$index_pathname = $exports_dir . 'index.php';
	if ( ! file_exists( $index_pathname ) ) {
		$file = fopen( $index_pathname, 'w' );
		if ( false === $file ) {
			gc_send_json_error( __( '无法保护个人数据导出目录不被浏览。' ) );
		}
		fwrite( $file, "<?php\n// Silence is golden.\n" );
		fclose( $file );
	}

	$obscura              = gc_generate_password( 32, false, false );
	$file_basename        = 'gc-personal-data-file-' . $obscura;
	$html_report_filename = gc_unique_filename( $exports_dir, $file_basename . '.html' );
	$html_report_pathname = gc_normalize_path( $exports_dir . $html_report_filename );
	$json_report_filename = $file_basename . '.json';
	$json_report_pathname = gc_normalize_path( $exports_dir . $json_report_filename );

	/*
	 * Gather general data needed.
	 */

	// Title.
	$title = sprintf(
		/* translators: %s: User's email address. */
		__( '%s的个人数据导出' ),
		$email_address
	);

	// First, build an "About" group on the fly for this report.
	$about_group = array(
		/* translators: Header for the About section in a personal data export. */
		'group_label'       => _x( '关于', 'personal data group label' ),
		/* translators: Description for the About section in a personal data export. */
		'group_description' => _x( '导出报告总览。', 'personal data group description' ),
		'items'             => array(
			'about-1' => array(
				array(
					'name'  => _x( '报告生成者', 'email address' ),
					'value' => $email_address,
				),
				array(
					'name'  => _x( '站点', 'website name' ),
					'value' => get_bloginfo( 'name' ),
				),
				array(
					'name'  => _x( '网站URL', 'website URL' ),
					'value' => get_bloginfo( 'url' ),
				),
				array(
					'name'  => _x( '于', 'date/time' ),
					'value' => current_time( 'mysql' ),
				),
			),
		),
	);

	// And now, all the Groups.
	$groups = get_post_meta( $request_id, '_export_data_grouped', true );
	if ( is_array( $groups ) ) {
		// Merge in the special "About" group.
		$groups       = array_merge( array( 'about' => $about_group ), $groups );
		$groups_count = count( $groups );
	} else {
		if ( false !== $groups ) {
			_doing_it_wrong(
				__FUNCTION__,
				/* translators: %s: Post meta key. */
				sprintf( __( '%s文章元必须是数组。' ), '<code>_export_data_grouped</code>' ),
				'5.8.0'
			);
		}

		$groups       = null;
		$groups_count = 0;
	}

	// Convert the groups to JSON format.
	$groups_json = gc_json_encode( $groups );

	if ( false === $groups_json ) {
		$error_message = sprintf(
			/* translators: %s: Error message. */
			__( '无法对导出的个人数据进行编码。错误：%s' ),
			json_last_error_msg()
		);

		gc_send_json_error( $error_message );
	}

	/*
	 * Handle the JSON export.
	 */
	$file = fopen( $json_report_pathname, 'w' );

	if ( false === $file ) {
		gc_send_json_error( __( '无法打开个人数据导出文件（JSON报告）进行写入。' ) );
	}

	fwrite( $file, '{' );
	fwrite( $file, '"' . $title . '":' );
	fwrite( $file, $groups_json );
	fwrite( $file, '}' );
	fclose( $file );

	/*
	 * Handle the HTML export.
	 */
	$file = fopen( $html_report_pathname, 'w' );

	if ( false === $file ) {
		gc_send_json_error( __( '无法打开个人数据导出文件（HTML报告）进行写入。' ) );
	}

	fwrite( $file, "<!DOCTYPE html>\n" );
	fwrite( $file, "<html>\n" );
	fwrite( $file, "<head>\n" );
	fwrite( $file, "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />\n" );
	fwrite( $file, "<style type='text/css'>" );
	fwrite( $file, 'body { color: black; font-family: Arial, sans-serif; font-size: 11pt; margin: 15px auto; width: 860px; }' );
	fwrite( $file, 'table { background: #f0f0f0; border: 1px solid #ddd; margin-bottom: 20px; width: 100%; }' );
	fwrite( $file, 'th { padding: 5px; text-align: left; width: 20%; }' );
	fwrite( $file, 'td { padding: 5px; }' );
	fwrite( $file, 'tr:nth-child(odd) { background-color: #fafafa; }' );
	fwrite( $file, '.return-to-top { text-align: right; }' );
	fwrite( $file, '</style>' );
	fwrite( $file, '<title>' );
	fwrite( $file, esc_html( $title ) );
	fwrite( $file, '</title>' );
	fwrite( $file, "</head>\n" );
	fwrite( $file, "<body>\n" );
	fwrite( $file, '<h1 id="top">' . esc_html__( '个人数据导出' ) . '</h1>' );

	// Create TOC.
	if ( $groups_count > 1 ) {
		fwrite( $file, '<div id="table_of_contents">' );
		fwrite( $file, '<h2>' . esc_html__( '目录' ) . '</h2>' );
		fwrite( $file, '<ul>' );
		foreach ( (array) $groups as $group_id => $group_data ) {
			$group_label       = esc_html( $group_data['group_label'] );
			$group_id_attr     = sanitize_title_with_dashes( $group_data['group_label'] . '-' . $group_id );
			$group_items_count = count( (array) $group_data['items'] );
			if ( $group_items_count > 1 ) {
				$group_label .= sprintf( ' <span class="count">(%d)</span>', $group_items_count );
			}
			fwrite( $file, '<li>' );
			fwrite( $file, '<a href="#' . esc_attr( $group_id_attr ) . '">' . $group_label . '</a>' );
			fwrite( $file, '</li>' );
		}
		fwrite( $file, '</ul>' );
		fwrite( $file, '</div>' );
	}

	// Now, iterate over every group in $groups and have the formatter render it in HTML.
	foreach ( (array) $groups as $group_id => $group_data ) {
		fwrite( $file, gc_privacy_generate_personal_data_export_group_html( $group_data, $group_id, $groups_count ) );
	}

	fwrite( $file, "</body>\n" );
	fwrite( $file, "</html>\n" );
	fclose( $file );

	/*
	 * Now, generate the ZIP.
	 *
	 * If an archive has already been generated, then remove it and reuse the filename,
	 * to avoid breaking any URLs that may have been previously sent via email.
	 */
	$error = false;

	// This meta value is used from version 5.5.
	$archive_filename = get_post_meta( $request_id, '_export_file_name', true );

	// This one stored an absolute path and is used for backward compatibility.
	$archive_pathname = get_post_meta( $request_id, '_export_file_path', true );

	// If a filename meta exists, use it.
	if ( ! empty( $archive_filename ) ) {
		$archive_pathname = $exports_dir . $archive_filename;
	} elseif ( ! empty( $archive_pathname ) ) {
		// If a full path meta exists, use it and create the new meta value.
		$archive_filename = basename( $archive_pathname );

		update_post_meta( $request_id, '_export_file_name', $archive_filename );

		// Remove the back-compat meta values.
		delete_post_meta( $request_id, '_export_file_url' );
		delete_post_meta( $request_id, '_export_file_path' );
	} else {
		// If there's no filename or full path stored, create a new file.
		$archive_filename = $file_basename . '.zip';
		$archive_pathname = $exports_dir . $archive_filename;

		update_post_meta( $request_id, '_export_file_name', $archive_filename );
	}

	$archive_url = $exports_url . $archive_filename;

	if ( ! empty( $archive_pathname ) && file_exists( $archive_pathname ) ) {
		gc_delete_file( $archive_pathname );
	}

	$zip = new ZipArchive;
	if ( true === $zip->open( $archive_pathname, ZipArchive::CREATE ) ) {
		if ( ! $zip->addFile( $json_report_pathname, 'export.json' ) ) {
			$error = __( '无法存档该个人数据导出文件（JSON格式）。' );
		}

		if ( ! $zip->addFile( $html_report_pathname, 'index.html' ) ) {
			$error = __( '无法存档该个人数据导出文件（HTML格式）。' );
		}

		$zip->close();

		if ( ! $error ) {
			/**
			 * Fires right after all personal data has been written to the export file.
			 *
		
		
			 *
			 * @param string $archive_pathname     The full path to the export file on the filesystem.
			 * @param string $archive_url          The URL of the archive file.
			 * @param string $html_report_pathname The full path to the HTML personal data report on the filesystem.
			 * @param int    $request_id           The export request ID.
			 * @param string $json_report_pathname The full path to the JSON personal data report on the filesystem.
			 */
			do_action( 'gc_privacy_personal_data_export_file_created', $archive_pathname, $archive_url, $html_report_pathname, $request_id, $json_report_pathname );
		}
	} else {
		$error = __( '无法打开个人数据导出文件（存档）进行写入。' );
	}

	// Remove the JSON file.
	unlink( $json_report_pathname );

	// Remove the HTML file.
	unlink( $html_report_pathname );

	if ( $error ) {
		gc_send_json_error( $error );
	}
}

/**
 * Send an email to the user with a link to the personal data export file
 *
 *
 *
 * @param int $request_id The request ID for this personal data export.
 * @return true|GC_Error True on success or `GC_Error` on failure.
 */
function gc_privacy_send_personal_data_export_email( $request_id ) {
	// Get the request.
	$request = gc_get_user_request( $request_id );

	if ( ! $request || 'export_personal_data' !== $request->action_name ) {
		return new GC_Error( 'invalid_request', __( '在发送个人数据导出邮件时遇到了无效的请求ID。' ) );
	}

	// Localize message content for user; fallback to site default for visitors.
	if ( ! empty( $request->user_id ) ) {
		$locale = get_user_locale( $request->user_id );
	} else {
		$locale = get_locale();
	}

	$switched_locale = switch_to_locale( $locale );

	/** This filter is documented in gc-includes/functions.php */
	$expiration      = apply_filters( 'gc_privacy_export_expiration', 3 * DAY_IN_SECONDS );
	$expiration_date = date_i18n( get_option( 'date_format' ), time() + $expiration );

	$exports_url      = gc_privacy_exports_url();
	$export_file_name = get_post_meta( $request_id, '_export_file_name', true );
	$export_file_url  = $exports_url . $export_file_name;

	$site_name = gc_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	$site_url  = home_url();

	/**
	 * Filters the recipient of the personal data export email notification.
	 * Should be used with great caution to avoid sending the data export link to wrong emails.
	 *
	 *
	 * @param string          $request_email The email address of the notification recipient.
	 * @param GC_User_Request $request       The request that is initiating the notification.
	 */
	$request_email = apply_filters( 'gc_privacy_personal_data_email_to', $request->email, $request );

	$email_data = array(
		'request'           => $request,
		'expiration'        => $expiration,
		'expiration_date'   => $expiration_date,
		'message_recipient' => $request_email,
		'export_file_url'   => $export_file_url,
		'sitename'          => $site_name,
		'siteurl'           => $site_url,
	);

	/* translators: Personal data export notification email subject. %s: Site title. */
	$subject = sprintf( __( '[%s] 个人数据导出' ), $site_name );

	/**
	 * Filters the subject of the email sent when an export request is completed.
	 *
	 *
	 * @param string $subject    The email subject.
	 * @param string $sitename   The name of the site.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request           User request object.
	 *     @type int             $expiration        The time in seconds until the export file expires.
	 *     @type string          $expiration_date   The localized date and time when the export file expires.
	 *     @type string          $message_recipient The address that the email will be sent to. Defaults
	 *                                              to the value of `$request->email`, but can be changed
	 *                                              by the `gc_privacy_personal_data_email_to` filter.
	 *     @type string          $export_file_url   The export file URL.
	 *     @type string          $sitename          The site name sending the mail.
	 *     @type string          $siteurl           The site URL sending the mail.
	 * }
	 */
	$subject = apply_filters( 'gc_privacy_personal_data_email_subject', $subject, $site_name, $email_data );

	/* translators: Do not translate EXPIRATION, LINK, SITENAME, SITEURL: those are placeholders. */
	$email_text = __(
		'您好：

您请求的个人数据导出已经完成。您可以通过点击以下链接来下载您的个人信息。
为了隐私和安全，我们会在###EXPIRATION###删除这些文件，
所以请在此日期前下载。

###LINK###

祝好，
###SITENAME###全体成员敬上
###SITEURL###'
	);

	/**
	 * Filters the text of the email sent with a personal data export file.
	 *
	 * The following strings have a special meaning and will get replaced dynamically:
	 * ###EXPIRATION###         The date when the URL will be automatically deleted.
	 * ###LINK###               URL of the personal data export file for the user.
	 * ###SITENAME###           The name of the site.
	 * ###SITEURL###            The URL to the site.
	 *
	 *
	 * @param string $email_text Text in the email.
	 * @param int    $request_id The request ID for this personal data export.
	 * @param array  $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request           User request object.
	 *     @type int             $expiration        The time in seconds until the export file expires.
	 *     @type string          $expiration_date   The localized date and time when the export file expires.
	 *     @type string          $message_recipient The address that the email will be sent to. Defaults
	 *                                              to the value of `$request->email`, but can be changed
	 *                                              by the `gc_privacy_personal_data_email_to` filter.
	 *     @type string          $export_file_url   The export file URL.
	 *     @type string          $sitename          The site name sending the mail.
	 *     @type string          $siteurl           The site URL sending the mail.
	 */
	$content = apply_filters( 'gc_privacy_personal_data_email_content', $email_text, $request_id, $email_data );

	$content = str_replace( '###EXPIRATION###', $expiration_date, $content );
	$content = str_replace( '###LINK###', esc_url_raw( $export_file_url ), $content );
	$content = str_replace( '###EMAIL###', $request_email, $content );
	$content = str_replace( '###SITENAME###', $site_name, $content );
	$content = str_replace( '###SITEURL###', esc_url_raw( $site_url ), $content );

	$headers = '';

	/**
	 * Filters the headers of the email sent with a personal data export file.
	 *
	 *
	 * @param string|array $headers    The email headers.
	 * @param string       $subject    The email subject.
	 * @param string       $content    The email content.
	 * @param int          $request_id The request ID.
	 * @param array        $email_data {
	 *     Data relating to the account action email.
	 *
	 *     @type GC_User_Request $request           User request object.
	 *     @type int             $expiration        The time in seconds until the export file expires.
	 *     @type string          $expiration_date   The localized date and time when the export file expires.
	 *     @type string          $message_recipient The address that the email will be sent to. Defaults
	 *                                              to the value of `$request->email`, but can be changed
	 *                                              by the `gc_privacy_personal_data_email_to` filter.
	 *     @type string          $export_file_url   The export file URL.
	 *     @type string          $sitename          The site name sending the mail.
	 *     @type string          $siteurl           The site URL sending the mail.
	 * }
	 */
	$headers = apply_filters( 'gc_privacy_personal_data_email_headers', $headers, $subject, $content, $request_id, $email_data );

	$mail_success = gc_mail( $request_email, $subject, $content, $headers );

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	if ( ! $mail_success ) {
		return new GC_Error( 'privacy_email_error', __( '无法发送个人数据导出邮件。' ) );
	}

	return true;
}

/**
 * Intercept personal data exporter page Ajax responses in order to assemble the personal data export file.
 *
 *
 *
 * @see 'gc_privacy_personal_data_export_page'
 *
 * @param array  $response        The response from the personal data exporter for the given page.
 * @param int    $exporter_index  The index of the personal data exporter. Begins at 1.
 * @param string $email_address   The email address of the user whose personal data this is.
 * @param int    $page            The page of personal data for this exporter. Begins at 1.
 * @param int    $request_id      The request ID for this personal data export.
 * @param bool   $send_as_email   Whether the final results of the export should be emailed to the user.
 * @param string $exporter_key    The slug (key) of the exporter.
 * @return array The filtered response.
 */
function gc_privacy_process_personal_data_export_page( $response, $exporter_index, $email_address, $page, $request_id, $send_as_email, $exporter_key ) {
	/* Do some simple checks on the shape of the response from the exporter.
	 * If the exporter response is malformed, don't attempt to consume it - let it
	 * pass through to generate a warning to the user by default Ajax processing.
	 */
	if ( ! is_array( $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'done', $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'data', $response ) ) {
		return $response;
	}

	if ( ! is_array( $response['data'] ) ) {
		return $response;
	}

	// Get the request.
	$request = gc_get_user_request( $request_id );

	if ( ! $request || 'export_personal_data' !== $request->action_name ) {
		gc_send_json_error( __( '在合并要导出的个人数据时遇到了无效的请求ID。' ) );
	}

	$export_data = array();

	// First exporter, first page? Reset the report data accumulation array.
	if ( 1 === $exporter_index && 1 === $page ) {
		update_post_meta( $request_id, '_export_data_raw', $export_data );
	} else {
		$accumulated_data = get_post_meta( $request_id, '_export_data_raw', true );

		if ( $accumulated_data ) {
			$export_data = $accumulated_data;
		}
	}

	// Now, merge the data from the exporter response into the data we have accumulated already.
	$export_data = array_merge( $export_data, $response['data'] );
	update_post_meta( $request_id, '_export_data_raw', $export_data );

	// If we are not yet on the last page of the last exporter, return now.
	/** This filter is documented in gc-admin/includes/ajax-actions.php */
	$exporters        = apply_filters( 'gc_privacy_personal_data_exporters', array() );
	$is_last_exporter = count( $exporters ) === $exporter_index;
	$exporter_done    = $response['done'];
	if ( ! $is_last_exporter || ! $exporter_done ) {
		return $response;
	}

	// Last exporter, last page - let's prepare the export file.

	// First we need to re-organize the raw data hierarchically in groups and items.
	$groups = array();
	foreach ( (array) $export_data as $export_datum ) {
		$group_id    = $export_datum['group_id'];
		$group_label = $export_datum['group_label'];

		$group_description = '';
		if ( ! empty( $export_datum['group_description'] ) ) {
			$group_description = $export_datum['group_description'];
		}

		if ( ! array_key_exists( $group_id, $groups ) ) {
			$groups[ $group_id ] = array(
				'group_label'       => $group_label,
				'group_description' => $group_description,
				'items'             => array(),
			);
		}

		$item_id = $export_datum['item_id'];
		if ( ! array_key_exists( $item_id, $groups[ $group_id ]['items'] ) ) {
			$groups[ $group_id ]['items'][ $item_id ] = array();
		}

		$old_item_data                            = $groups[ $group_id ]['items'][ $item_id ];
		$merged_item_data                         = array_merge( $export_datum['data'], $old_item_data );
		$groups[ $group_id ]['items'][ $item_id ] = $merged_item_data;
	}

	// Then save the grouped data into the request.
	delete_post_meta( $request_id, '_export_data_raw' );
	update_post_meta( $request_id, '_export_data_grouped', $groups );

	/**
	 * Generate the export file from the collected, grouped personal data.
	 *
	 *
	 * @param int $request_id The export request ID.
	 */
	do_action( 'gc_privacy_personal_data_export_file', $request_id );

	// Clear the grouped data now that it is no longer needed.
	delete_post_meta( $request_id, '_export_data_grouped' );

	// If the destination is email, send it now.
	if ( $send_as_email ) {
		$mail_success = gc_privacy_send_personal_data_export_email( $request_id );
		if ( is_gc_error( $mail_success ) ) {
			gc_send_json_error( $mail_success->get_error_message() );
		}

		// Update the request to completed state when the export email is sent.
		_gc_privacy_completed_request( $request_id );
	} else {
		// Modify the response to include the URL of the export file so the browser can fetch it.
		$exports_url      = gc_privacy_exports_url();
		$export_file_name = get_post_meta( $request_id, '_export_file_name', true );
		$export_file_url  = $exports_url . $export_file_name;

		if ( ! empty( $export_file_url ) ) {
			$response['url'] = $export_file_url;
		}
	}

	return $response;
}

/**
 * Mark erasure requests as completed after processing is finished.
 *
 * This intercepts the Ajax responses to personal data eraser page requests, and
 * monitors the status of a request. Once all of the processing has finished, the
 * request is marked as completed.
 *
 *
 *
 * @see 'gc_privacy_personal_data_erasure_page'
 *
 * @param array  $response      The response from the personal data eraser for
 *                              the given page.
 * @param int    $eraser_index  The index of the personal data eraser. Begins
 *                              at 1.
 * @param string $email_address The email address of the user whose personal
 *                              data this is.
 * @param int    $page          The page of personal data for this eraser.
 *                              Begins at 1.
 * @param int    $request_id    The request ID for this personal data erasure.
 * @return array The filtered response.
 */
function gc_privacy_process_personal_data_erasure_page( $response, $eraser_index, $email_address, $page, $request_id ) {
	/*
	 * If the eraser response is malformed, don't attempt to consume it; let it
	 * pass through, so that the default Ajax processing will generate a warning
	 * to the user.
	 */
	if ( ! is_array( $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'done', $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'items_removed', $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'items_retained', $response ) ) {
		return $response;
	}

	if ( ! array_key_exists( 'messages', $response ) ) {
		return $response;
	}

	// Get the request.
	$request = gc_get_user_request( $request_id );

	if ( ! $request || 'remove_personal_data' !== $request->action_name ) {
		gc_send_json_error( __( '在处理要抹除的个人数据时遇到了无效的请求ID。' ) );
	}

	/** This filter is documented in gc-admin/includes/ajax-actions.php */
	$erasers        = apply_filters( 'gc_privacy_personal_data_erasers', array() );
	$is_last_eraser = count( $erasers ) === $eraser_index;
	$eraser_done    = $response['done'];

	if ( ! $is_last_eraser || ! $eraser_done ) {
		return $response;
	}

	_gc_privacy_completed_request( $request_id );

	/**
	 * Fires immediately after a personal data erasure request has been marked completed.
	 *
	 *
	 * @param int $request_id The privacy request post ID associated with this request.
	 */
	do_action( 'gc_privacy_personal_data_erased', $request_id );

	return $response;
}
