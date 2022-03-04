<?php
/**
 * Blocks API: GC_Block_Styles_Registry class
 *
 * @package GeChiUI
 * @subpackage Blocks
 *
 */

/**
 * Class used for interacting with block styles.
 *
 *
 */
final class GC_Block_Styles_Registry {
	/**
	 * Registered block styles, as `$block_name => $block_style_name => $block_style_properties` multidimensional arrays.
	 *
	 *
	 * @var array[]
	 */
	private $registered_block_styles = array();

	/**
	 * Container for the main instance of the class.
	 *
	 *
	 * @var GC_Block_Styles_Registry|null
	 */
	private static $instance = null;

	/**
	 * Registers a block style for the given block type.
	 *
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param array  $style_properties Array containing the properties of the style name, label,
	 *                                 is_default, style_handle (name of the stylesheet to be enqueued),
	 *                                 inline_style (string containing the CSS to be added).
	 * @return bool True if the block style was registered with success and false otherwise.
	 */
	public function register( $block_name, $style_properties ) {

		if ( ! isset( $block_name ) || ! is_string( $block_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( '区块名必须为字符串。' ),
				'5.3.0'
			);
			return false;
		}

		if ( ! isset( $style_properties['name'] ) || ! is_string( $style_properties['name'] ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( '区块样式名必须为字符串。' ),
				'5.3.0'
			);
			return false;
		}

		if ( str_contains( $style_properties['name'], ' ' ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( '区块样式名称不得包含任何空格。' ),
				'5.9.0'
			);
			return false;
		}

		$block_style_name = $style_properties['name'];

		if ( ! isset( $this->registered_block_styles[ $block_name ] ) ) {
			$this->registered_block_styles[ $block_name ] = array();
		}
		$this->registered_block_styles[ $block_name ][ $block_style_name ] = $style_properties;

		return true;
	}

	/**
	 * Unregisters a block style of the given block type.
	 *
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param string $block_style_name Block style name.
	 * @return bool True if the block style was unregistered with success and false otherwise.
	 */
	public function unregister( $block_name, $block_style_name ) {
		if ( ! $this->is_registered( $block_name, $block_style_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: 1: Block name, 2: Block style name. */
				sprintf( __( '区块“%1$s”不包含名为“%2$s”的样式。' ), $block_name, $block_style_name ),
				'5.3.0'
			);
			return false;
		}

		unset( $this->registered_block_styles[ $block_name ][ $block_style_name ] );

		return true;
	}

	/**
	 * Retrieves the properties of a registered block style for the given block type.
	 *
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param string $block_style_name Block style name.
	 * @return array Registered block style properties.
	 */
	public function get_registered( $block_name, $block_style_name ) {
		if ( ! $this->is_registered( $block_name, $block_style_name ) ) {
			return null;
		}

		return $this->registered_block_styles[ $block_name ][ $block_style_name ];
	}

	/**
	 * Retrieves all registered block styles.
	 *
	 *
	 * @return array[] Array of arrays containing the registered block styles properties grouped by block type.
	 */
	public function get_all_registered() {
		return $this->registered_block_styles;
	}

	/**
	 * Retrieves registered block styles for a specific block type.
	 *
	 *
	 * @param string $block_name Block type name including namespace.
	 * @return array[] Array whose keys are block style names and whose values are block style properties.
	 */
	public function get_registered_styles_for_block( $block_name ) {
		if ( isset( $this->registered_block_styles[ $block_name ] ) ) {
			return $this->registered_block_styles[ $block_name ];
		}
		return array();
	}

	/**
	 * Checks if a block style is registered for the given block type.
	 *
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param string $block_style_name Block style name.
	 * @return bool True if the block style is registered, false otherwise.
	 */
	public function is_registered( $block_name, $block_style_name ) {
		return isset( $this->registered_block_styles[ $block_name ][ $block_style_name ] );
	}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 *
	 * @return GC_Block_Styles_Registry The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
