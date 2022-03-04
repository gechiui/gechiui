<?php
/**
 * Multisite administration functions.
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

/**
 * Determine if uploaded file exceeds space quota.
 *
 *
 *
 * @param array $file An element from the `$_FILES` array for a given file.
 * @return array The `$_FILES` array element with 'error' key set if file exceeds quota. 'error' is empty otherwise.
 */
function check_upload_size( $file ) {
	if ( get_site_option( 'upload_space_check_disabled' ) ) {
		return $file;
	}

	if ( $file['error'] > 0 ) { // There's already an error.
		return $file;
	}

	if ( defined( 'GC_IMPORTING' ) ) {
		return $file;
	}

	$space_left = get_upload_space_available();

	$file_size = filesize( $file['tmp_name'] );
	if ( $space_left < $file_size ) {
		/* translators: %s: Required disk space in kilobytes. */
		$file['error'] = sprintf( __( '上传空间不足，需要%s KB。' ), number_format( ( $file_size - $space_left ) / KB_IN_BYTES ) );
	}

	if ( $file_size > ( KB_IN_BYTES * get_site_option( 'fileupload_maxk', 1500 ) ) ) {
		/* translators: %s: Maximum allowed file size in kilobytes. */
		$file['error'] = sprintf( __( '此文件太大，文件必须小于%s KB。' ), get_site_option( 'fileupload_maxk', 1500 ) );
	}

	if ( upload_is_user_over_quota( false ) ) {
		$file['error'] = __( '您已经用尽了您的空间配额，请在上传前删除一些文件。' );
	}

	if ( $file['error'] > 0 && ! isset( $_POST['html-upload'] ) && ! gc_doing_ajax() ) {
		gc_die( $file['error'] . ' <a href="javascript:history.go(-1)">' . __( '返回' ) . '</a>' );
	}

	return $file;
}

/**
 * Delete a site.
 *
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int  $blog_id Site ID.
 * @param bool $drop    True if site's database tables should be dropped. Default false.
 */
function gcmu_delete_blog( $blog_id, $drop = false ) {
	global $gcdb;

	$blog_id = (int) $blog_id;

	$switch = false;
	if ( get_current_blog_id() !== $blog_id ) {
		$switch = true;
		switch_to_blog( $blog_id );
	}

	$blog = get_site( $blog_id );

	$current_network = get_network();

	// If a full blog object is not available, do not destroy anything.
	if ( $drop && ! $blog ) {
		$drop = false;
	}

	// Don't destroy the initial, main, or root blog.
	if ( $drop
		&& ( 1 === $blog_id || is_main_site( $blog_id )
			|| ( $blog->path === $current_network->path && $blog->domain === $current_network->domain ) )
	) {
		$drop = false;
	}

	$upload_path = trim( get_option( 'upload_path' ) );

	// If ms_files_rewriting is enabled and upload_path is empty, gc_upload_dir is not reliable.
	if ( $drop && get_site_option( 'ms_files_rewriting' ) && empty( $upload_path ) ) {
		$drop = false;
	}

	if ( $drop ) {
		gc_delete_site( $blog_id );
	} else {
		/** This action is documented in gc-includes/ms-blogs.php */
		do_action_deprecated( 'delete_blog', array( $blog_id, false ), '5.1.0' );

		$users = get_users(
			array(
				'blog_id' => $blog_id,
				'fields'  => 'ids',
			)
		);

		// Remove users from this blog.
		if ( ! empty( $users ) ) {
			foreach ( $users as $user_id ) {
				remove_user_from_blog( $user_id, $blog_id );
			}
		}

		update_blog_status( $blog_id, 'deleted', 1 );

		/** This action is documented in gc-includes/ms-blogs.php */
		do_action_deprecated( 'deleted_blog', array( $blog_id, false ), '5.1.0' );
	}

	if ( $switch ) {
		restore_current_blog();
	}
}

/**
 * Delete a user from the network and remove from all sites.
 *
 *
 *
 * @todo Merge with gc_delete_user()?
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int $id The user ID.
 * @return bool True if the user was deleted, otherwise false.
 */
function gcmu_delete_user( $id ) {
	global $gcdb;

	if ( ! is_numeric( $id ) ) {
		return false;
	}

	$id   = (int) $id;
	$user = new GC_User( $id );

	if ( ! $user->exists() ) {
		return false;
	}

	// Global super-administrators are protected, and cannot be deleted.
	$_super_admins = get_super_admins();
	if ( in_array( $user->user_login, $_super_admins, true ) ) {
		return false;
	}

	/**
	 * Fires before a user is deleted from the network.
	 *
	 * @since MU
	 *
	 * @param int     $id   ID of the user about to be deleted from the network.
	 * @param GC_User $user GC_User object of the user about to be deleted from the network.
	 */
	do_action( 'gcmu_delete_user', $id, $user );

	$blogs = get_blogs_of_user( $id );

	if ( ! empty( $blogs ) ) {
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog->userblog_id );
			remove_user_from_blog( $id, $blog->userblog_id );

			$post_ids = $gcdb->get_col( $gcdb->prepare( "SELECT ID FROM $gcdb->posts WHERE post_author = %d", $id ) );
			foreach ( (array) $post_ids as $post_id ) {
				gc_delete_post( $post_id );
			}

			// Clean links.
			$link_ids = $gcdb->get_col( $gcdb->prepare( "SELECT link_id FROM $gcdb->links WHERE link_owner = %d", $id ) );

			if ( $link_ids ) {
				foreach ( $link_ids as $link_id ) {
					gc_delete_link( $link_id );
				}
			}

			restore_current_blog();
		}
	}

	$meta = $gcdb->get_col( $gcdb->prepare( "SELECT umeta_id FROM $gcdb->usermeta WHERE user_id = %d", $id ) );
	foreach ( $meta as $mid ) {
		delete_metadata_by_mid( 'user', $mid );
	}

	$gcdb->delete( $gcdb->users, array( 'ID' => $id ) );

	clean_user_cache( $user );

	/** This action is documented in gc-admin/includes/user.php */
	do_action( 'deleted_user', $id, null, $user );

	return true;
}

/**
 * Check whether a site has used its allotted upload space.
 *
 * @since MU
 *
 * @param bool $echo Optional. If $echo is set and the quota is exceeded, a warning message is echoed. Default is true.
 * @return bool True if user is over upload space quota, otherwise false.
 */
function upload_is_user_over_quota( $echo = true ) {
	if ( get_site_option( 'upload_space_check_disabled' ) ) {
		return false;
	}

	$space_allowed = get_space_allowed();
	if ( ! is_numeric( $space_allowed ) ) {
		$space_allowed = 10; // Default space allowed is 10 MB.
	}
	$space_used = get_space_used();

	if ( ( $space_allowed - $space_used ) < 0 ) {
		if ( $echo ) {
			printf(
				/* translators: %s: Allowed space allocation. */
				__( '抱歉，您已经用尽了您的空间配额（%s）。请删除一些文件后再上传更多文件。' ),
				size_format( $space_allowed * MB_IN_BYTES )
			);
		}
		return true;
	} else {
		return false;
	}
}

/**
 * Displays the amount of disk space used by the current site. Not used in core.
 *
 * @since MU
 */
function display_space_usage() {
	$space_allowed = get_space_allowed();
	$space_used    = get_space_used();

	$percent_used = ( $space_used / $space_allowed ) * 100;

	$space = size_format( $space_allowed * MB_IN_BYTES );
	?>
	<strong>
	<?php
		/* translators: Storage space that's been used. 1: Percentage of used space, 2: Total space allowed in megabytes or gigabytes. */
		printf( __( '已用：%2$s中的%1$s%%' ), number_format( $percent_used ), $space );
	?>
	</strong>
	<?php
}

/**
 * Get the remaining upload space for this site.
 *
 * @since MU
 *
 * @param int $size Current max size in bytes
 * @return int Max size in bytes
 */
function fix_import_form_size( $size ) {
	if ( upload_is_user_over_quota( false ) ) {
		return 0;
	}
	$available = get_upload_space_available();
	return min( $size, $available );
}

/**
 * Displays the site upload space quota setting form on the Edit Site Settings screen.
 *
 *
 *
 * @param int $id The ID of the site to display the setting for.
 */
function upload_space_setting( $id ) {
	switch_to_blog( $id );
	$quota = get_option( 'blog_upload_space' );
	restore_current_blog();

	if ( ! $quota ) {
		$quota = '';
	}

	?>
	<tr>
		<th><label for="blog-upload-space-number"><?php _e( '站点更新空间限额' ); ?></label></th>
		<td>
			<input type="number" step="1" min="0" style="width: 100px" name="option[blog_upload_space]" id="blog-upload-space-number" aria-describedby="blog-upload-space-desc" value="<?php echo $quota; ?>" />
			<span id="blog-upload-space-desc"><span class="screen-reader-text"><?php _e( '大小，兆字节' ); ?></span> <?php _e( 'MB（留空则使用站点网络默认值）' ); ?></span>
		</td>
	</tr>
	<?php
}

/**
 * Cleans the user cache for a specific user.
 *
 *
 *
 * @param int $id The user ID.
 * @return int|false The ID of the refreshed user or false if the user does not exist.
 */
function refresh_user_details( $id ) {
	$id = (int) $id;

	$user = get_userdata( $id );
	if ( ! $user ) {
		return false;
	}

	clean_user_cache( $user );

	return $id;
}

/**
 * Returns the language for a language code.
 *
 *
 *
 * @param string $code Optional. The two-letter language code. Default empty.
 * @return string The language corresponding to $code if it exists. If it does not exist,
 *                then the first two letters of $code is returned.
 */
function format_code_lang( $code = '' ) {
	$code       = strtolower( substr( $code, 0, 2 ) );
	$lang_codes = array(
		'aa' => 'Afar',
		'ab' => 'Abkhazian',
		'af' => '南非荷兰语',
		'ak' => 'Akan',
		'sq' => 'Albanian',
		'am' => 'Amharic',
		'ar' => 'Arabic',
		'an' => 'Aragonese',
		'hy' => 'Armenian',
		'as' => 'Assamese',
		'av' => 'Avaric',
		'ae' => 'Avestan',
		'ay' => 'Aymara',
		'az' => 'Azerbaijani',
		'ba' => 'Bashkir',
		'bm' => 'Bambara',
		'eu' => 'Basque',
		'be' => '白俄罗斯语',
		'bn' => 'Bengali',
		'bh' => 'Bihari',
		'bi' => 'Bislama',
		'bs' => 'Bosnian',
		'br' => 'Breton',
		'bg' => '保加利亚语',
		'my' => 'Burmese',
		'ca' => 'Catalan; Valencian',
		'ch' => 'Chamorro',
		'ce' => 'Chechen',
		'zh' => 'Chinese',
		'cu' => 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic',
		'cv' => 'Chuvash',
		'kw' => 'Cornish',
		'co' => 'Corsican',
		'cr' => 'Cree',
		'cs' => 'Czech',
		'da' => 'Danish',
		'dv' => 'Divehi; Dhivehi; Maldivian',
		'nl' => 'Dutch; Flemish',
		'dz' => 'Dzongkha',
		'en' => 'English',
		'eo' => 'Esperanto',
		'et' => 'Estonian',
		'ee' => 'Ewe',
		'fo' => 'Faroese',
		'fj' => 'Fijjian',
		'fi' => 'Finnish',
		'fr' => 'French',
		'fy' => 'Western Frisian',
		'ff' => 'Fulah',
		'ka' => 'Georgian',
		'de' => 'German',
		'gd' => 'Gaelic; Scottish Gaelic',
		'ga' => 'Irish',
		'gl' => 'Galician',
		'gv' => 'Manx',
		'el' => 'Greek, Modern',
		'gn' => 'Guarani',
		'gu' => 'Gujarati',
		'ht' => 'Haitian; Haitian Creole',
		'ha' => 'Hausa',
		'he' => 'Hebrew',
		'hz' => 'Herero',
		'hi' => 'Hindi',
		'ho' => 'Hiri Motu',
		'hu' => '匈牙利语',
		'ig' => 'Igbo',
		'is' => '冰岛语',
		'io' => 'Ido',
		'ii' => 'Sichuan Yi',
		'iu' => 'Inuktitut',
		'ie' => 'Interlingue',
		'ia' => 'Interlingua (International Auxiliary Language Association)',
		'id' => '印度尼西亚语',
		'ik' => 'Inupiaq',
		'it' => 'Italian',
		'jv' => 'Javanese',
		'ja' => 'Japanese',
		'kl' => 'Kalaallisut; Greenlandic',
		'kn' => 'Kannada',
		'ks' => 'Kashmiri',
		'kr' => 'Kanuri',
		'kk' => 'Kazakh',
		'km' => 'Central Khmer',
		'ki' => 'Kikuyu; Gikuyu',
		'rw' => 'Kinyarwanda',
		'ky' => 'Kirghiz; Kyrgyz',
		'kv' => 'Komi',
		'kg' => 'Kongo',
		'ko' => 'Korean',
		'kj' => 'Kuanyama; Kwanyama',
		'ku' => 'Kurdish',
		'lo' => 'Lao',
		'la' => 'Latin',
		'lv' => 'Latvian',
		'li' => 'Limburgan; Limburger; Limburgish',
		'ln' => 'Lingala',
		'lt' => '立陶宛语',
		'lb' => 'Luxembourgish; Letzeburgesch',
		'lu' => 'Luba-Katanga',
		'lg' => 'Ganda',
		'mk' => '马其顿语',
		'mh' => 'Marshallese',
		'ml' => 'Malayalam',
		'mi' => 'Maori',
		'mr' => 'Marathi',
		'ms' => 'Malay',
		'mg' => 'Malagasy',
		'mt' => 'Maltese',
		'mo' => 'Moldavian',
		'mn' => 'Mongolian',
		'na' => 'Nauru',
		'nv' => 'Navajo; Navaho',
		'nr' => 'Ndebele, South; South Ndebele',
		'nd' => 'Ndebele, North; North Ndebele',
		'ng' => 'Ndonga',
		'ne' => 'Nepali',
		'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian',
		'nb' => 'Bokmål, Norwegian, Norwegian Bokmål',
		'no' => '挪威语',
		'ny' => 'Chichewa; Chewa; Nyanja',
		'oc' => 'Occitan, Provençal',
		'oj' => 'Ojibwa',
		'or' => 'Oriya',
		'om' => 'Oromo',
		'os' => 'Ossetian; Ossetic',
		'pa' => 'Panjabi; Punjabi',
		'fa' => 'Persian',
		'pi' => 'Pali',
		'pl' => 'Polish',
		'pt' => '葡萄牙语',
		'ps' => 'Pushto',
		'qu' => 'Quechua',
		'rm' => 'Romansh',
		'ro' => 'Romanian',
		'rn' => 'Rundi',
		'ru' => 'Russian',
		'sg' => 'Sango',
		'sa' => 'Sanskrit',
		'sr' => 'Serbian',
		'hr' => 'Croatian',
		'si' => 'Sinhala; Sinhalese',
		'sk' => 'Slovak',
		'sl' => '斯洛文尼亚语',
		'se' => 'Northern Sami',
		'sm' => 'Samoan',
		'sn' => 'Shona',
		'sd' => 'Sindhi',
		'so' => 'Somali',
		'st' => 'Sotho, Southern',
		'es' => 'Spanish; Castilian',
		'sc' => 'Sardinian',
		'ss' => 'Swati',
		'su' => 'Sundanese',
		'sw' => 'Swahili',
		'sv' => 'Swedish',
		'ty' => 'Tahitian',
		'ta' => 'Tamil',
		'tt' => 'Tatar',
		'te' => 'Telugu',
		'tg' => 'Tajik',
		'tl' => 'Tagalog',
		'th' => 'Thai',
		'bo' => 'Tibetan',
		'ti' => 'Tigrinya',
		'to' => 'Tonga (Tonga Islands)',
		'tn' => 'Tswana',
		'ts' => 'Tsonga',
		'tk' => 'Turkmen',
		'tr' => 'Turkish',
		'tw' => 'Twi',
		'ug' => 'Uighur; Uyghur',
		'uk' => '乌克兰语',
		'ur' => 'Urdu',
		'uz' => 'Uzbek',
		've' => 'Venda',
		'vi' => '越南语',
		'vo' => 'Volapük',
		'cy' => 'Welsh',
		'wa' => 'Walloon',
		'wo' => 'Wolof',
		'xh' => 'Xhosa',
		'yi' => 'Yiddish',
		'yo' => 'Yoruba',
		'za' => 'Zhuang; Chuang',
		'zu' => 'Zulu',
	);

	/**
	 * Filters the language codes.
	 *
	 * @since MU
	 *
	 * @param string[] $lang_codes Array of key/value pairs of language codes where key is the short version.
	 * @param string   $code       A two-letter designation of the language.
	 */
	$lang_codes = apply_filters( 'lang_codes', $lang_codes, $code );
	return strtr( $code, $lang_codes );
}

/**
 * Synchronizes category and post tag slugs when global terms are enabled.
 *
 *
 *
 * @param GC_Term|array $term     The term.
 * @param string        $taxonomy The taxonomy for `$term`. Should be 'category' or 'post_tag', as these are
 *                                the only taxonomies which are processed by this function; anything else
 *                                will be returned untouched.
 * @return GC_Term|array Returns `$term`, after filtering the 'slug' field with `sanitize_title()`
 *                       if `$taxonomy` is 'category' or 'post_tag'.
 */
function sync_category_tag_slugs( $term, $taxonomy ) {
	if ( global_terms_enabled() && ( 'category' === $taxonomy || 'post_tag' === $taxonomy ) ) {
		if ( is_object( $term ) ) {
			$term->slug = sanitize_title( $term->name );
		} else {
			$term['slug'] = sanitize_title( $term['name'] );
		}
	}
	return $term;
}

/**
 * Displays an access denied message when a user tries to view a site's dashboard they
 * do not have access to.
 *
 *
 * @access private
 */
function _access_denied_splash() {
	if ( ! is_user_logged_in() || is_network_admin() ) {
		return;
	}

	$blogs = get_blogs_of_user( get_current_user_id() );

	if ( gc_list_filter( $blogs, array( 'userblog_id' => get_current_blog_id() ) ) ) {
		return;
	}

	$blog_name = get_bloginfo( 'name' );

	if ( empty( $blogs ) ) {
		gc_die(
			sprintf(
				/* translators: 1: Site title. */
				__( '您正尝试访问“%1$s”的仪表盘，但您目前没有该站点的相应权限。如果您确定您具有“%1$s”的仪表盘访问权限，请联系网络管理员。' ),
				$blog_name
			),
			403
		);
	}

	$output = '<p>' . sprintf(
		/* translators: 1: Site title. */
		__( '您正尝试访问“%1$s”的仪表盘，但您目前没有该站点的相应权限。如果您确定您具有“%1$s”的仪表盘访问权限，请联系网络管理员。' ),
		$blog_name
	) . '</p>';
	$output .= '<p>' . __( '如果本来想访问其他页面，但不小心访问到本页面，下方的链接或许能帮到您。' ) . '</p>';

	$output .= '<h3>' . __( '您的站点' ) . '</h3>';
	$output .= '<table>';

	foreach ( $blogs as $blog ) {
		$output .= '<tr>';
		$output .= "<td>{$blog->blogname}</td>";
		$output .= '<td><a href="' . esc_url( get_admin_url( $blog->userblog_id ) ) . '">' . __( '访问“仪表盘”' ) . '</a> | ' .
			'<a href="' . esc_url( get_home_url( $blog->userblog_id ) ) . '">' . __( '查看站点' ) . '</a></td>';
		$output .= '</tr>';
	}

	$output .= '</table>';

	gc_die( $output, 403 );
}

/**
 * Checks if the current user has permissions to import new users.
 *
 *
 *
 * @param string $permission A permission to be checked. Currently not used.
 * @return bool True if the user has proper permissions, false if they do not.
 */
function check_import_new_users( $permission ) {
	if ( ! current_user_can( 'manage_network_users' ) ) {
		return false;
	}

	return true;
}
// See "import_allow_fetch_attachments" and "import_attachment_size_limit" filters too.

/**
 * Generates and displays a drop-down of available languages.
 *
 *
 *
 * @param string[] $lang_files Optional. An array of the language files. Default empty array.
 * @param string   $current    Optional. The current language code. Default empty.
 */
function mu_dropdown_languages( $lang_files = array(), $current = '' ) {
	$flag   = false;
	$output = array();

	foreach ( (array) $lang_files as $val ) {
		$code_lang = basename( $val, '.mo' );

		if ( 'zh_CN' === $code_lang ) { // American English.
			$flag          = true;
			$ae            = __( '中文' );
			$output[ $ae ] = '<option value="' . esc_attr( $code_lang ) . '"' . selected( $current, $code_lang, false ) . '> ' . $ae . '</option>';
		} elseif ( 'en_US' === $code_lang ) { // British English.
			$flag          = true;
			$be            = __( '英文' );
			$output[ $be ] = '<option value="' . esc_attr( $code_lang ) . '"' . selected( $current, $code_lang, false ) . '> ' . $be . '</option>';
		} else {
			$translated            = format_code_lang( $code_lang );
			$output[ $translated ] = '<option value="' . esc_attr( $code_lang ) . '"' . selected( $current, $code_lang, false ) . '> ' . esc_html( $translated ) . '</option>';
		}
	}

	if ( false === $flag ) { // GeChiUI English.
		$output[] = '<option value=""' . selected( $current, '', false ) . '>' . __( '中文' ) . '</option>';
	}

	// Order by name.
	uksort( $output, 'strnatcasecmp' );

	/**
	 * Filters the languages available in the dropdown.
	 *
	 * @since MU
	 *
	 * @param string[] $output     Array of HTML output for the dropdown.
	 * @param string[] $lang_files Array of available language files.
	 * @param string   $current    The current language code.
	 */
	$output = apply_filters( 'mu_dropdown_languages', $output, $lang_files, $current );

	echo implode( "\n\t", $output );
}

/**
 * Displays an admin notice to upgrade all sites after a core upgrade.
 *
 *
 *
 * @global int    $gc_db_version GeChiUI database version.
 * @global string $pagenow
 *
 * @return void|false Void on success. False if the current user is not a super admin.
 */
function site_admin_notice() {
	global $gc_db_version, $pagenow;

	if ( ! current_user_can( 'upgrade_network' ) ) {
		return false;
	}

	if ( 'upgrade.php' === $pagenow ) {
		return;
	}

	if ( (int) get_site_option( 'gcmu_upgrade_site' ) !== $gc_db_version ) {
		echo "<div class='update-nag notice notice-warning inline'>" . sprintf(
			/* translators: %s: URL to Upgrade Network screen. */
			__( '感谢您升级 GeChiUI！请使用<a href="%s">升级站点网络</a>页面来升级您的所有站点。' ),
			esc_url( network_admin_url( 'upgrade.php' ) )
		) . '</div>';
	}
}

/**
 * Avoids a collision between a site slug and a permalink slug.
 *
 * In a subdirectory installation this will make sure that a site and a post do not use the
 * same subdirectory by checking for a site with the same name as a new post.
 *
 *
 *
 * @param array $data    An array of post data.
 * @param array $postarr An array of posts. Not currently used.
 * @return array The new array of post data after checking for collisions.
 */
function avoid_blog_page_permalink_collision( $data, $postarr ) {
	if ( is_subdomain_install() ) {
		return $data;
	}
	if ( 'page' !== $data['post_type'] ) {
		return $data;
	}
	if ( ! isset( $data['post_name'] ) || '' === $data['post_name'] ) {
		return $data;
	}
	if ( ! is_main_site() ) {
		return $data;
	}
	if ( isset( $data['post_parent'] ) && $data['post_parent'] ) {
		return $data;
	}

	$post_name = $data['post_name'];
	$c         = 0;

	while ( $c < 10 && get_id_from_blogname( $post_name ) ) {
		$post_name .= mt_rand( 1, 10 );
		$c++;
	}

	if ( $post_name !== $data['post_name'] ) {
		$data['post_name'] = $post_name;
	}

	return $data;
}

/**
 * Handles the display of choosing a user's primary site.
 *
 * This displays the user's primary site and allows the user to choose
 * which site is primary.
 *
 *
 */
function choose_primary_blog() {
	?>
	<table class="form-table" role="presentation">
	<tr>
	<?php /* translators: My Sites label. */ ?>
		<th scope="row"><label for="primary_blog"><?php _e( '主站点' ); ?></label></th>
		<td>
		<?php
		$all_blogs    = get_blogs_of_user( get_current_user_id() );
		$primary_blog = (int) get_user_meta( get_current_user_id(), 'primary_blog', true );
		if ( count( $all_blogs ) > 1 ) {
			$found = false;
			?>
			<select name="primary_blog" id="primary_blog">
				<?php
				foreach ( (array) $all_blogs as $blog ) {
					if ( $blog->userblog_id === $primary_blog ) {
						$found = true;
					}
					?>
					<option value="<?php echo $blog->userblog_id; ?>"<?php selected( $primary_blog, $blog->userblog_id ); ?>><?php echo esc_url( get_home_url( $blog->userblog_id ) ); ?></option>
					<?php
				}
				?>
			</select>
			<?php
			if ( ! $found ) {
				$blog = reset( $all_blogs );
				update_user_meta( get_current_user_id(), 'primary_blog', $blog->userblog_id );
			}
		} elseif ( 1 === count( $all_blogs ) ) {
			$blog = reset( $all_blogs );
			echo esc_url( get_home_url( $blog->userblog_id ) );
			if ( $blog->userblog_id !== $primary_blog ) { // Set the primary blog again if it's out of sync with blog list.
				update_user_meta( get_current_user_id(), 'primary_blog', $blog->userblog_id );
			}
		} else {
			echo 'N/A';
		}
		?>
		</td>
	</tr>
	</table>
	<?php
}

/**
 * Whether or not we can edit this network from this page.
 *
 * By default editing of network is restricted to the Network Admin for that `$network_id`.
 * This function allows for this to be overridden.
 *
 *
 *
 * @param int $network_id The network ID to check.
 * @return bool True if network can be edited, otherwise false.
 */
function can_edit_network( $network_id ) {
	if ( get_current_network_id() === (int) $network_id ) {
		$result = true;
	} else {
		$result = false;
	}

	/**
	 * Filters whether this network can be edited from this page.
	 *
	 *
	 * @param bool $result     Whether the network can be edited from this page.
	 * @param int  $network_id The network ID to check.
	 */
	return apply_filters( 'can_edit_network', $result, $network_id );
}

/**
 * Thickbox image paths for Network Admin.
 *
 *
 *
 * @access private
 */
function _thickbox_path_admin_subfolder() {
	?>
<script type="text/javascript">
var tb_pathToImage = "<?php echo esc_js( '/assets/vendors/thickbox/loadingAnimation.gif' ); ?>";
</script>
	<?php
}

/**
 * @param array $users
 */
function confirm_delete_users( $users ) {
	$current_user = gc_get_current_user();
	if ( ! is_array( $users ) || empty( $users ) ) {
		return false;
	}
	?>
	<h1><?php esc_html_e( '用户' ); ?></h1>

	<?php if ( 1 === count( $users ) ) : ?>
		<p><?php _e( '您选择从所有站点网络及站点中删除此用户。' ); ?></p>
	<?php else : ?>
		<p><?php _e( '您选择从所有站点网络及站点中删除以下用户。' ); ?></p>
	<?php endif; ?>

	<form action="users.php?action=dodelete" method="post">
	<input type="hidden" name="dodelete" />
	<?php
	gc_nonce_field( 'ms-users-delete' );
	$site_admins = get_super_admins();
	$admin_out   = '<option value="' . esc_attr( $current_user->ID ) . '">' . $current_user->user_login . '</option>';
	?>
	<table class="form-table" role="presentation">
	<?php
	$allusers = (array) $_POST['allusers'];
	foreach ( $allusers as $user_id ) {
		if ( '' !== $user_id && '0' !== $user_id ) {
			$delete_user = get_userdata( $user_id );

			if ( ! current_user_can( 'delete_user', $delete_user->ID ) ) {
				gc_die(
					sprintf(
						/* translators: %s: User login. */
						__( '警告！用户%s不能被删除。' ),
						$delete_user->user_login
					)
				);
			}

			if ( in_array( $delete_user->user_login, $site_admins, true ) ) {
				gc_die(
					sprintf(
						/* translators: %s: User login. */
						__( '警告！无法删除此用户，用户%s是网络管理员。' ),
						'<em>' . $delete_user->user_login . '</em>'
					)
				);
			}
			?>
			<tr>
				<th scope="row"><?php echo $delete_user->user_login; ?>
					<?php echo '<input type="hidden" name="user[]" value="' . esc_attr( $user_id ) . '" />' . "\n"; ?>
				</th>
			<?php
			$blogs = get_blogs_of_user( $user_id, true );

			if ( ! empty( $blogs ) ) {
				?>
				<td><fieldset><p><legend>
				<?php
				printf(
					/* translators: %s: User login. */
					__( '要如何处理%s拥有的内容？' ),
					'<em>' . $delete_user->user_login . '</em>'
				);
				?>
				</legend></p>
				<?php
				foreach ( (array) $blogs as $key => $details ) {
					$blog_users = get_users(
						array(
							'blog_id' => $details->userblog_id,
							'fields'  => array( 'ID', 'user_login' ),
						)
					);

					if ( is_array( $blog_users ) && ! empty( $blog_users ) ) {
						$user_site      = "<a href='" . esc_url( get_home_url( $details->userblog_id ) ) . "'>{$details->blogname}</a>";
						$user_dropdown  = '<label for="reassign_user" class="screen-reader-text">' . __( '选择用户' ) . '</label>';
						$user_dropdown .= "<select name='blog[$user_id][$key]' id='reassign_user'>";
						$user_list      = '';

						foreach ( $blog_users as $user ) {
							if ( ! in_array( (int) $user->ID, $allusers, true ) ) {
								$user_list .= "<option value='{$user->ID}'>{$user->user_login}</option>";
							}
						}

						if ( '' === $user_list ) {
							$user_list = $admin_out;
						}

						$user_dropdown .= $user_list;
						$user_dropdown .= "</select>\n";
						?>
						<ul style="list-style:none;">
							<li>
								<?php
								/* translators: %s: Link to user's site. */
								printf( __( '站点：%s' ), $user_site );
								?>
							</li>
							<li><label><input type="radio" id="delete_option0" name="delete[<?php echo $details->userblog_id . '][' . $delete_user->ID; ?>]" value="delete" checked="checked" />
							<?php _e( '删除所有内容。' ); ?></label></li>
							<li><label><input type="radio" id="delete_option1" name="delete[<?php echo $details->userblog_id . '][' . $delete_user->ID; ?>]" value="reassign" />
							<?php _e( '将这些内容的作者修改为：' ); ?></label>
							<?php echo $user_dropdown; ?></li>
						</ul>
						<?php
					}
				}
				echo '</fieldset></td></tr>';
			} else {
				?>
				<td><p><?php _e( '用户没有任何站点或内容，将被删除。' ); ?></p></td>
			<?php } ?>
			</tr>
			<?php
		}
	}

	?>
	</table>
	<?php
	/** This action is documented in gc-admin/users.php */
	do_action( 'delete_user_form', $current_user, $allusers );

	if ( 1 === count( $users ) ) :
		?>
		<p><?php _e( '在您点击“确认删除”之后，该用户将被永久移除。' ); ?></p>
	<?php else : ?>
		<p><?php _e( '在您点击“确认删除”之后，这些用户将被永久移除。' ); ?></p>
		<?php
	endif;

	submit_button( __( '确认删除' ), 'primary' );
	?>
	</form>
	<?php
	return true;
}

/**
 * Print JavaScript in the header on the Network Settings screen.
 *
 *
 */
function network_settings_add_js() {
	?>
<script type="text/javascript">
jQuery( function($) {
	var languageSelect = $( '#GCLANG' );
	$( 'form' ).on( 'submit', function() {
		// Don't show a spinner for English and installed languages,
		// as there is nothing to download.
		if ( ! languageSelect.find( 'option:selected' ).data( 'installed' ) ) {
			$( '#submit', this ).after( '<span class="spinner language-install-spinner is-active" />' );
		}
	});
} );
</script>
	<?php
}

/**
 * Outputs the HTML for a network's "编辑站点" tabular interface.
 *
 *
 *
 * @global string $pagenow
 *
 * @param array $args {
 *     Optional. Array or string of Query parameters. Default empty array.
 *
 *     @type int    $blog_id  The site ID. Default is the current site.
 *     @type array  $links    The tabs to include with (label|url|cap) keys.
 *     @type string $selected The ID of the selected link.
 * }
 */
function network_edit_site_nav( $args = array() ) {

	/**
	 * Filters the links that appear on site-editing network pages.
	 *
	 * Default links: 'site-info', 'site-users', 'site-themes', and 'site-settings'.
	 *
	 *
	 * @param array $links {
	 *     An array of link data representing individual network admin pages.
	 *
	 *     @type array $link_slug {
	 *         An array of information about the individual link to a page.
	 *
	 *         $type string $label Label to use for the link.
	 *         $type string $url   URL, relative to `network_admin_url()` to use for the link.
	 *         $type string $cap   Capability required to see the link.
	 *     }
	 * }
	 */
	$links = apply_filters(
		'network_edit_site_nav_links',
		array(
			'site-info'     => array(
				'label' => __( '信息' ),
				'url'   => 'site-info.php',
				'cap'   => 'manage_sites',
			),
			'site-users'    => array(
				'label' => __( '用户' ),
				'url'   => 'site-users.php',
				'cap'   => 'manage_sites',
			),
			'site-themes'   => array(
				'label' => __( '主题' ),
				'url'   => 'site-themes.php',
				'cap'   => 'manage_sites',
			),
			'site-settings' => array(
				'label' => __( '设置' ),
				'url'   => 'site-settings.php',
				'cap'   => 'manage_sites',
			),
		)
	);

	// Parse arguments.
	$parsed_args = gc_parse_args(
		$args,
		array(
			'blog_id'  => isset( $_GET['blog_id'] ) ? (int) $_GET['blog_id'] : 0,
			'links'    => $links,
			'selected' => 'site-info',
		)
	);

	// Setup the links array.
	$screen_links = array();

	// Loop through tabs.
	foreach ( $parsed_args['links'] as $link_id => $link ) {

		// Skip link if user can't access.
		if ( ! current_user_can( $link['cap'], $parsed_args['blog_id'] ) ) {
			continue;
		}

		// Link classes.
		$classes = array( 'nav-tab' );

		// Aria-current attribute.
		$aria_current = '';

		// Selected is set by the parent OR assumed by the $pagenow global.
		if ( $parsed_args['selected'] === $link_id || $link['url'] === $GLOBALS['pagenow'] ) {
			$classes[]    = 'nav-tab-active';
			$aria_current = ' aria-current="page"';
		}

		// Escape each class.
		$esc_classes = implode( ' ', $classes );

		// Get the URL for this link.
		$url = add_query_arg( array( 'id' => $parsed_args['blog_id'] ), network_admin_url( $link['url'] ) );

		// Add link to nav links.
		$screen_links[ $link_id ] = '<a href="' . esc_url( $url ) . '" id="' . esc_attr( $link_id ) . '" class="' . $esc_classes . '"' . $aria_current . '>' . esc_html( $link['label'] ) . '</a>';
	}

	// All done!
	echo '<nav class="nav-tab-wrapper gc-clearfix" aria-label="' . esc_attr__( '次要菜单' ) . '">';
	echo implode( '', $screen_links );
	echo '</nav>';
}

/**
 * Returns the arguments for the help tab on the Edit Site screens.
 *
 *
 *
 * @return array Help tab arguments.
 */
function get_site_screen_help_tab_args() {
	return array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . __( '此菜单用来编辑只适用于单个站点的信息，尤其是当该站点的管理区域不可用时。' ) . '</p>' .
			'<p>' . __( '<strong>信息</strong>——您不应经常编辑站点URL，这可能让您的站点停止正常工作。注册日期和最后更新日期在这里显示。站点管理员可以将一个站点标记为已存档、垃圾、已删除或成人内容，也可以将站点从公开列表中移除或禁用站点。' ) . '</p>' .
			'<p>' . __( '<strong>用户</strong>——这里显示该站点所关联的用户。您也可以修改他们的角色、重置密码或将他们从站点移除。从站点中移除用户并不会将用户从站点网络中移除。' ) . '</p>' .
			'<p>' . sprintf(
				/* translators: %s: URL to Network Themes screen. */
				__( '<strong>主题</strong>——这个区域显示了并未在整个站点网络中启用的主题。在此启用主题将使其对本站点可用。这并不会启用主题，但会让主题在本站点的“外观”菜单中显示。要为整个站点网络启用主题，请查看<a href="%s">站点网络主题</a>页面。' ),
				network_admin_url( 'themes.php' )
			) . '</p>' .
			'<p>' . __( '<strong>设置</strong>——这个页面显示了本站点关联的所有设置。其中一些由GeChiUI创建，另一些由您启用的插件创建。有一些字段显示为灰色的“序列化数据”，因为这些字段在数据库中的存储方式特殊，您不能修改这些字段。' ) . '</p>',
	);
}

/**
 * Returns the content for the help sidebar on the Edit Site screens.
 *
 *
 *
 * @return string Help sidebar content.
 */
function get_site_screen_help_sidebar_content() {
	return '<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/network-admin-sites-screen/">站点管理文档</a>' ) . '</p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/forum/issues/multisite/">支持论坛</a>' ) . '</p>';
}
