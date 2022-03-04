<?php
/**
 * Reusable blocks REST API: GC_REST_Blocks_Controller class
 *
 * @package GeChiUI
 * @subpackage REST_API
 *
 */

/**
 * Controller which provides a REST endpoint for the editor to read, create,
 * edit and delete reusable blocks. Blocks are stored as posts with the gc_block
 * post type.
 *
 *
 *
 * @see GC_REST_Posts_Controller
 * @see GC_REST_Controller
 */
class GC_REST_Blocks_Controller extends GC_REST_Posts_Controller {

	/**
	 * Checks if a block can be read.
	 *
	 *
	 * @param GC_Post $post Post object that backs the block.
	 * @return bool Whether the block can be read.
	 */
	public function check_read_permission( $post ) {
		// By default the read_post capability is mapped to edit_posts.
		if ( ! current_user_can( 'read_post', $post->ID ) ) {
			return false;
		}

		return parent::check_read_permission( $post );
	}

	/**
	 * Filters a response based on the context defined in the schema.
	 *
	 *
	 * @param array  $data    Response data to filter.
	 * @param string $context Context defined in the schema.
	 * @return array Filtered response.
	 */
	public function filter_response_by_context( $data, $context ) {
		$data = parent::filter_response_by_context( $data, $context );

		/*
		 * Remove `title.rendered` and `content.rendered` from the response. It
		 * doesn't make sense for a reusable block to have rendered content on its
		 * own, since rendering a block requires it to be inside a post or a page.
		 */
		unset( $data['title']['rendered'] );
		unset( $data['content']['rendered'] );

		return $data;
	}

	/**
	 * Retrieves the block's schema, conforming to JSON Schema.
	 *
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		// Do not cache this schema because all properties are derived from parent controller.
		$schema = parent::get_item_schema();

		/*
		 * Allow all contexts to access `title.raw` and `content.raw`. Clients always
		 * need the raw markup of a reusable block to do anything useful, e.g. parse
		 * it or display it in an editor.
		 */
		$schema['properties']['title']['properties']['raw']['context']   = array( 'view', 'edit' );
		$schema['properties']['content']['properties']['raw']['context'] = array( 'view', 'edit' );

		/*
		 * Remove `title.rendered` and `content.rendered` from the schema. It doesn’t
		 * make sense for a reusable block to have rendered content on its own, since
		 * rendering a block requires it to be inside a post or a page.
		 */
		unset( $schema['properties']['title']['properties']['rendered'] );
		unset( $schema['properties']['content']['properties']['rendered'] );

		return $schema;
	}

}
