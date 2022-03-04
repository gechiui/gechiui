<?php
/**
 * Blocks API: GC_Block_Patterns_Registry class
 *
 * @package GeChiUI
 * @subpackage Blocks
 *
 */

/**
 * Class used for interacting with block patterns.
 *
 *
 */
final class GC_Block_Patterns_Registry {
	/**
	 * Registered block patterns array.
	 *
	 * @var array
	 */
	private $registered_patterns = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @var GC_Block_Patterns_Registry|null
	 */
	private static $instance = null;

	/**
	 * Registers a block pattern.
	 *
	 *
	 * @param string $pattern_name       Block pattern name including namespace.
	 * @param array  $pattern_properties {
	 *     List of properties for the block pattern.
	 *
	 *     @type string $title         Required. A human-readable title for the pattern.
	 *     @type string $content       Required. Block HTML markup for the pattern.
	 *     @type string $description   Optional. Visually hidden text used to describe the pattern in the
	 *                                 inserter. A description is optional, but is strongly
	 *                                 encouraged when the title does not fully describe what the
	 *                                 pattern does. The description will help users discover the
	 *                                 pattern while searching.
	 *     @type int    $viewportWidth Optional. The intended width of the pattern to allow for a scaled
	 *                                 preview within the pattern inserter.
	 *     @type array  $categories    Optional. A list of registered pattern categories used to group block
	 *                                 patterns. Block patterns can be shown on multiple categories.
	 *                                 A category must be registered separately in order to be used
	 *                                 here.
	 *     @type array  $keywords      Optional. A list of aliases or keywords that help users discover the
	 *                                 pattern while searching.
	 * }
	 * @return bool True if the pattern was registered with success and false otherwise.
	 */
	public function register( $pattern_name, $pattern_properties ) {
		if ( ! isset( $pattern_name ) || ! is_string( $pattern_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( '样板名称必须为字符串。' ),
				'5.5.0'
			);
			return false;
		}

		if ( ! isset( $pattern_properties['title'] ) || ! is_string( $pattern_properties['title'] ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( '样板标题必须为字符串。' ),
				'5.5.0'
			);
			return false;
		}

		if ( ! isset( $pattern_properties['content'] ) || ! is_string( $pattern_properties['content'] ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( '样板内容必须为字符串。' ),
				'5.5.0'
			);
			return false;
		}

		$this->registered_patterns[ $pattern_name ] = array_merge(
			$pattern_properties,
			array( 'name' => $pattern_name )
		);

		return true;
	}

	/**
	 * Unregisters a block pattern.
	 *
	 *
	 * @param string $pattern_name Block pattern name including namespace.
	 * @return bool True if the pattern was unregistered with success and false otherwise.
	 */
	public function unregister( $pattern_name ) {
		if ( ! $this->is_registered( $pattern_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Pattern name. */
				sprintf( __( '未找到区块样板“%s”。' ), $pattern_name ),
				'5.5.0'
			);
			return false;
		}

		unset( $this->registered_patterns[ $pattern_name ] );

		return true;
	}

	/**
	 * Retrieves an array containing the properties of a registered block pattern.
	 *
	 *
	 * @param string $pattern_name Block pattern name including namespace.
	 * @return array Registered pattern properties.
	 */
	public function get_registered( $pattern_name ) {
		if ( ! $this->is_registered( $pattern_name ) ) {
			return null;
		}

		return $this->registered_patterns[ $pattern_name ];
	}

	/**
	 * Retrieves all registered block patterns.
	 *
	 *
	 * @return array Array of arrays containing the registered block patterns properties,
	 *               and per style.
	 */
	public function get_all_registered() {
		return array_values( $this->registered_patterns );
	}

	/**
	 * Checks if a block pattern is registered.
	 *
	 *
	 * @param string $pattern_name Block pattern name including namespace.
	 * @return bool True if the pattern is registered, false otherwise.
	 */
	public function is_registered( $pattern_name ) {
		return isset( $this->registered_patterns[ $pattern_name ] );
	}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 *
	 * @return GC_Block_Patterns_Registry The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

/**
 * Registers a new block pattern.
 *
 *
 *
 * @param string $pattern_name       Block pattern name including namespace.
 * @param array  $pattern_properties List of properties for the block pattern.
 *                                   See GC_Block_Patterns_Registry::register() for accepted arguments.
 * @return bool True if the pattern was registered with success and false otherwise.
 */
function register_block_pattern( $pattern_name, $pattern_properties ) {
	return GC_Block_Patterns_Registry::get_instance()->register( $pattern_name, $pattern_properties );
}

/**
 * Unregisters a block pattern.
 *
 *
 *
 * @param string $pattern_name Block pattern name including namespace.
 * @return bool True if the pattern was unregistered with success and false otherwise.
 */
function unregister_block_pattern( $pattern_name ) {
	return GC_Block_Patterns_Registry::get_instance()->unregister( $pattern_name );
}
