<?php
/**
 * Facilitates adding of the GeChiUI editor as used on the Write and Edit screens.
 *
 * @package GeChiUI
 *
 *
 * Private, not included by default. See gc_editor() in gc-includes/general-template.php.
 */

final class _GC_Editors {
	public static $mce_locale;

	private static $mce_settings = array();
	private static $qt_settings  = array();
	private static $plugins      = array();
	private static $qt_buttons   = array();
	private static $ext_plugins;
	private static $baseurl;
	private static $first_init;
	private static $this_tinymce       = false;
	private static $this_quicktags     = false;
	private static $has_tinymce        = false;
	private static $has_quicktags      = false;
	private static $has_medialib       = false;
	private static $editor_buttons_css = true;
	private static $drag_drop_upload   = false;
	private static $translation;
	private static $tinymce_scripts_printed = false;
	private static $link_dialog_printed     = false;

	private function __construct() {}

	/**
	 * Parse default arguments for the editor instance.
	 *
	 *
	 * @param string $editor_id HTML ID for the textarea and TinyMCE and Quicktags instances.
	 *                          Should not contain square brackets.
	 * @param array  $settings {
	 *     Array of editor arguments.
	 *
	 *     @type bool       $gcautop           Whether to use gcautop(). Default true.
	 *     @type bool       $media_buttons     Whether to show the Add Media/other media buttons.
	 *     @type string     $default_editor    When both TinyMCE and Quicktags are used, set which
	 *                                         editor is shown on page load. Default empty.
	 *     @type bool       $drag_drop_upload  Whether to enable drag & drop on the editor uploading. Default false.
	 *                                         Requires the media modal.
	 *     @type string     $textarea_name     Give the textarea a unique name here. Square brackets
	 *                                         can be used here. Default $editor_id.
	 *     @type int        $textarea_rows     Number rows in the editor textarea. Default 20.
	 *     @type string|int $tabindex          Tabindex value to use. Default empty.
	 *     @type string     $tabfocus_elements The previous and next element ID to move the focus to
	 *                                         when pressing the Tab key in TinyMCE. Default ':prev,:next'.
	 *     @type string     $editor_css        Intended for extra styles for both Visual and Text editors.
	 *                                         Should include `<style>` tags, and can use "scoped". Default empty.
	 *     @type string     $editor_class      Extra classes to add to the editor textarea element. Default empty.
	 *     @type bool       $teeny             Whether to output the minimal editor config. Examples include
	 *                                         Press This and the Comment editor. Default false.
	 *     @type bool       $dfw               Deprecated in 4.1. Unused.
	 *     @type bool|array $tinymce           Whether to load TinyMCE. Can be used to pass settings directly to
	 *                                         TinyMCE using an array. Default true.
	 *     @type bool|array $quicktags         Whether to load Quicktags. Can be used to pass settings directly to
	 *                                         Quicktags using an array. Default true.
	 * }
	 * @return array Parsed arguments array.
	 */
	public static function parse_settings( $editor_id, $settings ) {

		/**
		 * Filters the gc_editor() settings.
		 *
		 *
		 * @see _GC_Editors::parse_settings()
		 *
		 * @param array  $settings  Array of editor arguments.
		 * @param string $editor_id Unique editor identifier, e.g. 'content'. Accepts 'classic-block'
		 *                          when called from block editor's Classic block.
		 */
		$settings = apply_filters( 'gc_editor_settings', $settings, $editor_id );

		$set = gc_parse_args(
			$settings,
			array(
				// Disable autop if the current post has blocks in it.
				'gcautop'             => ! has_blocks(),
				'media_buttons'       => true,
				'default_editor'      => '',
				'drag_drop_upload'    => false,
				'textarea_name'       => $editor_id,
				'textarea_rows'       => 20,
				'tabindex'            => '',
				'tabfocus_elements'   => ':prev,:next',
				'editor_css'          => '',
				'editor_class'        => '',
				'teeny'               => false,
				'_content_editor_dfw' => false,
				'tinymce'             => true,
				'quicktags'           => true,
			)
		);

		self::$this_tinymce = ( $set['tinymce'] && user_can_richedit() );

		if ( self::$this_tinymce ) {
			if ( false !== strpos( $editor_id, '[' ) ) {
				self::$this_tinymce = false;
				_deprecated_argument( 'gc_editor()', '3.9.0', 'TinyMCE editor IDs cannot have brackets.' );
			}
		}

		self::$this_quicktags = (bool) $set['quicktags'];

		if ( self::$this_tinymce ) {
			self::$has_tinymce = true;
		}

		if ( self::$this_quicktags ) {
			self::$has_quicktags = true;
		}

		if ( empty( $set['editor_height'] ) ) {
			return $set;
		}

		if ( 'content' === $editor_id && empty( $set['tinymce']['gc_autoresize_on'] ) ) {
			// A cookie (set when a user resizes the editor) overrides the height.
			$cookie = (int) get_user_setting( 'ed_size' );

			if ( $cookie ) {
				$set['editor_height'] = $cookie;
			}
		}

		if ( $set['editor_height'] < 50 ) {
			$set['editor_height'] = 50;
		} elseif ( $set['editor_height'] > 5000 ) {
			$set['editor_height'] = 5000;
		}

		return $set;
	}

	/**
	 * Outputs the HTML for a single instance of the editor.
	 *
	 *
	 * @param string $content   Initial content for the editor.
	 * @param string $editor_id HTML ID for the textarea and TinyMCE and Quicktags instances.
	 *                          Should not contain square brackets.
	 * @param array  $settings  See _GC_Editors::parse_settings() for description.
	 */
	public static function editor( $content, $editor_id, $settings = array() ) {
		$set            = self::parse_settings( $editor_id, $settings );
		$editor_class   = ' class="' . trim( esc_attr( $set['editor_class'] ) . ' gc-editor-area' ) . '"';
		$tabindex       = $set['tabindex'] ? ' tabindex="' . (int) $set['tabindex'] . '"' : '';
		$default_editor = 'html';
		$buttons        = '';
		$autocomplete   = '';
		$editor_id_attr = esc_attr( $editor_id );

		if ( $set['drag_drop_upload'] ) {
			self::$drag_drop_upload = true;
		}

		if ( ! empty( $set['editor_height'] ) ) {
			$height = ' style="height: ' . (int) $set['editor_height'] . 'px"';
		} else {
			$height = ' rows="' . (int) $set['textarea_rows'] . '"';
		}

		if ( ! current_user_can( 'upload_files' ) ) {
			$set['media_buttons'] = false;
		}

		if ( self::$this_tinymce ) {
			$autocomplete = ' autocomplete="off"';

			if ( self::$this_quicktags ) {
				$default_editor = $set['default_editor'] ? $set['default_editor'] : gc_default_editor();
				// 'html' is used for the "Text" editor tab.
				if ( 'html' !== $default_editor ) {
					$default_editor = 'tinymce';
				}

				$buttons .= '<button type="button" id="' . $editor_id_attr . '-tmce" class="gc-switch-editor switch-tmce"' .
					' data-gc-editor-id="' . $editor_id_attr . '">' . _x( '可视化', 'Name for the Visual editor tab' ) . "</button>\n";
				$buttons .= '<button type="button" id="' . $editor_id_attr . '-html" class="gc-switch-editor switch-html"' .
					' data-gc-editor-id="' . $editor_id_attr . '">' . _x( '文本', 'Name for the Text editor tab (formerly HTML)' ) . "</button>\n";
			} else {
				$default_editor = 'tinymce';
			}
		}

		$switch_class = 'html' === $default_editor ? 'html-active' : 'tmce-active';
		$wrap_class   = 'gc-core-ui gc-editor-wrap ' . $switch_class;

		if ( $set['_content_editor_dfw'] ) {
			$wrap_class .= ' has-dfw';
		}

		echo '<div id="gc-' . $editor_id_attr . '-wrap" class="' . $wrap_class . '">';

		if ( self::$editor_buttons_css ) {
			gc_print_styles( 'editor-buttons' );
			self::$editor_buttons_css = false;
		}

		if ( ! empty( $set['editor_css'] ) ) {
			echo $set['editor_css'] . "\n";
		}

		if ( ! empty( $buttons ) || $set['media_buttons'] ) {
			echo '<div id="gc-' . $editor_id_attr . '-editor-tools" class="gc-editor-tools hide-if-no-js">';

			if ( $set['media_buttons'] ) {
				self::$has_medialib = true;

				if ( ! function_exists( 'media_buttons' ) ) {
					require ABSPATH . 'gc-admin/includes/media.php';
				}

				echo '<div id="gc-' . $editor_id_attr . '-media-buttons" class="gc-media-buttons">';

				/**
				 * Fires after the default media button(s) are displayed.
				 *
			
				 *
				 * @param string $editor_id Unique editor identifier, e.g. 'content'.
				 */
				do_action( 'media_buttons', $editor_id );
				echo "</div>\n";
			}

			echo '<div class="gc-editor-tabs">' . $buttons . "</div>\n";
			echo "</div>\n";
		}

		$quicktags_toolbar = '';

		if ( self::$this_quicktags ) {
			if ( 'content' === $editor_id && ! empty( $GLOBALS['current_screen'] ) && 'post' === $GLOBALS['current_screen']->base ) {
				$toolbar_id = 'ed_toolbar';
			} else {
				$toolbar_id = 'qt_' . $editor_id_attr . '_toolbar';
			}

			$quicktags_toolbar = '<div id="' . $toolbar_id . '" class="quicktags-toolbar hide-if-no-js"></div>';
		}

		/**
		 * Filters the HTML markup output that displays the editor.
		 *
		 *
		 * @param string $output Editor's HTML markup.
		 */
		$the_editor = apply_filters(
			'the_editor',
			'<div id="gc-' . $editor_id_attr . '-editor-container" class="gc-editor-container">' .
			$quicktags_toolbar .
			'<textarea' . $editor_class . $height . $tabindex . $autocomplete . ' cols="40" name="' . esc_attr( $set['textarea_name'] ) . '" ' .
			'id="' . $editor_id_attr . '">%s</textarea></div>'
		);

		// Prepare the content for the Visual or Text editor, only when TinyMCE is used (back-compat).
		if ( self::$this_tinymce ) {
			add_filter( 'the_editor_content', 'format_for_editor', 10, 2 );
		}

		/**
		 * Filters the default editor content.
		 *
		 *
		 * @param string $content        Default editor content.
		 * @param string $default_editor The default editor for the current user.
		 *                               Either 'html' or 'tinymce'.
		 */
		$content = apply_filters( 'the_editor_content', $content, $default_editor );

		// Remove the filter as the next editor on the same page may not need it.
		if ( self::$this_tinymce ) {
			remove_filter( 'the_editor_content', 'format_for_editor' );
		}

		// Back-compat for the `htmledit_pre` and `richedit_pre` filters.
		if ( 'html' === $default_editor && has_filter( 'htmledit_pre' ) ) {
			/** This filter is documented in gc-includes/deprecated.php */
			$content = apply_filters_deprecated( 'htmledit_pre', array( $content ), '4.3.0', 'format_for_editor' );
		} elseif ( 'tinymce' === $default_editor && has_filter( 'richedit_pre' ) ) {
			/** This filter is documented in gc-includes/deprecated.php */
			$content = apply_filters_deprecated( 'richedit_pre', array( $content ), '4.3.0', 'format_for_editor' );
		}

		if ( false !== stripos( $content, 'textarea' ) ) {
			$content = preg_replace( '%</textarea%i', '&lt;/textarea', $content );
		}

		printf( $the_editor, $content );
		echo "\n</div>\n\n";

		self::editor_settings( $editor_id, $set );
	}

	/**
	 *
	 * @param string $editor_id Unique editor identifier, e.g. 'content'.
	 * @param array  $set       Array of editor arguments.
	 */
	public static function editor_settings( $editor_id, $set ) {
		if ( empty( self::$first_init ) ) {
			if ( is_admin() ) {
				add_action( 'admin_print_footer_scripts', array( __CLASS__, 'editor_js' ), 50 );
				add_action( 'admin_print_footer_scripts', array( __CLASS__, 'force_uncompressed_tinymce' ), 1 );
				add_action( 'admin_print_footer_scripts', array( __CLASS__, 'enqueue_scripts' ), 1 );
			} else {
				add_action( 'gc_print_footer_scripts', array( __CLASS__, 'editor_js' ), 50 );
				add_action( 'gc_print_footer_scripts', array( __CLASS__, 'force_uncompressed_tinymce' ), 1 );
				add_action( 'gc_print_footer_scripts', array( __CLASS__, 'enqueue_scripts' ), 1 );
			}
		}

		if ( self::$this_quicktags ) {

			$qtInit = array(
				'id'      => $editor_id,
				'buttons' => '',
			);

			if ( is_array( $set['quicktags'] ) ) {
				$qtInit = array_merge( $qtInit, $set['quicktags'] );
			}

			if ( empty( $qtInit['buttons'] ) ) {
				$qtInit['buttons'] = 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close';
			}

			if ( $set['_content_editor_dfw'] ) {
				$qtInit['buttons'] .= ',dfw';
			}

			/**
			 * Filters the Quicktags settings.
			 *
		
			 *
			 * @param array  $qtInit    Quicktags settings.
			 * @param string $editor_id Unique editor identifier, e.g. 'content'.
			 */
			$qtInit = apply_filters( 'quicktags_settings', $qtInit, $editor_id );

			self::$qt_settings[ $editor_id ] = $qtInit;

			self::$qt_buttons = array_merge( self::$qt_buttons, explode( ',', $qtInit['buttons'] ) );
		}

		if ( self::$this_tinymce ) {

			if ( empty( self::$first_init ) ) {
				$baseurl     = self::get_baseurl();
				$mce_locale  = self::get_mce_locale();
				$ext_plugins = '';

				if ( $set['teeny'] ) {

					/**
					 * Filters the list of teenyMCE plugins.
					 *
				
				
					 *
					 * @param array  $plugins   An array of teenyMCE plugins.
					 * @param string $editor_id Unique editor identifier, e.g. 'content'.
					 */
					$plugins = apply_filters(
						'teeny_mce_plugins',
						array(
							'colorpicker',
							'lists',
							'fullscreen',
							'image',
							'gechiui',
							'gceditimage',
							'gclink',
						),
						$editor_id
					);
				} else {

					/**
					 * Filters the list of TinyMCE external plugins.
					 *
					 * The filter takes an associative array of external plugins for
					 * TinyMCE in the form 'plugin_name' => 'url'.
					 *
					 * The url should be absolute, and should include the js filename
					 * to be loaded. For example:
					 * 'myplugin' => 'http://mysite.com/gc-content/plugins/myfolder/mce_plugin.js'.
					 *
					 * If the external plugin adds a button, it should be added with
					 * one of the 'mce_buttons' filters.
					 *
				
				
					 *
					 * @param array  $external_plugins An array of external TinyMCE plugins.
					 * @param string $editor_id        Unique editor identifier, e.g. 'content'. Accepts 'classic-block'
					 *                                 when called from block editor's Classic block.
					 */
					$mce_external_plugins = apply_filters( 'mce_external_plugins', array(), $editor_id );

					$plugins = array(
						'charmap',
						'colorpicker',
						'hr',
						'lists',
						'media',
						'paste',
						'tabfocus',
						'textcolor',
						'fullscreen',
						'gechiui',
						'gcautoresize',
						'gceditimage',
						'gcemoji',
						'gcgallery',
						'gclink',
						'gcdialogs',
						'gctextpattern',
						'gcview',
					);

					if ( ! self::$has_medialib ) {
						$plugins[] = 'image';
					}

					/**
					 * Filters the list of default TinyMCE plugins.
					 *
					 * The filter specifies which of the default plugins included
					 * in GeChiUI should be added to the TinyMCE instance.
					 *
				
				
					 *
					 * @param array  $plugins   An array of default TinyMCE plugins.
					 * @param string $editor_id Unique editor identifier, e.g. 'content'. Accepts 'classic-block'
					 *                          when called from block editor's Classic block.
					 */
					$plugins = array_unique( apply_filters( 'tiny_mce_plugins', $plugins, $editor_id ) );

					$key = array_search( 'spellchecker', $plugins, true );
					if ( false !== $key ) {
						// Remove 'spellchecker' from the internal plugins if added with 'tiny_mce_plugins' filter to prevent errors.
						// It can be added with 'mce_external_plugins'.
						unset( $plugins[ $key ] );
					}

					if ( ! empty( $mce_external_plugins ) ) {

						/**
						 * Filters the translations loaded for external TinyMCE 3.x plugins.
						 *
						 * The filter takes an associative array ('plugin_name' => 'path')
						 * where 'path' is the include path to the file.
						 *
						 * The language file should follow the same format as gc_mce_translation(),
						 * and should define a variable ($strings) that holds all translated strings.
						 *
					
					
						 *
						 * @param array  $translations Translations for external TinyMCE plugins.
						 * @param string $editor_id    Unique editor identifier, e.g. 'content'.
						 */
						$mce_external_languages = apply_filters( 'mce_external_languages', array(), $editor_id );

						$loaded_langs = array();
						$strings      = '';

						if ( ! empty( $mce_external_languages ) ) {
							foreach ( $mce_external_languages as $name => $path ) {
								if ( @is_file( $path ) && @is_readable( $path ) ) {
									include_once $path;
									$ext_plugins   .= $strings . "\n";
									$loaded_langs[] = $name;
								}
							}
						}

						foreach ( $mce_external_plugins as $name => $url ) {
							if ( in_array( $name, $plugins, true ) ) {
								unset( $mce_external_plugins[ $name ] );
								continue;
							}

							$url                           = set_url_scheme( $url );
							$mce_external_plugins[ $name ] = $url;
							$plugurl                       = dirname( $url );
							$strings                       = '';

							// Try to load langs/[locale].js and langs/[locale]_dlg.js.
							if ( ! in_array( $name, $loaded_langs, true ) ) {
								$path = str_replace( content_url(), '', $plugurl );
								$path = GC_CONTENT_DIR . $path . '/langs/';

								$path = trailingslashit( realpath( $path ) );

								if ( @is_file( $path . $mce_locale . '.js' ) ) {
									$strings .= @file_get_contents( $path . $mce_locale . '.js' ) . "\n";
								}

								if ( @is_file( $path . $mce_locale . '_dlg.js' ) ) {
									$strings .= @file_get_contents( $path . $mce_locale . '_dlg.js' ) . "\n";
								}

								if ( 'en' !== $mce_locale && empty( $strings ) ) {
									if ( @is_file( $path . 'en.js' ) ) {
										$str1     = @file_get_contents( $path . 'en.js' );
										$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str1, 1 ) . "\n";
									}

									if ( @is_file( $path . 'en_dlg.js' ) ) {
										$str2     = @file_get_contents( $path . 'en_dlg.js' );
										$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str2, 1 ) . "\n";
									}
								}

								if ( ! empty( $strings ) ) {
									$ext_plugins .= "\n" . $strings . "\n";
								}
							}

							$ext_plugins .= 'tinyMCEPreInit.load_ext("' . $plugurl . '", "' . $mce_locale . '");' . "\n";
						}
					}
				}

				self::$plugins     = $plugins;
				self::$ext_plugins = $ext_plugins;

				$settings            = self::default_settings();
				$settings['plugins'] = implode( ',', $plugins );

				if ( ! empty( $mce_external_plugins ) ) {
					$settings['external_plugins'] = gc_json_encode( $mce_external_plugins );
				}

				/** This filter is documented in gc-admin/includes/media.php */
				if ( apply_filters( 'disable_captions', '' ) ) {
					$settings['gceditimage_disable_captions'] = true;
				}

				$mce_css = $settings['content_css'];

				/*
				 * The `editor-style.css` added by the theme is generally intended for the editor instance on the Edit Post screen.
				 * Plugins that use gc_editor() on the front-end can decide whether to add the theme stylesheet
				 * by using `get_editor_stylesheets()` and the `mce_css` or `tiny_mce_before_init` filters, see below.
				 */
				if ( is_admin() ) {
					$editor_styles = get_editor_stylesheets();

					if ( ! empty( $editor_styles ) ) {
						// Force urlencoding of commas.
						foreach ( $editor_styles as $key => $url ) {
							if ( strpos( $url, ',' ) !== false ) {
								$editor_styles[ $key ] = str_replace( ',', '%2C', $url );
							}
						}

						$mce_css .= ',' . implode( ',', $editor_styles );
					}
				}

				/**
				 * Filters the comma-delimited list of stylesheets to load in TinyMCE.
				 *
			
				 *
				 * @param string $stylesheets Comma-delimited list of stylesheets.
				 */
				$mce_css = trim( apply_filters( 'mce_css', $mce_css ), ' ,' );

				if ( ! empty( $mce_css ) ) {
					$settings['content_css'] = $mce_css;
				} else {
					unset( $settings['content_css'] );
				}

				self::$first_init = $settings;
			}

			if ( $set['teeny'] ) {
				$mce_buttons = array(
					'bold',
					'italic',
					'underline',
					'blockquote',
					'strikethrough',
					'bullist',
					'numlist',
					'alignleft',
					'aligncenter',
					'alignright',
					'undo',
					'redo',
					'link',
					'fullscreen',
				);

				/**
				 * Filters the list of teenyMCE buttons (Text tab).
				 *
			
			
				 *
				 * @param array  $mce_buttons An array of teenyMCE buttons.
				 * @param string $editor_id   Unique editor identifier, e.g. 'content'.
				 */
				$mce_buttons   = apply_filters( 'teeny_mce_buttons', $mce_buttons, $editor_id );
				$mce_buttons_2 = array();
				$mce_buttons_3 = array();
				$mce_buttons_4 = array();
			} else {
				$mce_buttons = array(
					'formatselect',
					'bold',
					'italic',
					'bullist',
					'numlist',
					'blockquote',
					'alignleft',
					'aligncenter',
					'alignright',
					'link',
					'gc_more',
					'spellchecker',
				);

				if ( ! gc_is_mobile() ) {
					if ( $set['_content_editor_dfw'] ) {
						$mce_buttons[] = 'gc_adv';
						$mce_buttons[] = 'dfw';
					} else {
						$mce_buttons[] = 'fullscreen';
						$mce_buttons[] = 'gc_adv';
					}
				} else {
					$mce_buttons[] = 'gc_adv';
				}

				/**
				 * Filters the first-row list of TinyMCE buttons (Visual tab).
				 *
			
			
				 *
				 * @param array  $mce_buttons First-row list of buttons.
				 * @param string $editor_id   Unique editor identifier, e.g. 'content'. Accepts 'classic-block'
				 *                            when called from block editor's Classic block.
				 */
				$mce_buttons = apply_filters( 'mce_buttons', $mce_buttons, $editor_id );

				$mce_buttons_2 = array(
					'strikethrough',
					'hr',
					'forecolor',
					'pastetext',
					'removeformat',
					'charmap',
					'outdent',
					'indent',
					'undo',
					'redo',
				);

				if ( ! gc_is_mobile() ) {
					$mce_buttons_2[] = 'gc_help';
				}

				/**
				 * Filters the second-row list of TinyMCE buttons (Visual tab).
				 *
			
			
				 *
				 * @param array  $mce_buttons_2 Second-row list of buttons.
				 * @param string $editor_id     Unique editor identifier, e.g. 'content'. Accepts 'classic-block'
				 *                              when called from block editor's Classic block.
				 */
				$mce_buttons_2 = apply_filters( 'mce_buttons_2', $mce_buttons_2, $editor_id );

				/**
				 * Filters the third-row list of TinyMCE buttons (Visual tab).
				 *
			
			
				 *
				 * @param array  $mce_buttons_3 Third-row list of buttons.
				 * @param string $editor_id     Unique editor identifier, e.g. 'content'. Accepts 'classic-block'
				 *                              when called from block editor's Classic block.
				 */
				$mce_buttons_3 = apply_filters( 'mce_buttons_3', array(), $editor_id );

				/**
				 * Filters the fourth-row list of TinyMCE buttons (Visual tab).
				 *
			
			
				 *
				 * @param array  $mce_buttons_4 Fourth-row list of buttons.
				 * @param string $editor_id     Unique editor identifier, e.g. 'content'. Accepts 'classic-block'
				 *                              when called from block editor's Classic block.
				 */
				$mce_buttons_4 = apply_filters( 'mce_buttons_4', array(), $editor_id );
			}

			$body_class = $editor_id;

			$post = get_post();
			if ( $post ) {
				$body_class .= ' post-type-' . sanitize_html_class( $post->post_type ) . ' post-status-' . sanitize_html_class( $post->post_status );

				if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
					$post_format = get_post_format( $post );
					if ( $post_format && ! is_gc_error( $post_format ) ) {
						$body_class .= ' post-format-' . sanitize_html_class( $post_format );
					} else {
						$body_class .= ' post-format-standard';
					}
				}

				$page_template = get_page_template_slug( $post );

				if ( false !== $page_template ) {
					$page_template = empty( $page_template ) ? 'default' : str_replace( '.', '-', basename( $page_template, '.php' ) );
					$body_class   .= ' page-template-' . sanitize_html_class( $page_template );
				}
			}

			$body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_user_locale() ) ) );

			if ( ! empty( $set['tinymce']['body_class'] ) ) {
				$body_class .= ' ' . $set['tinymce']['body_class'];
				unset( $set['tinymce']['body_class'] );
			}

			$mceInit = array(
				'selector'          => "#$editor_id",
				'gcautop'           => (bool) $set['gcautop'],
				'indent'            => ! $set['gcautop'],
				'toolbar1'          => implode( ',', $mce_buttons ),
				'toolbar2'          => implode( ',', $mce_buttons_2 ),
				'toolbar3'          => implode( ',', $mce_buttons_3 ),
				'toolbar4'          => implode( ',', $mce_buttons_4 ),
				'tabfocus_elements' => $set['tabfocus_elements'],
				'body_class'        => $body_class,
			);

			// Merge with the first part of the init array.
			$mceInit = array_merge( self::$first_init, $mceInit );

			if ( is_array( $set['tinymce'] ) ) {
				$mceInit = array_merge( $mceInit, $set['tinymce'] );
			}

			/*
			 * For people who really REALLY know what they're doing with TinyMCE
			 * You can modify $mceInit to add, remove, change elements of the config
			 * before tinyMCE.init. Setting "valid_elements", "invalid_elements"
			 * and "extended_valid_elements" can be done through this filter. Best
			 * is to use the default cleanup by not specifying valid_elements,
			 * as TinyMCE checks against the full set of HTML 5.0 elements and attributes.
			 */
			if ( $set['teeny'] ) {

				/**
				 * Filters the teenyMCE config before init.
				 *
			
			
				 *
				 * @param array  $mceInit   An array with teenyMCE config.
				 * @param string $editor_id Unique editor identifier, e.g. 'content'.
				 */
				$mceInit = apply_filters( 'teeny_mce_before_init', $mceInit, $editor_id );
			} else {

				/**
				 * Filters the TinyMCE config before init.
				 *
			
			
				 *
				 * @param array  $mceInit   An array with TinyMCE config.
				 * @param string $editor_id Unique editor identifier, e.g. 'content'. Accepts 'classic-block'
				 *                          when called from block editor's Classic block.
				 */
				$mceInit = apply_filters( 'tiny_mce_before_init', $mceInit, $editor_id );
			}

			if ( empty( $mceInit['toolbar3'] ) && ! empty( $mceInit['toolbar4'] ) ) {
				$mceInit['toolbar3'] = $mceInit['toolbar4'];
				$mceInit['toolbar4'] = '';
			}

			self::$mce_settings[ $editor_id ] = $mceInit;
		} // End if self::$this_tinymce.
	}

	/**
	 *
	 * @param array $init
	 * @return string
	 */
	private static function _parse_init( $init ) {
		$options = '';

		foreach ( $init as $key => $value ) {
			if ( is_bool( $value ) ) {
				$val      = $value ? 'true' : 'false';
				$options .= $key . ':' . $val . ',';
				continue;
			} elseif ( ! empty( $value ) && is_string( $value ) && (
				( '{' === $value[0] && '}' === $value[ strlen( $value ) - 1 ] ) ||
				( '[' === $value[0] && ']' === $value[ strlen( $value ) - 1 ] ) ||
				preg_match( '/^\(?function ?\(/', $value ) ) ) {

				$options .= $key . ':' . $value . ',';
				continue;
			}
			$options .= $key . ':"' . $value . '",';
		}

		return '{' . trim( $options, ' ,' ) . '}';
	}

	/**
	 *
	 * @param bool $default_scripts Optional. Whether default scripts should be enqueued. Default false.
	 */
	public static function enqueue_scripts( $default_scripts = false ) {
		if ( $default_scripts || self::$has_tinymce ) {
			gc_enqueue_script( 'editor' );
		}

		if ( $default_scripts || self::$has_quicktags ) {
			gc_enqueue_script( 'quicktags' );
			gc_enqueue_style( 'buttons' );
		}

		if ( $default_scripts || in_array( 'gclink', self::$plugins, true ) || in_array( 'link', self::$qt_buttons, true ) ) {
			gc_enqueue_script( 'gclink' );
			gc_enqueue_script( 'jquery-ui-autocomplete' );
		}

		if ( self::$has_medialib ) {
			add_thickbox();
			gc_enqueue_script( 'media-upload' );
			gc_enqueue_script( 'gc-embed' );
		} elseif ( $default_scripts ) {
			gc_enqueue_script( 'media-upload' );
		}

		/**
		 * Fires when scripts and styles are enqueued for the editor.
		 *
		 *
		 * @param array $to_load An array containing boolean values whether TinyMCE
		 *                       and Quicktags are being loaded.
		 */
		do_action(
			'gc_enqueue_editor',
			array(
				'tinymce'   => ( $default_scripts || self::$has_tinymce ),
				'quicktags' => ( $default_scripts || self::$has_quicktags ),
			)
		);
	}

	/**
	 * Enqueue all editor scripts.
	 * For use when the editor is going to be initialized after page load.
	 *
	 */
	public static function enqueue_default_editor() {
		// We are past the point where scripts can be enqueued properly.
		if ( did_action( 'gc_enqueue_editor' ) ) {
			return;
		}

		self::enqueue_scripts( true );

		// Also add gc-includes/css/editor.css.
		gc_enqueue_style( 'editor-buttons' );

		if ( is_admin() ) {
			add_action( 'admin_print_footer_scripts', array( __CLASS__, 'force_uncompressed_tinymce' ), 1 );
			add_action( 'admin_print_footer_scripts', array( __CLASS__, 'print_default_editor_scripts' ), 45 );
		} else {
			add_action( 'gc_print_footer_scripts', array( __CLASS__, 'force_uncompressed_tinymce' ), 1 );
			add_action( 'gc_print_footer_scripts', array( __CLASS__, 'print_default_editor_scripts' ), 45 );
		}
	}

	/**
	 * Print (output) all editor scripts and default settings.
	 * For use when the editor is going to be initialized after page load.
	 *
	 */
	public static function print_default_editor_scripts() {
		$user_can_richedit = user_can_richedit();

		if ( $user_can_richedit ) {
			$settings = self::default_settings();

			$settings['toolbar1']    = 'bold,italic,bullist,numlist,link';
			$settings['gcautop']     = false;
			$settings['indent']      = true;
			$settings['elementpath'] = false;

			if ( is_rtl() ) {
				$settings['directionality'] = 'rtl';
			}

			/*
			 * In production all plugins are loaded (they are in gc-editor.js.gz).
			 * The 'gcview', 'gcdialogs', and 'media' TinyMCE plugins are not initialized by default.
			 * Can be added from js by using the 'gc-before-tinymce-init' event.
			 */
			$settings['plugins'] = implode(
				',',
				array(
					'charmap',
					'colorpicker',
					'hr',
					'lists',
					'paste',
					'tabfocus',
					'textcolor',
					'fullscreen',
					'gechiui',
					'gcautoresize',
					'gceditimage',
					'gcemoji',
					'gcgallery',
					'gclink',
					'gctextpattern',
				)
			);

			$settings = self::_parse_init( $settings );
		} else {
			$settings = '{}';
		}

		?>
		<script type="text/javascript">
		window.gc = window.gc || {};
		window.gc.editor = window.gc.editor || {};
		window.gc.editor.getDefaultSettings = function() {
			return {
				tinymce: <?php echo $settings; ?>,
				quicktags: {
					buttons: 'strong,em,link,ul,ol,li,code'
				}
			};
		};

		<?php

		if ( $user_can_richedit ) {
			$suffix  = SCRIPT_DEBUG ? '' : '.min';
			$baseurl = self::get_baseurl();

			?>
			var tinyMCEPreInit = {
				baseURL: "<?php echo $baseurl; ?>",
				suffix: "<?php echo $suffix; ?>",
				mceInit: {},
				qtInit: {},
				load_ext: function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
			};
			<?php
		}
		?>
		</script>
		<?php

		if ( $user_can_richedit ) {
			self::print_tinymce_scripts();
		}

		/**
		 * Fires when the editor scripts are loaded for later initialization,
		 * after all scripts and settings are printed.
		 *
		 */
		do_action( 'print_default_editor_scripts' );

		self::gc_link_dialog();
	}

	/**
	 * Returns the TinyMCE locale.
	 *
	 *
	 * @return string
	 */
	public static function get_mce_locale() {
		if ( empty( self::$mce_locale ) ) {
			$mce_locale       = get_user_locale();
			self::$mce_locale = empty( $mce_locale ) ? 'zh_CN' : strtolower( substr( $mce_locale, 0, 2 ) ); // ISO 639-1.
		}

		return self::$mce_locale;
	}

	/**
	 * Returns the TinyMCE base URL.
	 *
	 *
	 * @return string
	 */
	public static function get_baseurl() {
		if ( empty( self::$baseurl ) ) {
			self::$baseurl =  assets_url( '/vendors/tinymce' );
		}

		return self::$baseurl;
	}

	/**
	 * Returns the default TinyMCE settings.
	 * Doesn't include plugins, buttons, editor selector.
	 *
	 *
	 * @global string $tinymce_version
	 *
	 * @return array
	 */
	private static function default_settings() {
		global $tinymce_version;

		$shortcut_labels = array();

		foreach ( self::get_translation() as $name => $value ) {
			if ( is_array( $value ) ) {
				$shortcut_labels[ $name ] = $value[1];
			}
		}

		$settings = array(
			'theme'                        => 'modern',
			'skin'                         => 'lightgray',
			'language'                     => self::get_mce_locale(),
			'formats'                      => '{' .
				'alignleft: [' .
					'{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"left"}},' .
					'{selector: "img,table,dl.gc-caption", classes: "alignleft"}' .
				'],' .
				'aligncenter: [' .
					'{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"center"}},' .
					'{selector: "img,table,dl.gc-caption", classes: "aligncenter"}' .
				'],' .
				'alignright: [' .
					'{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"right"}},' .
					'{selector: "img,table,dl.gc-caption", classes: "alignright"}' .
				'],' .
				'strikethrough: {inline: "del"}' .
			'}',
			'relative_urls'                => false,
			'remove_script_host'           => false,
			'convert_urls'                 => false,
			'browser_spellcheck'           => true,
			'fix_list_elements'            => true,
			'entities'                     => '38,amp,60,lt,62,gt',
			'entity_encoding'              => 'raw',
			'keep_styles'                  => false,
			'cache_suffix'                 => 'gc-mce-' . $tinymce_version,
			'resize'                       => 'vertical',
			'menubar'                      => false,
			'branding'                     => false,

			// Limit the preview styles in the menu/toolbar.
			'preview_styles'               => 'font-family font-size font-weight font-style text-decoration text-transform',

			'end_container_on_empty_block' => true,
			'gceditimage_html5_captions'   => true,
			'gc_lang_attr'                 => get_bloginfo( 'language' ),
			'gc_keep_scroll_position'      => false,
			'gc_shortcut_labels'           => gc_json_encode( $shortcut_labels ),
		);

		$suffix  = SCRIPT_DEBUG ? '' : '.min';
		$version = 'ver=' . get_bloginfo( 'version' );

		// Default stylesheets.
		$settings['content_css'] = assets_url( "/css/dashicons$suffix.css?$version" ) . ',' .
		assets_url( "/vendors/tinymce/skins/gechiui/gc-content.css?$version" );

		return $settings;
	}

	/**
	 *
	 * @return array
	 */
	private static function get_translation() {
		if ( empty( self::$translation ) ) {
			self::$translation = array(
				// Default TinyMCE strings.
				'新文档'                         => __( '新文档' ),
				'Formats'                              => _x( '格式', 'TinyMCE' ),

				'Headings'                             => _x( '标题', 'TinyMCE' ),
				'一级标题'                            => array( __( '一级标题' ), 'access1' ),
				'二级标题'                            => array( __( '二级标题' ), 'access2' ),
				'三级标题'                            => array( __( '三级标题' ), 'access3' ),
				'四级标题'                            => array( __( '四级标题' ), 'access4' ),
				'五级标题'                            => array( __( '五级标题' ), 'access5' ),
				'六级标题'                            => array( __( '六级标题' ), 'access6' ),

				/* translators: Block tags. */
				'Blocks'                               => _x( '块', 'TinyMCE' ),
				'段落'                            => array( __( '段落' ), 'access7' ),
				'段落引用'                           => array( __( '段落引用' ), 'accessQ' ),
				'Div'                                  => _x( 'Div', 'HTML tag' ),
				'Pre'                                  => _x( 'Pre', 'HTML tag' ),
				'预格式'                         => _x( '预格式', 'HTML tag' ),
				'Address'                              => _x( '地址', 'HTML tag' ),

				'Inline'                               => _x( '行内', 'HTML elements' ),
				'下划线'                            => array( __( '下划线' ), 'metaU' ),
				'删除线'                        => array( __( '删除线' ), 'accessD' ),
				'下标'                            => __( '下标' ),
				'上标'                          => __( '上标' ),
				'清除格式'                     => __( '清除格式' ),
				'Bold'                                 => array( __( '粗体' ), 'metaB' ),
				'Italic'                               => array( __( '斜体' ), 'metaI' ),
				'Code'                                 => array( __( '代码' ), 'accessX' ),
				'源代码'                          => __( '源代码' ),
				'字体'                          => __( '字体' ),
				'字号'                           => __( '字号' ),

				'居中对齐'                         => array( __( '居中对齐' ), 'accessC' ),
				'右对齐'                          => array( __( '右对齐' ), 'accessR' ),
				'左对齐'                           => array( __( '左对齐' ), 'accessL' ),
				'Justify'                              => array( __( '两端对齐' ), 'accessJ' ),
				'增加缩进量'                      => __( '增加缩进量' ),
				'减少缩进量'                      => __( '减少缩进量' ),

				'Cut'                                  => array( __( '剪切' ), 'metaX' ),
				'Copy'                                 => array( __( '复制' ), 'metaC' ),
				'Paste'                                => array( __( '粘帖' ), 'metaV' ),
				'全选'                           => array( __( '全选' ), 'metaA' ),
				'Undo'                                 => array( __( '撤销' ), 'metaZ' ),
				'Redo'                                 => array( __( '重做' ), 'metaY' ),

				'Ok'                                   => __( '确定' ),
				'Cancel'                               => __( '取消' ),
				'Close'                                => __( '关闭' ),
				'视觉辅助'                          => __( '视觉辅助' ),

				'Bullet list'                          => array( __( '项目符号列表' ), 'accessU' ),
				'编号列表'                        => array( __( '编号列表' ), 'accessO' ),
				'Square'                               => _x( '实心方块', 'list style' ),
				'Default'                              => _x( '默认', 'list style' ),
				'Circle'                               => _x( '圆圈', 'list style' ),
				'Disc'                                 => _x( '圆点', 'list style' ),
				'小写希腊字母'                          => _x( '小写希腊字母', 'list style' ),
				'小写英文字母'                          => _x( '小写英文字母', 'list style' ),
				'大写英文字母'                          => _x( '大写英文字母', 'list style' ),
				'大写罗马数字'                          => _x( '大写罗马数字', 'list style' ),
				'小写罗马数字'                          => _x( '小写罗马数字', 'list style' ),

				// Anchor plugin.
				'Name'                                 => _x( '名称', 'Name of link anchor (TinyMCE)' ),
				'Anchor'                               => _x( '锚', 'Link anchor (TinyMCE)' ),
				'Anchors'                              => _x( '锚', 'Link anchors (TinyMCE)' ),
				'Id应以字体开头，后面可以是字母、数字、短横线、点、冒号或下划线。' =>
					__( 'Id应以字体开头，后面可以是字母、数字、短横线、点、冒号或下划线。' ),
				'Id'                                   => _x( 'Id', 'Id for link anchor (TinyMCE)' ),

				// Fullpage plugin.
				'文档属性'                  => __( '文档属性' ),
				'Robots'                               => __( '机器人' ),
				'Title'                                => __( '标题' ),
				'Keywords'                             => __( '关键字' ),
				'Encoding'                             => __( '编码' ),
				'description'                          => __( '描述' ),
				'Author'                               => __( '作者' ),

				// Media, image plugins.
				'Image'                                => __( '图片' ),
				'插入或编辑图片'                    => array( __( '插入或编辑图片' ), 'accessM' ),
				'General'                              => __( '常规' ),
				'Advanced'                             => __( '高级' ),
				'Source'                               => __( '源' ),
				'Border'                               => __( '边框' ),
				'保持长宽比'                => __( '保持长宽比' ),
				'垂直间隔'                       => __( '垂直间隔' ),
				'图片说明'                    => __( '图片说明' ),
				'Style'                                => __( '样式' ),
				'尺寸'                           => __( '尺寸' ),
				'插入图片'                         => __( '插入图片' ),
				'日期/时间'                            => __( '日期/时间' ),
				'插入日期、时间'                     => __( '插入日期、时间' ),
				'目录'                    => __( '目录' ),
				'Insert/Edit code sample'              => __( '插入/编辑代码片段' ),
				'Language'                             => __( '语言' ),
				'Media'                                => __( '媒体' ),
				'插入/编辑媒体'                    => __( '插入/编辑媒体' ),
				'Poster'                               => __( '海报' ),
				'备用源'                   => __( '备用源' ),
				'请将嵌入代码贴入下方：'         => __( '请将嵌入代码贴入下方：' ),
				'插入视频'                         => __( '插入视频' ),
				'Embed'                                => __( '嵌入' ),

				// Each of these have a corresponding plugin.
				'特殊字符'                    => __( '特殊字符' ),
				'从右到左'                        => _x( '从右到左', 'editor button' ),
				'从左到右'                        => _x( '从左到右', 'editor button' ),
				'表情符号'                            => __( '表情符号' ),
				'不间断空格'                    => __( '不间断空格' ),
				'分页符'                           => __( '分页符' ),
				'粘贴为文本'                        => __( '粘贴为文本' ),
				'Preview'                              => __( '预览' ),
				'Print'                                => __( '打印' ),
				'Save'                                 => __( '保存' ),
				'全屏'                           => __( '全屏' ),
				'水平线'                      => __( '水平线' ),
				'水平间隔'                     => __( '水平间隔' ),
				'恢复上一草稿'                   => __( '恢复上一草稿' ),
				'插入或编辑链接'                     => array( __( '插入或编辑链接' ), 'metaK' ),
				'移除链接'                          => array( __( '移除链接' ), 'accessS' ),

				// Link plugin.
				'Link'                                 => __( '链接' ),
				'插入链接'                          => __( '插入链接' ),
				'Target'                               => __( '打开方式' ),
				'新窗口'                           => __( '新窗口' ),
				'显示文本'                      => __( '显示文本' ),
				'Url'                                  => __( 'URL' ),
				'您输入的网址似乎是个电子邮箱，您要自动加上mailto:​前缀吗？' =>
					__( '您输入的网址似乎是个电子邮箱，您要自动加上mailto:​前缀吗？' ),
				'您输入的链接似乎是个外部地址，您要自动加上http://​前缀吗？' =>
					__( '您输入的链接似乎是个外部地址，您要自动加上http://​前缀吗？' ),

				'Color'                                => __( '颜色' ),
				'自定义颜色'                         => __( '自定义颜色' ),
				'自定义…'                            => _x( '自定义…', 'label for custom color' ), // No ellipsis.
				'No color'                             => __( '无颜色' ),
				'R'                                    => _x( 'R', 'Short for red in RGB' ),
				'G'                                    => _x( 'G', 'Short for green in RGB' ),
				'B'                                    => _x( 'B', 'Short for blue in RGB' ),

				// Spelling, search/replace plugins.
				'无法找到指定的字符串。' => __( '无法找到指定的字符串。' ),
				'Replace'                              => _x( '替换', 'find/replace' ),
				'Next'                                 => _x( '下一个', 'find/replace' ),
				/* translators: Previous. */
				'Prev'                                 => _x( '上一个', 'find/replace' ),
				'匹配整词'                          => _x( '匹配整词', 'find/replace' ),
				'查找和替换'                     => __( '查找和替换' ),
				'替换为'                         => _x( '替换为', 'find/replace' ),
				'Find'                                 => _x( '查找', 'find/replace' ),
				'全部替换'                          => _x( '全部替换', 'find/replace' ),
				'匹配大小写'                           => __( '匹配大小写' ),
				'Spellcheck'                           => __( '拼写检查' ),
				'Finish'                               => _x( '完成', 'spellcheck' ),
				'全部忽略'                           => _x( '全部忽略', 'spellcheck' ),
				'Ignore'                               => _x( '忽略', 'spellcheck' ),
				'添加至词典'                    => __( '添加至词典' ),

				// TinyMCE tables.
				'插入表格'                         => __( '插入表格' ),
				'删除表格'                         => __( '删除表格' ),
				'表格属性'                     => __( '表格属性' ),
				'Row properties'                       => __( '表格行属性' ),
				'Cell properties'                      => __( '单元格属性' ),
				'边框颜色'                         => __( '边框颜色' ),

				'Row'                                  => __( '行' ),
				'Rows'                                 => __( '行' ),
				'Column'                               => __( '单衣栏目' ),
				'Cols'                                 => __( '栏目' ),
				'Cell'                                 => _x( '单元格', 'table cell' ),
				'表头单元格'                          => __( '表头单元格' ),
				'Header'                               => _x( '表头', 'table header' ),
				'Body'                                 => _x( '主体', 'table body' ),
				'Footer'                               => _x( '注脚', 'table footer' ),

				'在上方插入行'                    => __( '在上方插入行' ),
				'在下方插入行'                     => __( '在下方插入行' ),
				'在前方插入列'                 => __( '在前方插入列' ),
				'在后方插入列'                  => __( '在后方插入列' ),
				'Paste row before'                     => __( '在上方粘贴表格行' ),
				'Paste row after'                      => __( '在下方粘贴表格行' ),
				'删除行'                           => __( '删除行' ),
				'删除列'                        => __( '删除列' ),
				'Cut row'                              => __( '剪切该行' ),
				'Copy row'                             => __( '复制该行' ),
				'Merge cells'                          => __( '合并单元格' ),
				'Split cell'                           => __( '拆分单元格' ),

				'Height'                               => __( '高度' ),
				'Width'                                => __( '宽度' ),
				'Caption'                              => __( '说明文字' ),
				'对齐方式'                            => __( '对齐方式' ),
				'H Align'                              => _x( '横向对齐', 'horizontal table cell alignment' ),
				'Left'                                 => __( '左' ),
				'Center'                               => __( '中' ),
				'Right'                                => __( '右' ),
				'None'                                 => _x( '无', 'table cell alignment attribute' ),
				'V Align'                              => _x( '纵向对齐', 'vertical table cell alignment' ),
				'Top'                                  => __( '顶部' ),
				'Middle'                               => __( '中部' ),
				'Bottom'                               => __( '底部' ),

				'行组'                            => __( '行组' ),
				'栏目组合'                         => __( '栏目组合' ),
				'Row type'                             => __( '行类型' ),
				'单元格类型'                            => __( '单元格类型' ),
				'单元格内边距'                         => __( '单元格内边距' ),
				'单元格间距'                         => __( '单元格间距' ),
				'Scope'                                => _x( '范围', 'table cell scope attribute' ),

				'插入模板'                      => _x( '插入模板', 'TinyMCE' ),
				'模板'                            => _x( '模板', 'TinyMCE' ),

				'背景颜色'                     => __( '背景颜色' ),
				'文字颜色'                           => __( '文字颜色' ),
				'显示块'                          => _x( '显示块', 'editor button' ),
				'显示不可见字符'            => __( '显示不可见字符' ),

				/* translators: Word count. */
				'Words: {0}'                           => sprintf( __( '词数：%s' ), '{0}' ),
				'当前处于纯文本粘贴模式，粘贴的内容将被视作纯文本。' =>
					__( '当前处于纯文本粘贴模式，粘贴的内容将被视作纯文本。' ) . "\n\n" .
					__( '如果您希望从Microsoft Word粘贴富文本内容，请将此选项关闭。编辑器将自动清理从Word粘贴来的文本。' ),
				'Rich Text Area. Press ALT-F9 for menu. Press ALT-F10 for toolbar. Press ALT-0 for help' =>
					__( '富文本区域。按Alt-Shift-H获取帮助。' ),
				'富文本区域。按Control-Option-H获取帮助。' => __( '富文本区域。按Control-Option-H获取帮助。' ),
				'You have unsaved changes are you sure you want to navigate away?' =>
					__( '离开这个页面，您所做的更改将丢失。' ),
				'Your browser doesn\'t support direct access to the clipboard. Please use the Ctrl+X/C/V keyboard shortcuts instead.' =>
					__( '您的浏览器不支持直接访问剪贴板，请使用键盘快捷键或浏览器的编辑菜单。' ),

				// TinyMCE menus.
				'Insert'                               => _x( '插入', 'TinyMCE menu' ),
				'File'                                 => _x( '文件', 'TinyMCE menu' ),
				'Edit'                                 => _x( '编辑', 'TinyMCE menu' ),
				'Tools'                                => _x( '工具', 'TinyMCE menu' ),
				'View'                                 => _x( '查看', 'TinyMCE menu' ),
				'Table'                                => _x( '表格', 'TinyMCE menu' ),
				'Format'                               => _x( '格式', 'TinyMCE menu' ),

				// GeChiUI strings.
				'显示/隐藏工具栏'                       => array( __( '显示/隐藏工具栏' ), 'accessZ' ),
				'插入“More”标签'                 => array( __( '插入“More”标签' ), 'accessT' ),
				'插入分页标签'                => array( __( '插入分页标签' ), 'accessP' ),
				'阅读更多…'                         => __( '阅读更多…' ), // Title on the placeholder inside the editor (no ellipsis).
				'免打扰写作模式'        => array( __( '免打扰写作模式' ), 'accessW' ),
				'无对齐'                         => __( '无对齐' ), // Tooltip for the 'alignnone' button in the image toolbar.
				'Remove'                               => __( '移除' ),       // Tooltip for the 'remove' button in the image toolbar.
				'Edit|button'                          => __( '编辑' ),         // Tooltip for the 'edit' button in the image toolbar.
				'粘贴URL或键入来搜索'          => __( '粘贴URL或键入来搜索' ), // Placeholder for the inline link dialog.
				'Apply'                                => __( '应用' ),        // Tooltip for the 'apply' button in the inline link dialog.
				'链接选项'                         => __( '链接选项' ), // Tooltip for the 'link options' button in the inline link dialog.
				'Visual'                               => _x( '可视化', 'Name for the Visual editor tab' ),             // Editor switch tab label.
				'Text'                                 => _x( '文本', 'Name for the Text editor tab (formerly HTML)' ), // Editor switch tab label.
				'添加媒体'                            => array( __( '添加媒体' ), 'accessM' ), // Tooltip for the '添加媒体' button in the block editor Classic block.

				// Shortcuts help modal.
				'键盘快捷键'                   => array( __( '键盘快捷键' ), 'accessH' ),
				'传统区块键盘快捷键'     => __( '传统区块键盘快捷键' ),
				'默认快捷方式，'                   => __( '默认快捷方式，' ),
				'额外的快捷方式，'                => __( '额外的快捷方式，' ),
				'焦点快捷方式：'                     => __( '焦点快捷方式：' ),
				'内联工具栏（当图片、链接或预览被选中时）' => __( '内联工具栏（当图片、链接或预览被选中时）' ),
				'编辑菜单（如被启用）'           => __( '编辑菜单（如被启用）' ),
				'编辑工具栏'                       => __( '编辑工具栏' ),
				'元素路径'                        => __( '元素路径' ),
				'Ctrl+Alt+字母：'                 => __( 'Ctrl+Alt+字母：' ),
				'Shift+Alt+字母：'                => __( 'Shift+Alt+字母：' ),
				'Cmd+字母：'                        => __( 'Cmd+字母：' ),
				'Ctrl+字母：'                       => __( 'Ctrl+字母：' ),
				'Letter'                               => __( '字母' ),
				'Action'                               => __( '操作' ),
				'警告：此链接已被插入但可能含有错误，请测试。' => __( '警告：此链接已被插入但可能含有错误，请测试。' ),
				'要移动焦点到其他按钮，请使用Tab或箭头键；要将焦点移回编辑器，请按Esc或使用任意一个按钮。' =>
					__( '要移动焦点到其他按钮，请使用Tab或箭头键；要将焦点移回编辑器，请按Esc或使用任意一个按钮。' ),
				'当使用这些格式快捷键后跟空格来创建新段落时，这些格式会被自动应用。按退格或退出键来撤销。' =>
					__( '当使用这些格式快捷键后跟空格来创建新段落时，这些格式会被自动应用。按退格或退出键来撤销。' ),
				'以下格式捷径在按回车键时被替换。请按退出或撤销键来撤销。' =>
					__( '以下格式捷径在按回车键时被替换。请按退出或撤销键来撤销。' ),
				'以下的格式捷径将会在您打字或将它们插入同一段落种的纯文本周围时被自动应用。按Esc或撤销按钮来撤销。' =>
					__( '以下的格式捷径将会在您打字或将它们插入同一段落种的纯文本周围时被自动应用。按Esc或撤销按钮来撤销。' ),
			);
		}

		/*
		Imagetools plugin (not included):
			'编辑图片' => __( '编辑图片' ),
			'图片选项' => __( '图片选项' ),
			'Back' => __( '返回' ),
			'Invert' => __( 'Invert' ),
			'Flip horizontally' => __( '水平翻转' ),
			'Flip vertically' => __( '垂直翻转' ),
			'Crop' => __( '裁剪' ),
			'Orientation' => __( '方向' ),
			'Resize' => __( 'Resize' ),
			'Rotate clockwise' => __( '向右转' ),
			'Rotate counterclockwise' => __( '向左转' ),
			'Sharpen' => __( 'Sharpen' ),
			'Brightness' => __( '亮度' ),
			'Color levels' => __( 'Color levels' ),
			'Contrast' => __( 'Contrast' ),
			'Gamma' => __( 'Gamma' ),
			'放大'  => __( '放大'  ),
			'缩小'  => __( '缩小'  ),
		*/

		return self::$translation;
	}

	/**
	 * Translates the default TinyMCE strings and returns them as JSON encoded object ready to be loaded with tinymce.addI18n(),
	 * or as JS snippet that should run after tinymce.js is loaded.
	 *
	 *
	 * @param string $mce_locale The locale used for the editor.
	 * @param bool   $json_only  Optional. Whether to include the JavaScript calls to tinymce.addI18n() and
	 *                           tinymce.ScriptLoader.markDone().
	 * @return string Translation object, JSON encoded.
	 */
	public static function gc_mce_translation( $mce_locale = '', $json_only = false ) {
		if ( ! $mce_locale ) {
			$mce_locale = self::get_mce_locale();
		}

		$mce_translation = self::get_translation();

		foreach ( $mce_translation as $name => $value ) {
			if ( is_array( $value ) ) {
				$mce_translation[ $name ] = $value[0];
			}
		}

		/**
		 * Filters translated strings prepared for TinyMCE.
		 *
		 *
		 * @param array  $mce_translation Key/value pairs of strings.
		 * @param string $mce_locale      Locale.
		 */
		$mce_translation = apply_filters( 'gc_mce_translation', $mce_translation, $mce_locale );

		foreach ( $mce_translation as $key => $value ) {
			// Remove strings that are not translated.
			if ( $key === $value ) {
				unset( $mce_translation[ $key ] );
				continue;
			}

			if ( false !== strpos( $value, '&' ) ) {
				$mce_translation[ $key ] = html_entity_decode( $value, ENT_QUOTES, 'UTF-8' );
			}
		}

		// Set direction.
		if ( is_rtl() ) {
			$mce_translation['_dir'] = 'rtl';
		}

		if ( $json_only ) {
			return gc_json_encode( $mce_translation );
		}

		$baseurl = self::get_baseurl();

		return "tinymce.addI18n( '$mce_locale', " . gc_json_encode( $mce_translation ) . ");\n" .
			"tinymce.ScriptLoader.markDone( '$baseurl/langs/$mce_locale.js' );\n";
	}

	/**
	 * Force uncompressed TinyMCE when a custom theme has been defined.
	 *
	 * The compressed TinyMCE file cannot deal with custom themes, so this makes
	 * sure that we use the uncompressed TinyMCE file if a theme is defined.
	 * Even if we are on a production environment.
	 *
	 */
	public static function force_uncompressed_tinymce() {
		$has_custom_theme = false;
		foreach ( self::$mce_settings as $init ) {
			if ( ! empty( $init['theme_url'] ) ) {
				$has_custom_theme = true;
				break;
			}
		}

		if ( ! $has_custom_theme ) {
			return;
		}

		$gc_scripts = gc_scripts();

		$gc_scripts->remove( 'gc-tinymce' );
		gc_register_tinymce_scripts( $gc_scripts, true );
	}

	/**
	 * Print (output) the main TinyMCE scripts.
	 *
	 *
	 * @global bool $concatenate_scripts
	 */
	public static function print_tinymce_scripts() {
		global $concatenate_scripts;

		if ( self::$tinymce_scripts_printed ) {
			return;
		}

		self::$tinymce_scripts_printed = true;

		if ( ! isset( $concatenate_scripts ) ) {
			script_concat_settings();
		}

		gc_print_scripts( array( 'gc-tinymce' ) );

		echo "<script type='text/javascript'>\n" . self::gc_mce_translation() . "</script>\n";
	}

	/**
	 * Print (output) the TinyMCE configuration and initialization scripts.
	 *
	 *
	 * @global string $tinymce_version
	 */
	public static function editor_js() {
		global $tinymce_version;

		$tmce_on = ! empty( self::$mce_settings );
		$mceInit = '';
		$qtInit  = '';

		if ( $tmce_on ) {
			foreach ( self::$mce_settings as $editor_id => $init ) {
				$options  = self::_parse_init( $init );
				$mceInit .= "'$editor_id':{$options},";
			}
			$mceInit = '{' . trim( $mceInit, ',' ) . '}';
		} else {
			$mceInit = '{}';
		}

		if ( ! empty( self::$qt_settings ) ) {
			foreach ( self::$qt_settings as $editor_id => $init ) {
				$options = self::_parse_init( $init );
				$qtInit .= "'$editor_id':{$options},";
			}
			$qtInit = '{' . trim( $qtInit, ',' ) . '}';
		} else {
			$qtInit = '{}';
		}

		$ref = array(
			'plugins'  => implode( ',', self::$plugins ),
			'theme'    => 'modern',
			'language' => self::$mce_locale,
		);

		$suffix  = SCRIPT_DEBUG ? '' : '.min';
		$baseurl = self::get_baseurl();
		$version = 'ver=' . $tinymce_version;

		/**
		 * Fires immediately before the TinyMCE settings are printed.
		 *
		 *
		 * @param array $mce_settings TinyMCE settings array.
		 */
		do_action( 'before_gc_tiny_mce', self::$mce_settings );
		?>

		<script type="text/javascript">
		tinyMCEPreInit = {
			baseURL: "<?php echo $baseurl; ?>",
			suffix: "<?php echo $suffix; ?>",
			<?php

			if ( self::$drag_drop_upload ) {
				echo 'dragDropUpload: true,';
			}

			?>
			mceInit: <?php echo $mceInit; ?>,
			qtInit: <?php echo $qtInit; ?>,
			ref: <?php echo self::_parse_init( $ref ); ?>,
			load_ext: function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
		};
		</script>
		<?php

		if ( $tmce_on ) {
			self::print_tinymce_scripts();

			if ( self::$ext_plugins ) {
				// Load the old-format English strings to prevent unsightly labels in old style popups.
				echo "<script type='text/javascript' src='{$baseurl}/langs/gc-langs-en.js?$version'></script>\n";
			}
		}

		/**
		 * Fires after tinymce.js is loaded, but before any TinyMCE editor
		 * instances are created.
		 *
		 *
		 * @param array $mce_settings TinyMCE settings array.
		 */
		do_action( 'gc_tiny_mce_init', self::$mce_settings );

		?>
		<script type="text/javascript">
		<?php

		if ( self::$ext_plugins ) {
			echo self::$ext_plugins . "\n";
		}

		if ( ! is_admin() ) {
			echo 'var ajaxurl = "' . admin_url( 'admin-ajax.php', 'relative' ) . '";';
		}

		?>

		( function() {
			var initialized = [];
			var initialize  = function() {
				var init, id, inPostbox, $wrap;
				var readyState = document.readyState;

				if ( readyState !== 'complete' && readyState !== 'interactive' ) {
					return;
				}

				for ( id in tinyMCEPreInit.mceInit ) {
					if ( initialized.indexOf( id ) > -1 ) {
						continue;
					}

					init      = tinyMCEPreInit.mceInit[id];
					$wrap     = tinymce.$( '#gc-' + id + '-wrap' );
					inPostbox = $wrap.parents( '.postbox' ).length > 0;

					if (
						! init.gc_skip_init &&
						( $wrap.hasClass( 'tmce-active' ) || ! tinyMCEPreInit.qtInit.hasOwnProperty( id ) ) &&
						( readyState === 'complete' || ( ! inPostbox && readyState === 'interactive' ) )
					) {
						tinymce.init( init );
						initialized.push( id );

						if ( ! window.gcActiveEditor ) {
							window.gcActiveEditor = id;
						}
					}
				}
			}

			if ( typeof tinymce !== 'undefined' ) {
				if ( tinymce.Env.ie && tinymce.Env.ie < 11 ) {
					tinymce.$( '.gc-editor-wrap ' ).removeClass( 'tmce-active' ).addClass( 'html-active' );
				} else {
					if ( document.readyState === 'complete' ) {
						initialize();
					} else {
						document.addEventListener( 'readystatechange', initialize );
					}
				}
			}

			if ( typeof quicktags !== 'undefined' ) {
				for ( id in tinyMCEPreInit.qtInit ) {
					quicktags( tinyMCEPreInit.qtInit[id] );

					if ( ! window.gcActiveEditor ) {
						window.gcActiveEditor = id;
					}
				}
			}
		}());
		</script>
		<?php

		if ( in_array( 'gclink', self::$plugins, true ) || in_array( 'link', self::$qt_buttons, true ) ) {
			self::gc_link_dialog();
		}

		/**
		 * Fires after any core TinyMCE editor instances are created.
		 *
		 *
		 * @param array $mce_settings TinyMCE settings array.
		 */
		do_action( 'after_gc_tiny_mce', self::$mce_settings );
	}

	/**
	 * Outputs the HTML for distraction-free writing mode.
	 *
	 * @deprecated 4.3.0
	 */
	public static function gc_fullscreen_html() {
		_deprecated_function( __FUNCTION__, '4.3.0' );
	}

	/**
	 * Performs post queries for internal linking.
	 *
	 *
	 * @param array $args Optional. Accepts 'pagenum' and 's' (search) arguments.
	 * @return array|false Results.
	 */
	public static function gc_link_query( $args = array() ) {
		$pts      = get_post_types( array( 'public' => true ), 'objects' );
		$pt_names = array_keys( $pts );

		$query = array(
			'post_type'              => $pt_names,
			'suppress_filters'       => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status'            => 'publish',
			'posts_per_page'         => 20,
		);

		$args['pagenum'] = isset( $args['pagenum'] ) ? absint( $args['pagenum'] ) : 1;

		if ( isset( $args['s'] ) ) {
			$query['s'] = $args['s'];
		}

		$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

		/**
		 * Filters the link query arguments.
		 *
		 * Allows modification of the link query arguments before querying.
		 *
		 * @see GC_Query for a full list of arguments
		 *
		 *
		 * @param array $query An array of GC_Query arguments.
		 */
		$query = apply_filters( 'gc_link_query_args', $query );

		// Do main query.
		$get_posts = new GC_Query;
		$posts     = $get_posts->query( $query );

		// Build results.
		$results = array();
		foreach ( $posts as $post ) {
			if ( 'post' === $post->post_type ) {
				$info = mysql2date( __( 'Y-m-d' ), $post->post_date );
			} else {
				$info = $pts[ $post->post_type ]->labels->singular_name;
			}

			$results[] = array(
				'ID'        => $post->ID,
				'title'     => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
				'permalink' => get_permalink( $post->ID ),
				'info'      => $info,
			);
		}

		/**
		 * Filters the link query results.
		 *
		 * Allows modification of the returned link query results.
		 *
		 *
		 * @see 'gc_link_query_args' filter
		 *
		 * @param array $results {
		 *     An array of associative arrays of query results.
		 *
		 *     @type array ...$0 {
		 *         @type int    $ID        Post ID.
		 *         @type string $title     The trimmed, escaped post title.
		 *         @type string $permalink Post permalink.
		 *         @type string $info      A 'Y/m/d'-formatted date for 'post' post type,
		 *                                 the 'singular_name' post type label otherwise.
		 *     }
		 * }
		 * @param array $query  An array of GC_Query arguments.
		 */
		$results = apply_filters( 'gc_link_query', $results, $query );

		return ! empty( $results ) ? $results : false;
	}

	/**
	 * Dialog for internal linking.
	 *
	 */
	public static function gc_link_dialog() {
		// Run once.
		if ( self::$link_dialog_printed ) {
			return;
		}

		self::$link_dialog_printed = true;

		// `display: none` is required here, see #GC27605.
		?>
		<div id="gc-link-backdrop" style="display: none"></div>
		<div id="gc-link-wrap" class="gc-core-ui" style="display: none" role="dialog" aria-labelledby="link-modal-title">
		<form id="gc-link" tabindex="-1">
		<?php gc_nonce_field( 'internal-linking', '_ajax_linking_nonce', false ); ?>
		<h1 id="link-modal-title"><?php _e( '插入或编辑链接' ); ?></h1>
		<button type="button" id="gc-link-close"><span class="screen-reader-text"><?php _e( '关闭' ); ?></span></button>
		<div id="link-selector">
			<div id="link-options">
				<p class="howto" id="gclink-enter-url"><?php _e( '输入目标URL' ); ?></p>
				<div>
					<label><span><?php _e( 'URL' ); ?></span>
					<input id="gc-link-url" type="text" aria-describedby="gclink-enter-url" /></label>
				</div>
				<div class="gc-link-text-field">
					<label><span><?php _e( '链接文字' ); ?></span>
					<input id="gc-link-text" type="text" /></label>
				</div>
				<div class="link-target">
					<label><span></span>
					<input type="checkbox" id="gc-link-target" /> <?php _e( '在新标签页中打开链接' ); ?></label>
				</div>
			</div>
			<p class="howto" id="gclink-link-existing-content"><?php _e( '或链接到站点中的内容' ); ?></p>
			<div id="search-panel">
				<div class="link-search-wrapper">
					<label>
						<span class="search-label"><?php _e( '搜索' ); ?></span>
						<input type="search" id="gc-link-search" class="link-search-field" autocomplete="off" aria-describedby="gclink-link-existing-content" />
						<span class="spinner"></span>
					</label>
				</div>
				<div id="search-results" class="query-results" tabindex="0">
					<ul></ul>
					<div class="river-waiting">
						<span class="spinner"></span>
					</div>
				</div>
				<div id="most-recent-results" class="query-results" tabindex="0">
					<div class="query-notice" id="query-notice-message">
						<em class="query-notice-default"><?php _e( '未指定搜索条件。自动显示最近发布条目。' ); ?></em>
						<em class="query-notice-hint screen-reader-text"><?php _e( '搜索或使用上下方向键来选择一项。' ); ?></em>
					</div>
					<ul></ul>
					<div class="river-waiting">
						<span class="spinner"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="submitbox">
			<div id="gc-link-cancel">
				<button type="button" class="button"><?php _e( '取消' ); ?></button>
			</div>
			<div id="gc-link-update">
				<input type="submit" value="<?php esc_attr_e( '添加链接' ); ?>" class="button button-primary" id="gc-link-submit" name="gc-link-submit">
			</div>
		</div>
		</form>
		</div>
		<?php
	}
}
