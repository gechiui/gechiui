<?php
/**
 * Widget API: GC_Media_Widget class
 *
 * @package GeChiUI
 * @subpackage Widgets
 *
 */

/**
 * Core class that implements a media widget.
 *
 *
 *
 * @see GC_Widget
 */
abstract class GC_Widget_Media extends GC_Widget {

	/**
	 * Translation labels.
	 *
	 * @var array
	 */
	public $l10n = array(
		'add_to_widget'              => '',
		'replace_media'              => '',
		'edit_media'                 => '',
		'media_library_state_multi'  => '',
		'media_library_state_single' => '',
		'missing_attachment'         => '',
		'no_media_selected'          => '',
		'add_media'                  => '',
	);

	/**
	 * Whether or not the widget has been registered yet.
	 *
	 * @var bool
	 */
	protected $registered = false;

	/**
	 * Constructor.
	 *
	 *
	 * @param string $id_base         Base ID for the widget, lowercase and unique.
	 * @param string $name            Name for the widget displayed on the configuration page.
	 * @param array  $widget_options  Optional. Widget options. See gc_register_sidebar_widget() for
	 *                                information on accepted arguments. Default empty array.
	 * @param array  $control_options Optional. Widget control options. See gc_register_widget_control()
	 *                                for information on accepted arguments. Default empty array.
	 */
	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		$widget_opts = gc_parse_args(
			$widget_options,
			array(
				'description'                 => __( '单个媒体项目。' ),
				'customize_selective_refresh' => true,
				'show_instance_in_rest'       => true,
				'mime_type'                   => '',
			)
		);

		$control_opts = gc_parse_args( $control_options, array() );

		$l10n_defaults = array(
			'no_media_selected'          => __( '未选中媒体' ),
			'add_media'                  => _x( '添加媒体', 'label for button in the media widget' ),
			'replace_media'              => _x( '替换媒体', 'label for button in the media widget; should preferably not be longer than ~13 characters long' ),
			'edit_media'                 => _x( '编辑媒体', 'label for button in the media widget; should preferably not be longer than ~13 characters long' ),
			'add_to_widget'              => __( '添加至小工具' ),
			'missing_attachment'         => sprintf(
				/* translators: %s: URL to media library. */
				__( '找不到指定的文件。请使用<a href="%s">媒体库</a>检查文件没有被删除。' ),
				esc_url( admin_url( 'upload.php' ) )
			),
			/* translators: %d: Widget count. */
			'media_library_state_multi'  => _n_noop( '媒体小工具（%d）', '媒体小工具（%d）' ),
			'media_library_state_single' => __( '媒体小工具' ),
			'unsupported_file_type'      => __( '这个文件的格式不受支持。请重新添加一个合适的文件。' ),
		);
		$this->l10n    = array_merge( $l10n_defaults, array_filter( $this->l10n ) );

		parent::__construct(
			$id_base,
			$name,
			$widget_opts,
			$control_opts
		);
	}

	/**
	 * Add hooks while registering all widget instances of this widget class.
	 *
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

		// Note that the widgets component in the customizer will also do
		// the 'admin_print_scripts-widgets.php' action in GC_Customize_Widgets::print_scripts().
		add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue_admin_scripts' ) );

		if ( $this->is_preview() ) {
			add_action( 'gc_enqueue_scripts', array( $this, 'enqueue_preview_scripts' ) );
		}

		// Note that the widgets component in the customizer will also do
		// the 'admin_footer-widgets.php' action in GC_Customize_Widgets::print_footer_scripts().
		add_action( 'admin_footer-widgets.php', array( $this, 'render_control_template_scripts' ) );

		add_filter( 'display_media_states', array( $this, 'display_media_state' ), 10, 2 );
	}

	/**
	 * Get schema for properties of a widget instance (item).
	 *
	 *
	 * @see GC_REST_Controller::get_item_schema()
	 * @see GC_REST_Controller::get_additional_fields()
	 * @link https://core.trac.gechiui.com/ticket/35574
	 *
	 * @return array Schema for properties.
	 */
	public function get_instance_schema() {
		$schema = array(
			'attachment_id' => array(
				'type'        => 'integer',
				'default'     => 0,
				'minimum'     => 0,
				'description' => __( '附件文章ID' ),
				'media_prop'  => 'id',
			),
			'url'           => array(
				'type'        => 'string',
				'default'     => '',
				'format'      => 'uri',
				'description' => __( '媒体URL' ),
			),
			'title'         => array(
				'type'                  => 'string',
				'default'               => '',
				'sanitize_callback'     => 'sanitize_text_field',
				'description'           => __( '小工具标题' ),
				'should_preview_update' => false,
			),
		);

		/**
		 * Filters the media widget instance schema to add additional properties.
		 *
		 *
		 * @param array           $schema Instance schema.
		 * @param GC_Widget_Media $widget Widget object.
		 */
		$schema = apply_filters( "widget_{$this->id_base}_instance_schema", $schema, $this );

		return $schema;
	}

	/**
	 * Determine if the supplied attachment is for a valid attachment post with the specified MIME type.
	 *
	 *
	 * @param int|GC_Post $attachment Attachment post ID or object.
	 * @param string      $mime_type  MIME type.
	 * @return bool Is matching MIME type.
	 */
	public function is_attachment_with_mime_type( $attachment, $mime_type ) {
		if ( empty( $attachment ) ) {
			return false;
		}
		$attachment = get_post( $attachment );
		if ( ! $attachment ) {
			return false;
		}
		if ( 'attachment' !== $attachment->post_type ) {
			return false;
		}
		return gc_attachment_is( $mime_type, $attachment );
	}

	/**
	 * Sanitize a token list string, such as used in HTML rel and class attributes.
	 *
	 *
	 * @link http://w3c.github.io/html/infrastructure.html#space-separated-tokens
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/DOMTokenList
	 * @param string|array $tokens List of tokens separated by spaces, or an array of tokens.
	 * @return string Sanitized token string list.
	 */
	public function sanitize_token_list( $tokens ) {
		if ( is_string( $tokens ) ) {
			$tokens = preg_split( '/\s+/', trim( $tokens ) );
		}
		$tokens = array_map( 'sanitize_html_class', $tokens );
		$tokens = array_filter( $tokens );
		return implode( ' ', $tokens );
	}

	/**
	 * Displays the widget on the front-end.
	 *
	 *
	 * @see GC_Widget::widget()
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance Saved setting from the database.
	 */
	public function widget( $args, $instance ) {
		$instance = gc_parse_args( $instance, gc_list_pluck( $this->get_instance_schema(), 'default' ) );

		// Short-circuit if no media is selected.
		if ( ! $this->has_content( $instance ) ) {
			return;
		}

		echo $args['before_widget'];

		/** This filter is documented in gc-includes/widgets/class-gc-widget-pages.php */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		/**
		 * Filters the media widget instance prior to rendering the media.
		 *
		 *
		 * @param array           $instance Instance data.
		 * @param array           $args     Widget args.
		 * @param GC_Widget_Media $widget   Widget object.
		 */
		$instance = apply_filters( "widget_{$this->id_base}_instance", $instance, $args, $this );

		$this->render_media( $instance );

		echo $args['after_widget'];
	}

	/**
	 * Sanitizes the widget form values as they are saved.
	 *
	 *              for PHP 8 named parameter support.
	 *
	 * @see GC_Widget::update()
	 * @see GC_REST_Request::has_valid_params()
	 * @see GC_REST_Request::sanitize_params()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$schema = $this->get_instance_schema();
		foreach ( $schema as $field => $field_schema ) {
			if ( ! array_key_exists( $field, $new_instance ) ) {
				continue;
			}
			$value = $new_instance[ $field ];

			/*
			 * Workaround for rest_validate_value_from_schema() due to the fact that
			 * rest_is_boolean( '' ) === false, while rest_is_boolean( '1' ) is true.
			 */
			if ( 'boolean' === $field_schema['type'] && '' === $value ) {
				$value = false;
			}

			if ( true !== rest_validate_value_from_schema( $value, $field_schema, $field ) ) {
				continue;
			}

			$value = rest_sanitize_value_from_schema( $value, $field_schema );

			// @codeCoverageIgnoreStart
			if ( is_gc_error( $value ) ) {
				continue; // Handle case when rest_sanitize_value_from_schema() ever returns GC_Error as its phpdoc @return tag indicates.
			}

			// @codeCoverageIgnoreEnd
			if ( isset( $field_schema['sanitize_callback'] ) ) {
				$value = call_user_func( $field_schema['sanitize_callback'], $value );
			}
			if ( is_gc_error( $value ) ) {
				continue;
			}
			$old_instance[ $field ] = $value;
		}

		return $old_instance;
	}

	/**
	 * Render the media on the frontend.
	 *
	 *
	 * @param array $instance Widget instance props.
	 */
	abstract public function render_media( $instance );

	/**
	 * Outputs the settings update form.
	 *
	 * Note that the widget UI itself is rendered with JavaScript via `MediaWidgetControl#render()`.
	 *
	 *
	 * @see \GC_Widget_Media::render_control_template_scripts() Where the JS template is located.
	 *
	 * @param array $instance Current settings.
	 */
	final public function form( $instance ) {
		$instance_schema = $this->get_instance_schema();
		$instance        = gc_array_slice_assoc(
			gc_parse_args( (array) $instance, gc_list_pluck( $instance_schema, 'default' ) ),
			array_keys( $instance_schema )
		);

		foreach ( $instance as $name => $value ) : ?>
			<input
				type="hidden"
				data-property="<?php echo esc_attr( $name ); ?>"
				class="media-widget-instance-property"
				name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( $name ) ); // Needed specifically by gcWidgets.appendTitle(). ?>"
				value="<?php echo esc_attr( is_array( $value ) ? implode( ',', $value ) : (string) $value ); ?>"
			/>
			<?php
		endforeach;
	}

	/**
	 * Filters the default media display states for items in the Media list table.
	 *
	 *
	 * @param array   $states An array of media states.
	 * @param GC_Post $post   The current attachment object.
	 * @return array
	 */
	public function display_media_state( $states, $post = null ) {
		if ( ! $post ) {
			$post = get_post();
		}

		// Count how many times this attachment is used in widgets.
		$use_count = 0;
		foreach ( $this->get_settings() as $instance ) {
			if ( isset( $instance['attachment_id'] ) && $instance['attachment_id'] === $post->ID ) {
				$use_count++;
			}
		}

		if ( 1 === $use_count ) {
			$states[] = $this->l10n['media_library_state_single'];
		} elseif ( $use_count > 0 ) {
			$states[] = sprintf( translate_nooped_plural( $this->l10n['media_library_state_multi'], $use_count ), number_format_i18n( $use_count ) );
		}

		return $states;
	}

	/**
	 * Enqueue preview scripts.
	 *
	 * These scripts normally are enqueued just-in-time when a widget is rendered.
	 * In the customizer, however, widgets can be dynamically added and rendered via
	 * selective refresh, and so it is important to unconditionally enqueue them in
	 * case a widget does get added.
	 *
	 */
	public function enqueue_preview_scripts() {}

	/**
	 * Loads the required scripts and styles for the widget control.
	 *
	 */
	public function enqueue_admin_scripts() {
		gc_enqueue_media();
		gc_enqueue_script( 'media-widgets' );
	}

	/**
	 * Render form template scripts.
	 *
	 */
	public function render_control_template_scripts() {
		?>
		<script type="text/html" id="tmpl-widget-media-<?php echo esc_attr( $this->id_base ); ?>-control">
			<# var elementIdPrefix = 'el' + String( Math.random() ) + '_' #>
			<p>
				<label for="{{ elementIdPrefix }}title"><?php esc_html_e( '标题：' ); ?></label>
				<input id="{{ elementIdPrefix }}title" type="text" class="widefat title">
			</p>
			<div class="media-widget-preview <?php echo esc_attr( $this->id_base ); ?>">
				<div class="attachment-media-view">
					<button type="button" class="select-media button-add-media not-selected">
						<?php echo esc_html( $this->l10n['add_media'] ); ?>
					</button>
				</div>
			</div>
			<p class="media-widget-buttons">
				<button type="button" class="button edit-media selected">
					<?php echo esc_html( $this->l10n['edit_media'] ); ?>
				</button>
			<?php if ( ! empty( $this->l10n['replace_media'] ) ) : ?>
				<button type="button" class="button change-media select-media selected">
					<?php echo esc_html( $this->l10n['replace_media'] ); ?>
				</button>
			<?php endif; ?>
			</p>
			<div class="media-widget-fields">
			</div>
		</script>
		<?php
	}

	/**
	 * Whether the widget has content to show.
	 *
	 *
	 * @param array $instance Widget instance props.
	 * @return bool Whether widget has content.
	 */
	protected function has_content( $instance ) {
		return ( $instance['attachment_id'] && 'attachment' === get_post_type( $instance['attachment_id'] ) ) || $instance['url'];
	}
}
