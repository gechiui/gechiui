<?php
/**
 * REST API: GC_REST_Edit_Site_Export_Controller class
 *
 * @package    GeChiUI
 * @subpackage REST_API
 */

/**
 * Controller which provides REST endpoint for exporting current templates
 * and template parts.
 *
 *
 *
 * @see GC_REST_Controller
 */
class GC_REST_Edit_Site_Export_Controller extends GC_REST_Controller {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->namespace = 'gc-block-editor/v1';
		$this->rest_base = 'export';
	}

	/**
	 * Registers the site export route.
	 *
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => GC_REST_Server::READABLE,
					'callback'            => array( $this, 'export' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to export.
	 *
	 *
	 * @return GC_Error|true True if the request has access, or GC_Error object.
	 */
	public function permissions_check() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new GC_Error(
				'rest_cannot_export_templates',
				__( '抱歉，您无权导出模板和模板组件。' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Output a ZIP file with an export of the current templates
	 * and template parts from the site editor, and close the connection.
	 *
	 *
	 * @return GC_Error|void
	 */
	public function export() {
		// Generate the export file.
		$filename = gc_generate_block_templates_export_file();

		if ( is_gc_error( $filename ) ) {
			$filename->add_data( array( 'status' => 500 ) );

			return $filename;
		}

		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename=edit-site-export.zip' );
		header( 'Content-Length: ' . filesize( $filename ) );
		flush();
		readfile( $filename );
		unlink( $filename );
		exit;
	}
}
