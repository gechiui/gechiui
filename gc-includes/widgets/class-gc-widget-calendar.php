<?php
/**
 * Widget API: GC_Widget_Calendar class
 *
 * @package GeChiUI
 * @subpackage Widgets
 *
 */

/**
 * Core class used to implement the Calendar widget.
 *
 *
 *
 * @see GC_Widget
 */
class GC_Widget_Calendar extends GC_Widget {
	/**
	 * Ensure that the ID attribute only appears in the markup once
	 *
	 * @var int
	 */
	private static $instance = 0;

	/**
	 * Sets up a new Calendar widget instance.
	 *
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_calendar',
			'description'                 => __( '您站点文章的日历。' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		parent::__construct( 'calendar', __( '日历' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Calendar widget instance.
	 *
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in gc-includes/widgets/class-gc-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if ( 0 === self::$instance ) {
			echo '<div id="calendar_wrap" class="calendar_wrap">';
		} else {
			echo '<div class="calendar_wrap">';
		}
		get_calendar();
		echo '</div>';
		echo $args['after_widget'];

		self::$instance++;
	}

	/**
	 * Handles updating settings for the current Calendar widget instance.
	 *
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            GC_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Outputs the settings form for the Calendar widget.
	 *
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = gc_parse_args( (array) $instance, array( 'title' => '' ) );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( '标题：' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<?php
	}
}
