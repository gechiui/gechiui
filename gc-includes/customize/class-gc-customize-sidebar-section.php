<?php
/**
 * Customize API: GC_Customize_Sidebar_Section class
 *
 * @package GeChiUI
 * @subpackage Customize
 *
 */

/**
 * Customizer section representing widget area (sidebar).
 *
 *
 *
 * @see GC_Customize_Section
 */
class GC_Customize_Sidebar_Section extends GC_Customize_Section {

	/**
	 * Type of this section.
	 *
	 * @var string
	 */
	public $type = 'sidebar';

	/**
	 * Unique identifier.
	 *
	 * @var string
	 */
	public $sidebar_id;

	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 *
	 * @return array The array to be exported to the client as JSON.
	 */
	public function json() {
		$json              = parent::json();
		$json['sidebarId'] = $this->sidebar_id;
		return $json;
	}

	/**
	 * Whether the current sidebar is rendered on the page.
	 *
	 *
	 * @return bool Whether sidebar is rendered.
	 */
	public function active_callback() {
		return $this->manager->widgets->is_sidebar_rendered( $this->sidebar_id );
	}
}
