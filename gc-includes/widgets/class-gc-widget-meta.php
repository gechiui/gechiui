<?php
/**
 * Widget API: GC_Widget_Meta class
 *
 * @package GeChiUI
 * @subpackage Widgets
 *
 */

/**
 * Core class used to implement a Meta widget.
 *
 * Displays log in/out, RSS feed links, etc.
 *
 *
 *
 * @see GC_Widget
 */
class GC_Widget_Meta extends GC_Widget {

	/**
	 * Sets up a new Meta widget instance.
	 *
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_meta',
			'description'                 => __( '登录、RSS和www.GeChiUI.com的链接。' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		parent::__construct( 'meta', __( '功能' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Meta widget instance.
	 *
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Meta widget instance.
	 */
	public function widget( $args, $instance ) {
		$default_title = __( '功能' );
		$title         = ! empty( $instance['title'] ) ? $instance['title'] : $default_title;

		/** This filter is documented in gc-includes/widgets/class-gc-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';

		/** This filter is documented in gc-includes/widgets/class-gc-nav-menu-widget.php */
		$format = apply_filters( 'navigation_widgets_format', $format );

		if ( 'html5' === $format ) {
			// The title may be filtered: Strip out HTML and make sure the aria-label is never empty.
			$title      = trim( strip_tags( $title ) );
			$aria_label = $title ? $title : $default_title;
			echo '<nav aria-label="' . esc_attr( $aria_label ) . '">';
		}
		?>

		<ul>
			<?php gc_register(); ?>
			<li><?php gc_loginout(); ?></li>
			<li><a href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>"><?php _e( '条目feed' ); ?></a></li>
			<li><a href="<?php echo esc_url( get_bloginfo( 'comments_rss2_url' ) ); ?>"><?php _e( '评论feed' ); ?></a></li>

			<?php
			/**
			 * Filters the "www.GeChiUI.com" list item HTML in the Meta widget.
			 *
		
		
			 *
			 * @param string $html     Default HTML for the www.GeChiUI.com list item.
			 * @param array  $instance Array of settings for the current widget.
			 */
			echo apply_filters(
				'widget_meta_poweredby',
				sprintf(
					'<li><a href="%1$s">%2$s</a></li>',
					esc_url( __( 'https://www.gechiui.com/' ) ),
					__( 'www.GeChiUI.com' )
				),
				$instance
			);

			gc_meta();
			?>

		</ul>

		<?php
		if ( 'html5' === $format ) {
			echo '</nav>';
		}

		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Meta widget instance.
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
	 * Outputs the settings form for the Meta widget.
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
