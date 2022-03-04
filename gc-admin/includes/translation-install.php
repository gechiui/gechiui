<?php
/**
 * GeChiUI Translation Installation Administration API
 *
 * @package GeChiUI
 * @subpackage Administration
 */


/**
 * Retrieve translations from GeChiUI Translation API.
 *
 *
 *
 * @param string       $type Type of translations. Accepts 'plugins', 'themes', 'core'.
 * @param array|object $args Translation API arguments. Optional.
 * @return array|GC_Error On success an associative array of translations, GC_Error on failure.
 */
function translations_api( $type, $args = null ) {
	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	if ( ! in_array( $type, array( 'plugins', 'themes', 'core' ), true ) ) {
		return new GC_Error( 'invalid_type', __( '无效的翻译类型。' ) );
	}

	/**
	 * Allows a plugin to override the www.GeChiUI.com Translation Installation API entirely.
	 *
	 *
	 * @param false|array $result The result array. Default false.
	 * @param string      $type   The type of translations being requested.
	 * @param object      $args   Translation API arguments.
	 */
	$res = apply_filters( 'translations_api', false, $type, $args );

	if ( false === $res ) {
		$url      = 'http://api.gechiui.com/translations/' . $type . '/1.0/';
		$http_url = $url;
		$ssl      = gc_http_supports( array( 'ssl' ) );
		if ( $ssl ) {
			$url = set_url_scheme( $url, 'https' );
		}

		$options = array(
			'timeout' => 3,
			'body'    => array(
				'gc_version' => $gc_version,
				'locale'     => get_locale(),
				'version'    => $args['version'], // Version of plugin, theme or core.
			),
		);

		if ( 'core' !== $type ) {
			$options['body']['slug'] = $args['slug']; // Plugin or theme slug.
		}

		$request = gc_remote_post( $url, $options );

		if ( $ssl && is_gc_error( $request ) ) {
			trigger_error(
				sprintf(
					/* translators: %s: Support forums URL. */
					__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
					__( 'https://www.gechiui.com/support/forums/' )
				) . ' ' . __( '（GeChiUI无法建立到www.GeChiUI.com的安全连接，请联系您的服务器管理员。）' ),
				headers_sent() || GC_DEBUG ? E_USER_WARNING : E_USER_NOTICE
			);

			$request = gc_remote_post( $http_url, $options );
		}

		if ( is_gc_error( $request ) ) {
			$res = new GC_Error(
				'translations_api_failed',
				sprintf(
					/* translators: %s: Support forums URL. */
					__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
					__( 'https://www.gechiui.com/support/forums/' )
				),
				$request->get_error_message()
			);
		} else {
			$res = json_decode( gc_remote_retrieve_body( $request ), true );
			if ( ! is_object( $res ) && ! is_array( $res ) ) {
				$res = new GC_Error(
					'translations_api_failed',
					sprintf(
						/* translators: %s: Support forums URL. */
						__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
						__( 'https://www.gechiui.com/support/forums/' )
					),
					gc_remote_retrieve_body( $request )
				);
			}
		}
	}

	/**
	 * Filters the Translation Installation API response results.
	 *
	 *
	 * @param array|GC_Error $res  Response as an associative array or GC_Error.
	 * @param string         $type The type of translations being requested.
	 * @param object         $args Translation API arguments.
	 */
	return apply_filters( 'translations_api_result', $res, $type, $args );
}

/**
 * Get available translations from the www.GeChiUI.com API.
 *
 *
 *
 * @see translations_api()
 *
 * @return array[] Array of translations, each an array of data, keyed by the language. If the API response results
 *                 in an error, an empty array will be returned.
 */
function gc_get_available_translations() {
	if ( ! gc_installing() ) {
		$translations = get_site_transient( 'available_translations' );
		if ( false !== $translations ) {
			return $translations;
		}
	}

	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	$api = translations_api( 'core', array( 'version' => $gc_version ) );

	if ( is_gc_error( $api ) || empty( $api['translations'] ) ) {
		return array();
	}

	$translations = array();
	// Key the array with the language code for now.
	foreach ( $api['translations'] as $translation ) {
		$translations[ $translation['language'] ] = $translation;
	}

	if ( ! defined( 'GC_INSTALLING' ) ) {
		set_site_transient( 'available_translations', $translations, 3 * HOUR_IN_SECONDS );
	}

	return $translations;
}

/**
 * Output the select form for the language selection on the installation screen.
 *
 *
 *
 * @global string $gc_local_package Locale code of the package.
 *
 * @param array[] $languages Array of available languages (populated via the Translation API).
 */
function gc_install_language_form( $languages ) {
	global $gc_local_package;

	$installed_languages = get_available_languages();

	echo "<label class='screen-reader-text' for='language'>选择一个默认语言</label>\n";
	echo "<select size='14' name='language' id='language'>\n";
	echo '<option value="" lang="zh" selected="selected" data-continue="Continue" data-installed="1">中文（简体）</option>';
	echo "\n";

	if ( ! empty( $gc_local_package ) && isset( $languages[ $gc_local_package ] ) ) {
		if ( isset( $languages[ $gc_local_package ] ) ) {
			$language = $languages[ $gc_local_package ];
			printf(
				'<option value="%s" lang="%s" data-continue="%s"%s>%s</option>' . "\n",
				esc_attr( $language['language'] ),
				esc_attr( current( $language['iso'] ) ),
				esc_attr( $language['strings']['continue'] ? $language['strings']['continue'] : 'Continue' ),
				in_array( $language['language'], $installed_languages, true ) ? ' data-installed="1"' : '',
				esc_html( $language['native_name'] )
			);

			unset( $languages[ $gc_local_package ] );
		}
	}

	foreach ( $languages as $language ) {
		printf(
			'<option value="%s" lang="%s" data-continue="%s"%s>%s</option>' . "\n",
			esc_attr( $language['language'] ),
			esc_attr( current( $language['iso'] ) ),
			esc_attr( $language['strings']['continue'] ? $language['strings']['continue'] : 'Continue' ),
			in_array( $language['language'], $installed_languages, true ) ? ' data-installed="1"' : '',
			esc_html( $language['native_name'] )
		);
	}
	echo "</select>\n";
	echo '<p class="step"><span class="spinner"></span><input id="language-continue" type="submit" class="button button-primary button-large" value="Continue" /></p>';
}

/**
 * Download a language pack.
 *
 *
 *
 * @see gc_get_available_translations()
 *
 * @param string $download Language code to download.
 * @return string|false Returns the language code if successfully downloaded
 *                      (or already installed), or false on failure.
 */
function gc_download_language_pack( $download ) {
	// Check if the translation is already installed.
	if ( in_array( $download, get_available_languages(), true ) ) {
		return $download;
	}

	if ( ! gc_is_file_mod_allowed( 'download_language_pack' ) ) {
		return false;
	}

	// Confirm the translation is one we can download.
	$translations = gc_get_available_translations();
	if ( ! $translations ) {
		return false;
	}
	foreach ( $translations as $translation ) {
		if ( $translation['language'] === $download ) {
			$translation_to_load = true;
			break;
		}
	}

	if ( empty( $translation_to_load ) ) {
		return false;
	}
	$translation = (object) $translation;

	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';
	$skin              = new Automatic_Upgrader_Skin;
	$upgrader          = new Language_Pack_Upgrader( $skin );
	$translation->type = 'core';
	$result            = $upgrader->upgrade( $translation, array( 'clear_update_cache' => false ) );

	if ( ! $result || is_gc_error( $result ) ) {
		return false;
	}

	return $translation->language;
}

/**
 * Check if GeChiUI has access to the filesystem without asking for
 * credentials.
 *
 *
 *
 * @return bool Returns true on success, false on failure.
 */
function gc_can_install_language_pack() {
	if ( ! gc_is_file_mod_allowed( 'can_install_language_pack' ) ) {
		return false;
	}

	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';
	$skin     = new Automatic_Upgrader_Skin;
	$upgrader = new Language_Pack_Upgrader( $skin );
	$upgrader->init();

	$check = $upgrader->fs_connect( array( GC_CONTENT_DIR, GC_LANG_DIR ) );

	if ( ! $check || is_gc_error( $check ) ) {
		return false;
	}

	return true;
}
