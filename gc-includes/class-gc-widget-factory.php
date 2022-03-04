<?php
/**
 * Widget API: GC_Widget_Factory class
 *
 * @package GeChiUI
 * @subpackage Widgets
 *
 */

/**
 * Singleton that registers and instantiates GC_Widget classes.
 *
 *
 *
 */
class GC_Widget_Factory {

	/**
	 * Widgets array.
	 *
	 * @var array
	 */
	public $widgets = array();

	/**
	 * PHP5 constructor.
	 *
	 */
	public function __construct() {
		add_action( 'widgets_init', array( $this, '_register_widgets' ), 100 );
	}

	/**
	 * PHP4 constructor.
	 *
	 * @deprecated 4.3.0 Use __construct() instead.
	 *
	 * @see GC_Widget_Factory::__construct()
	 */
	public function GC_Widget_Factory() {
		_deprecated_constructor( 'GC_Widget_Factory', '4.3.0' );
		self::__construct();
	}

	/**
	 * Registers a widget subclass.
	 *
	 *              instead of simply a `GC_Widget` subclass name.
	 *
	 * @param string|GC_Widget $widget Either the name of a `GC_Widget` subclass or an instance of a `GC_Widget` subclass.
	 */
	public function register( $widget ) {
		if ( $widget instanceof GC_Widget ) {
			$this->widgets[ spl_object_hash( $widget ) ] = $widget;
		} else {
			$this->widgets[ $widget ] = new $widget();
		}
	}

	/**
	 * Un-registers a widget subclass.
	 *
	 *              instead of simply a `GC_Widget` subclass name.
	 *
	 * @param string|GC_Widget $widget Either the name of a `GC_Widget` subclass or an instance of a `GC_Widget` subclass.
	 */
	public function unregister( $widget ) {
		if ( $widget instanceof GC_Widget ) {
			unset( $this->widgets[ spl_object_hash( $widget ) ] );
		} else {
			unset( $this->widgets[ $widget ] );
		}
	}

	/**
	 * Serves as a utility method for adding widgets to the registered widgets global.
	 *
	 *
	 * @global array $gc_registered_widgets
	 */
	public function _register_widgets() {
		global $gc_registered_widgets;
		$keys       = array_keys( $this->widgets );
		$registered = array_keys( $gc_registered_widgets );
		$registered = array_map( '_get_widget_id_base', $registered );

		foreach ( $keys as $key ) {
			// Don't register new widget if old widget with the same id is already registered.
			if ( in_array( $this->widgets[ $key ]->id_base, $registered, true ) ) {
				unset( $this->widgets[ $key ] );
				continue;
			}

			$this->widgets[ $key ]->_register();
		}
	}

	/**
	 * Returns the registered GC_Widget object for the given widget type.
	 *
	 *
	 * @param string $id_base Widget type ID.
	 * @return GC_Widget|null
	 */
	public function get_widget_object( $id_base ) {
		$key = $this->get_widget_key( $id_base );
		if ( '' === $key ) {
			return null;
		}

		return $this->widgets[ $key ];
	}

	/**
	 * Returns the registered key for the given widget type.
	 *
	 *
	 * @param string $id_base Widget type ID.
	 * @return string
	 */
	public function get_widget_key( $id_base ) {
		foreach ( $this->widgets as $key => $widget_object ) {
			if ( $widget_object->id_base === $id_base ) {
				return $key;
			}
		}

		return '';
	}
}
