<?php
/**
 * Widget API: GC_Widget_Tag_Cloud class
 *
 * @package GeChiUI
 * @subpackage Widgets
 */

/**
 * Core class used to implement a Tag cloud widget.
 *
 * @see GC_Widget
 */
class GC_Widget_Tag_Cloud extends GC_Widget {

	/**
	 * Sets up a new Tag Cloud widget instance.
	 *
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( '您最常使用的标签云。' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		parent::__construct( 'tag_cloud', __( '标签云' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Tag Cloud widget instance.
	 *
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Tag Cloud widget instance.
	 */
	public function widget( $args, $instance ) {
		$current_taxonomy = $this->_get_current_taxonomy( $instance );

		if ( ! empty( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			if ( 'post_tag' === $current_taxonomy ) {
				$title = __( '标签' );
			} else {
				$tax   = get_taxonomy( $current_taxonomy );
				$title = $tax->labels->name;
			}
		}

		$default_title = $title;

		$show_count = ! empty( $instance['count'] );

		$tag_cloud = gc_tag_cloud(
			/**
			 * Filters the taxonomy used in the Tag Cloud widget.
			 *
			 * @since 2.8.0
			 * @since 3.0.0 Added taxonomy drop-down.
			 * @since 4.9.0 Added the `$instance` parameter.
			 *
			 * @see gc_tag_cloud()
			 *
			 * @param array $args     Args used for the tag cloud widget.
			 * @param array $instance Array of settings for the current widget.
			 */
			apply_filters(
				'widget_tag_cloud_args',
				array(
					'taxonomy'   => $current_taxonomy,
					'echo'       => false,
					'show_count' => $show_count,
				),
				$instance
			)
		);

		if ( empty( $tag_cloud ) ) {
			return;
		}

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

		echo '<div class="tagcloud">';

		echo $tag_cloud;

		echo "</div>\n";

		if ( 'html5' === $format ) {
			echo '</nav>';
		}

		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Tag Cloud widget instance.
	 *
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            GC_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = array();
		$instance['title']    = sanitize_text_field( $new_instance['title'] );
		$instance['count']    = ! empty( $new_instance['count'] ) ? 1 : 0;
		$instance['taxonomy'] = stripslashes( $new_instance['taxonomy'] );
		return $instance;
	}

	/**
	 * Outputs the Tag Cloud widget settings form.
	 *
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$count = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( '标题：' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
		$taxonomies       = get_taxonomies( array( 'show_tagcloud' => true ), 'object' );
		$current_taxonomy = $this->_get_current_taxonomy( $instance );

		switch ( count( $taxonomies ) ) {

			// No tag cloud supporting taxonomies found, display error message.
			case 0:
				?>
				<input type="hidden" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" value="" />
				<p>
					<?php _e( '标签云未被显示，因为不存在支持标签云小工具的分类法。' ); ?>
				</p>
				<?php
				break;

			// Just a single tag cloud supporting taxonomy found, no need to display a select.
			case 1:
				$keys     = array_keys( $taxonomies );
				$taxonomy = reset( $keys );
				?>
				<input type="hidden" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" value="<?php echo esc_attr( $taxonomy ); ?>" />
				<?php
				break;

			// More than one tag cloud supporting taxonomy found, display a select.
			default:
				?>
				<p>
					<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( '分类法：' ); ?></label>
					<select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>">
					<?php foreach ( $taxonomies as $taxonomy => $tax ) : ?>
						<option value="<?php echo esc_attr( $taxonomy ); ?>" <?php selected( $taxonomy, $current_taxonomy ); ?>>
							<?php echo esc_html( $tax->labels->name ); ?>
						</option>
					<?php endforeach; ?>
					</select>
				</p>
				<?php
		}

		if ( count( $taxonomies ) > 0 ) {
			?>
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" <?php checked( $count, true ); ?> />
				<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( '显示标签计数' ); ?></label>
			</p>
			<?php
		}
	}

	/**
	 * Retrieves the taxonomy for the current Tag cloud widget instance.
	 *
	 * @since 4.4.0
	 *
	 * @param array $instance Current settings.
	 * @return string Name of the current taxonomy if set, otherwise 'post_tag'.
	 */
	public function _get_current_taxonomy( $instance ) {
		if ( ! empty( $instance['taxonomy'] ) && taxonomy_exists( $instance['taxonomy'] ) ) {
			return $instance['taxonomy'];
		}

		return 'post_tag';
	}
}
