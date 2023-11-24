<?php
/**
 * Widget API: GC_Widget_Custom_HTML class
 *
 * @package GeChiUI
 * @subpackage Widgets
 */

/**
 * Core class used to implement a Custom HTML widget.
 *
 * @see GC_Widget
 */
class GC_Widget_Custom_HTML extends GC_Widget {

	/**
	 * Whether or not the widget has been registered yet.
	 *
	 * @since 4.9.0
	 * @var bool
	 */
	protected $registered = false;

	/**
	 * Default instance.
	 *
	 * @since 4.8.1
	 * @var array
	 */
	protected $default_instance = array(
		'title'   => '',
		'content' => '',
	);

	/**
	 * Sets up a new Custom HTML widget instance.
	 *
	 * @since 4.8.1
	 */
	public function __construct() {
		$widget_ops  = array(
			'classname'                   => 'widget_custom_html',
			'description'                 => __( '任意HTML代码。' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'custom_html', __( '自定义HTML' ), $widget_ops, $control_ops );
	}

	/**
	 * Add hooks for enqueueing assets when registering all widget instances of this widget class.
	 *
	 * @since 4.9.0
	 *
	 * @param int $number Optional. The unique order number of this widget instance
	 *                    compared to other instances of the same class. Default -1.
	 */
	public function _register_one( $number = -1 ) {
		parent::_register_one( $number );
		if ( $this->registered ) {
			return;
		}
		$this->registered = true;

		/*
		 * Note that the widgets component in the customizer will also do
		 * the 'admin_print_scripts-widgets.php' action in GC_Customize_Widgets::print_scripts().
		 */
		add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue_admin_scripts' ) );

		/*
		 * Note that the widgets component in the customizer will also do
		 * the 'admin_footer-widgets.php' action in GC_Customize_Widgets::print_footer_scripts().
		 */
		add_action( 'admin_footer-widgets.php', array( 'GC_Widget_Custom_HTML', 'render_control_template_scripts' ) );

		// Note this action is used to ensure the help text is added to the end.
		add_action( 'admin_head-widgets.php', array( 'GC_Widget_Custom_HTML', 'add_help_text' ) );
	}

	/**
	 * Filters gallery shortcode attributes.
	 *
	 * Prevents all of a site's attachments from being shown in a gallery displayed on a
	 * non-singular template where a $post context is not available.
	 *
	 * @since 4.9.0
	 *
	 * @param array $attrs Attributes.
	 * @return array Attributes.
	 */
	public function _filter_gallery_shortcode_attrs( $attrs ) {
		if ( ! is_singular() && empty( $attrs['id'] ) && empty( $attrs['include'] ) ) {
			$attrs['id'] = -1;
		}
		return $attrs;
	}

	/**
	 * Outputs the content for the current Custom HTML widget instance.
	 *
	 * @since 4.8.1
	 *
	 * @global GC_Post $post Global post object.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Custom HTML widget instance.
	 */
	public function widget( $args, $instance ) {
		global $post;

		// Override global $post so filters (and shortcodes) apply in a consistent context.
		$original_post = $post;
		if ( is_singular() ) {
			// Make sure post is always the queried object on singular queries (not from another sub-query that failed to clean up the global $post).
			$post = get_queried_object();
		} else {
			// Nullify the $post global during widget rendering to prevent shortcodes from running with the unexpected context on archive queries.
			$post = null;
		}

		// Prevent dumping out all attachments from the media library.
		add_filter( 'shortcode_atts_gallery', array( $this, '_filter_gallery_shortcode_attrs' ) );

		$instance = array_merge( $this->default_instance, $instance );

		/** This filter is documented in gc-includes/widgets/class-gc-widget-pages.php */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		// Prepare instance data that looks like a normal Text widget.
		$simulated_text_widget_instance = array_merge(
			$instance,
			array(
				'text'   => isset( $instance['content'] ) ? $instance['content'] : '',
				'filter' => false, // Because gcautop is not applied.
				'visual' => false, // Because it wasn't created in TinyMCE.
			)
		);
		unset( $simulated_text_widget_instance['content'] ); // Was moved to 'text' prop.

		/** This filter is documented in gc-includes/widgets/class-gc-widget-text.php */
		$content = apply_filters( 'widget_text', $instance['content'], $simulated_text_widget_instance, $this );

		// Adds 'noopener' relationship, without duplicating values, to all HTML A elements that have a target.
		$content = gc_targeted_link_rel( $content );

		/**
		 * Filters the content of the Custom HTML widget.
		 *
		 * @since 4.8.1
		 *
		 * @param string                $content  The widget content.
		 * @param array                 $instance Array of settings for the current widget.
		 * @param GC_Widget_Custom_HTML $widget   Current Custom HTML widget instance.
		 */
		$content = apply_filters( 'widget_custom_html_content', $content, $instance, $this );

		// Restore post global.
		$post = $original_post;
		remove_filter( 'shortcode_atts_gallery', array( $this, '_filter_gallery_shortcode_attrs' ) );

		// Inject the Text widget's container class name alongside this widget's class name for theme styling compatibility.
		$args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_text ', $args['before_widget'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo '<div class="textwidget custom-html-widget">'; // The textwidget class is for theme styling compatibility.
		echo $content;
		echo '</div>';
		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Custom HTML widget instance.
	 *
	 * @since 4.8.1
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            GC_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array_merge( $this->default_instance, $old_instance );
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['content'] = $new_instance['content'];
		} else {
			$instance['content'] = gc_kses_post( $new_instance['content'] );
		}
		return $instance;
	}

	/**
	 * Loads the required scripts and styles for the widget control.
	 *
	 * @since 4.9.0
	 */
	public function enqueue_admin_scripts() {
		$settings = gc_enqueue_code_editor(
			array(
				'type'       => 'text/html',
				'codemirror' => array(
					'indentUnit' => 2,
					'tabSize'    => 2,
				),
			)
		);

		gc_enqueue_script( 'custom-html-widgets' );
		gc_add_inline_script( 'custom-html-widgets', sprintf( 'gc.customHtmlWidgets.idBases.push( %s );', gc_json_encode( $this->id_base ) ) );

		if ( empty( $settings ) ) {
			$settings = array(
				'disabled' => true,
			);
		}
		gc_add_inline_script( 'custom-html-widgets', sprintf( 'gc.customHtmlWidgets.init( %s );', gc_json_encode( $settings ) ), 'after' );

		$l10n = array(
			'errorNotice' => array(
				/* translators: %d: Error count. */
				'singular' => _n( '在您保存前必须修正%d个错误。', '在您保存前必须修正%d个错误。', 1 ),
				/* translators: %d: Error count. */
				'plural'   => _n( '在您保存前必须修正%d个错误。', '在您保存前必须修正%d个错误。', 2 ),
				// @todo This is lacking, as some languages have a dedicated dual form. For proper handling of plurals in JS, see #20491.
			),
		);
		gc_add_inline_script( 'custom-html-widgets', sprintf( 'jQuery.extend( gc.customHtmlWidgets.l10n, %s );', gc_json_encode( $l10n ) ), 'after' );
	}

	/**
	 * Outputs the Custom HTML widget settings form.
	 *
	 * @since 4.8.1
	 * @since 4.9.0 The form contains only hidden sync inputs. For the control UI, see `GC_Widget_Custom_HTML::render_control_template_scripts()`.
	 *
	 * @see GC_Widget_Custom_HTML::render_control_template_scripts()
	 *
	 * @param array $instance Current instance.
	 */
	public function form( $instance ) {
		$instance = gc_parse_args( (array) $instance, $this->default_instance );
		?>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="title sync-input" type="hidden" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		<textarea id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" class="content sync-input" hidden><?php echo esc_textarea( $instance['content'] ); ?></textarea>
		<?php
	}

	/**
	 * Render form template scripts.
	 *
	 * @since 4.9.0
	 */
	public static function render_control_template_scripts() {
		?>
		<script type="text/html" id="tmpl-widget-custom-html-control-fields">
			<# var elementIdPrefix = 'el' + String( Math.random() ).replace( /\D/g, '' ) + '_' #>
			<p>
				<label for="{{ elementIdPrefix }}title"><?php esc_html_e( '标题：' ); ?></label>
				<input id="{{ elementIdPrefix }}title" type="text" class="widefat title">
			</p>

			<p>
				<label for="{{ elementIdPrefix }}content" id="{{ elementIdPrefix }}content-label"><?php esc_html_e( '内容：' ); ?></label>
				<textarea id="{{ elementIdPrefix }}content" class="widefat code content" rows="16" cols="20"></textarea>
			</p>

			<?php if ( ! current_user_can( 'unfiltered_html' ) ) : ?>
				<?php
				$probably_unsafe_html = array( 'script', 'iframe', 'form', 'input', 'style' );
				$allowed_html         = gc_kses_allowed_html( 'post' );
				$disallowed_html      = array_diff( $probably_unsafe_html, array_keys( $allowed_html ) );
				?>
				<?php if ( ! empty( $disallowed_html ) ) : ?>
					<# if ( data.codeEditorDisabled ) { #>
						<p>
							<?php _e( '不允许使用某些HTML标记，其中包括：' ); ?>
							<code><?php echo implode( '</code>, <code>', $disallowed_html ); ?></code>
						</p>
					<# } #>
				<?php endif; ?>
			<?php endif; ?>

			<div class="code-editor-error-container"></div>
		</script>
		<?php
	}

	/**
	 * Add help text to widgets admin screen.
	 *
	 * @since 4.9.0
	 */
	public static function add_help_text() {
		$screen = get_current_screen();

		$content  = '<p>';
		$content .= __( '使用自定义HTML小工具来向小工具区中加入任意HTML代码。' );
		$content .= '</p>';

		if ( 'false' !== gc_get_current_user()->syntax_highlighting ) {
			$content .= '<p>';
			$content .= sprintf(
				/* translators: 1: Link to user profile, 2: Additional link attributes, 3: Accessibility text. */
				__( '编辑区将自动高亮代码语法，您可以在您的<a href="%1$s" %2$s>用户资料%3$s</a>中选择纯文本模式。' ),
				esc_url( get_edit_profile_url() ),
				'class="external-link" target="_blank"',
				sprintf(
					'<span class="screen-reader-text"> %s</span>',
					/* translators: Hidden accessibility text. */
					__( '（在新窗口中打开）' )
				)
			);
			$content .= '</p>';

			$content .= '<p id="editor-keyboard-trap-help-1">' . __( '用键盘导航时：' ) . '</p>';
			$content .= '<ul>';
			$content .= '<li id="editor-keyboard-trap-help-2">' . __( '在编辑区域中，Tab键将输入一个制表符。' ) . '</li>';
			$content .= '<li id="editor-keyboard-trap-help-3">' . __( '要移开此区域，请先按Esc键再按Tab键。' ) . '</li>';
			$content .= '<li id="editor-keyboard-trap-help-4">' . __( '致屏幕阅读器用户：在表单模式中，您可能需要按Esc键两次。' ) . '</li>';
			$content .= '</ul>';
		}

		$screen->add_help_tab(
			array(
				'id'      => 'custom_html_widget',
				'title'   => __( '自定义HTML小工具' ),
				'content' => $content,
			)
		);
	}
}
