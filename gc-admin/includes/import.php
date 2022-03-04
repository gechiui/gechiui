<?php
/**
 * GeChiUI Administration Importer API.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Retrieve list of importers.
 *
 *
 *
 * @global array $gc_importers
 * @return array
 */
function get_importers() {
	global $gc_importers;
	if ( is_array( $gc_importers ) ) {
		uasort( $gc_importers, '_usort_by_first_member' );
	}
	return $gc_importers;
}

/**
 * Sorts a multidimensional array by first member of each top level member
 *
 * Used by uasort() as a callback, should not be used directly.
 *
 *
 * @access private
 *
 * @param array $a
 * @param array $b
 * @return int
 */
function _usort_by_first_member( $a, $b ) {
	return strnatcasecmp( $a[0], $b[0] );
}

/**
 * Register importer for GeChiUI.
 *
 *
 *
 * @global array $gc_importers
 *
 * @param string   $id          Importer tag. Used to uniquely identify importer.
 * @param string   $name        Importer name and title.
 * @param string   $description Importer description.
 * @param callable $callback    Callback to run.
 * @return void|GC_Error Void on success. GC_Error when $callback is GC_Error.
 */
function register_importer( $id, $name, $description, $callback ) {
	global $gc_importers;
	if ( is_gc_error( $callback ) ) {
		return $callback;
	}
	$gc_importers[ $id ] = array( $name, $description, $callback );
}

/**
 * Cleanup importer.
 *
 * Removes attachment based on ID.
 *
 *
 *
 * @param string $id Importer ID.
 */
function gc_import_cleanup( $id ) {
	gc_delete_attachment( $id );
}

/**
 * Handle importer uploading and add attachment.
 *
 *
 *
 * @return array Uploaded file's details on success, error message on failure
 */
function gc_import_handle_upload() {
	if ( ! isset( $_FILES['import'] ) ) {
		return array(
			'error' => sprintf(
				/* translators: 1: php.ini, 2: post_max_size, 3: upload_max_filesize */
				__( '文件是空的。请上传有内容的文件。这个错误也有可能是因为您的%1$s文件禁止了上传，或%1$s中%2$s的值小于%3$s的值。' ),
				'php.ini',
				'post_max_size',
				'upload_max_filesize'
			),
		);
	}

	$overrides                 = array(
		'test_form' => false,
		'test_type' => false,
	);
	$_FILES['import']['name'] .= '.txt';
	$upload                    = gc_handle_upload( $_FILES['import'], $overrides );

	if ( isset( $upload['error'] ) ) {
		return $upload;
	}

	// Construct the object array.
	$object = array(
		'post_title'     => gc_basename( $upload['file'] ),
		'post_content'   => $upload['url'],
		'post_mime_type' => $upload['type'],
		'guid'           => $upload['url'],
		'context'        => 'import',
		'post_status'    => 'private',
	);

	// Save the data.
	$id = gc_insert_attachment( $object, $upload['file'] );

	/*
	 * Schedule a cleanup for one day from now in case of failed
	 * import or missing gc_import_cleanup() call.
	 */
	gc_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array( $id ) );

	return array(
		'file' => $upload['file'],
		'id'   => $id,
	);
}

/**
 * Returns a list from www.GeChiUI.com of popular importer plugins.
 *
 *
 *
 * @return array Importers with metadata for each.
 */
function gc_get_popular_importers() {
	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	$locale            = get_user_locale();
	$cache_key         = 'popular_importers_' . md5( $locale . $gc_version );
	$popular_importers = get_site_transient( $cache_key );

	if ( ! $popular_importers ) {
		$url     = add_query_arg(
			array(
				'locale'  => $locale,
				'version' => $gc_version,
			),
			'http://api.gechiui.com/core/importers/1.1/'
		);
		$options = array( 'user-agent' => 'GeChiUI/' . $gc_version . '; ' . home_url( '/' ) );

		if ( gc_http_supports( array( 'ssl' ) ) ) {
			$url = set_url_scheme( $url, 'https' );
		}

		$response          = gc_remote_get( $url, $options );
		$popular_importers = json_decode( gc_remote_retrieve_body( $response ), true );

		if ( is_array( $popular_importers ) ) {
			set_site_transient( $cache_key, $popular_importers, 2 * DAY_IN_SECONDS );
		} else {
			$popular_importers = false;
		}
	}

	if ( is_array( $popular_importers ) ) {
		// If the data was received as translated, return it as-is.
		if ( $popular_importers['translated'] ) {
			return $popular_importers['importers'];
		}

		foreach ( $popular_importers['importers'] as &$importer ) {
			// phpcs:ignore GeChiUI.GC.I18n.LowLevelTranslationFunction,GeChiUI.GC.I18n.NonSingularStringLiteralText
			$importer['description'] = translate( $importer['description'] );
			if ( 'GeChiUI' !== $importer['name'] ) {
				// phpcs:ignore GeChiUI.GC.I18n.LowLevelTranslationFunction,GeChiUI.GC.I18n.NonSingularStringLiteralText
				$importer['name'] = translate( $importer['name'] );
			}
		}
		return $popular_importers['importers'];
	}

	return array(
		// slug => name, description, plugin slug, and register_importer() slug.
		'gccat2tag'   => array(
			'name'        => __( '分类与标签转换器' ),
			'description' => __( '选择性地将已有的分类转换为标签，或将标签转换为分类。' ),
			'plugin-slug' => 'gccat2tag-importer',
			'importer-id' => 'gc-cat2tag',
		),

		'rss'         => array(
			'name'        => __( 'RSS' ),
			'description' => __( '从RSS feed导入文章。' ),
			'plugin-slug' => 'rss-importer',
			'importer-id' => 'rss',
		),
		'gechiui'   => array(
			'name'        => 'GeChiUI',
			'description' => __( '从GeChiUI导出文件导入文章、页面、自定义字段、分类和标签。' ),
			'plugin-slug' => 'gechiui-importer',
			'importer-id' => 'gechiui',
		),
	);
}
