<?php
/**
 * GC_Privacy_Policy_Content class.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

#[AllowDynamicProperties]
final class GC_Privacy_Policy_Content {

	private static $policy_content = array();

	/**
	 * Constructor
	 *
	 * @since 4.9.6
	 */
	private function __construct() {}

	/**
	 * Adds content to the postbox shown when editing the privacy policy.
	 *
	 * Plugins and themes should suggest text for inclusion in the site's privacy policy.
	 * The suggested text should contain information about any functionality that affects user privacy,
	 * and will be shown in the Suggested Privacy Policy Content postbox.
	 *
	 * Intended for use from `gc_add_privacy_policy_content()`.
	 *
	 * @since 4.9.6
	 *
	 * @param string $plugin_name The name of the plugin or theme that is suggesting content for the site's privacy policy.
	 * @param string $policy_text The suggested content for inclusion in the policy.
	 */
	public static function add( $plugin_name, $policy_text ) {
		if ( empty( $plugin_name ) || empty( $policy_text ) ) {
			return;
		}

		$data = array(
			'plugin_name' => $plugin_name,
			'policy_text' => $policy_text,
		);

		if ( ! in_array( $data, self::$policy_content, true ) ) {
			self::$policy_content[] = $data;
		}
	}

	/**
	 * Performs a quick check to determine whether any privacy info has changed.
	 *
	 * @since 4.9.6
	 */
	public static function text_change_check() {

		$policy_page_id = (int) get_option( 'gc_page_for_privacy_policy' );

		// The site doesn't have a privacy policy.
		if ( empty( $policy_page_id ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $policy_page_id ) ) {
			return false;
		}

		$old = (array) get_post_meta( $policy_page_id, '_gc_suggested_privacy_policy_content' );

		// Updates are not relevant if the user has not reviewed any suggestions yet.
		if ( empty( $old ) ) {
			return false;
		}

		$cached = get_option( '_gc_suggested_policy_text_has_changed' );

		/*
		 * When this function is called before `admin_init`, `self::$policy_content`
		 * has not been populated yet, so use the cached result from the last
		 * execution instead.
		 */
		if ( ! did_action( 'admin_init' ) ) {
			return 'changed' === $cached;
		}

		$new = self::$policy_content;

		// Remove the extra values added to the meta.
		foreach ( $old as $key => $data ) {
			if ( ! is_array( $data ) || ! empty( $data['removed'] ) ) {
				unset( $old[ $key ] );
				continue;
			}

			$old[ $key ] = array(
				'plugin_name' => $data['plugin_name'],
				'policy_text' => $data['policy_text'],
			);
		}

		// Normalize the order of texts, to facilitate comparison.
		sort( $old );
		sort( $new );

		/*
		 * The == operator (equal, not identical) was used intentionally.
		 * See https://www.php.net/manual/en/language.operators.array.php
		 */
		if ( $new != $old ) {
			/*
			 * A plugin was activated or deactivated, or some policy text has changed.
			 * Show a notice on the relevant screens to inform the admin.
			 */
			add_action( 'admin_notices', array( 'GC_Privacy_Policy_Content', 'policy_text_changed_notice' ) );
			$state = 'changed';
		} else {
			$state = 'not-changed';
		}

		// Cache the result for use before `admin_init` (see above).
		if ( $cached !== $state ) {
			update_option( '_gc_suggested_policy_text_has_changed', $state );
		}

		return 'changed' === $state;
	}

	/**
	 * Outputs a warning when some privacy info has changed.
	 *
	 * @since 4.9.6
	 *
	 * @global GC_Post $post Global post object.
	 */
	public static function policy_text_changed_notice() {
		global $post;

		$screen = get_current_screen()->id;

		if ( 'privacy' !== $screen ) {
			return;
		}

		$message = sprintf(
					/* translators: %s: Privacy Policy Guide URL. */
					__( '推荐的隐私政策文本已发生改动。请<a href="%s">浏览指南页面</a>并更新您的隐私政策。' ),
					esc_url( admin_url( 'privacy-policy-guide.php?tab=policyguide' ) )
				);

		echo setting_error( $message, 'policy-text-updated warning' );
	}

	/**
	 * Updates the cached policy info when the policy page is updated.
	 *
	 * @since 4.9.6
	 * @access private
	 *
	 * @param int $post_id The ID of the updated post.
	 */
	public static function _policy_page_updated( $post_id ) {
		$policy_page_id = (int) get_option( 'gc_page_for_privacy_policy' );

		if ( ! $policy_page_id || $policy_page_id !== (int) $post_id ) {
			return;
		}

		// Remove updated|removed status.
		$old          = (array) get_post_meta( $policy_page_id, '_gc_suggested_privacy_policy_content' );
		$done         = array();
		$update_cache = false;

		foreach ( $old as $old_key => $old_data ) {
			if ( ! empty( $old_data['removed'] ) ) {
				// Remove the old policy text.
				$update_cache = true;
				continue;
			}

			if ( ! empty( $old_data['updated'] ) ) {
				// 'updated' is now 'added'.
				$done[]       = array(
					'plugin_name' => $old_data['plugin_name'],
					'policy_text' => $old_data['policy_text'],
					'added'       => $old_data['updated'],
				);
				$update_cache = true;
			} else {
				$done[] = $old_data;
			}
		}

		if ( $update_cache ) {
			delete_post_meta( $policy_page_id, '_gc_suggested_privacy_policy_content' );
			// Update the cache.
			foreach ( $done as $data ) {
				add_post_meta( $policy_page_id, '_gc_suggested_privacy_policy_content', $data );
			}
		}
	}

	/**
	 * Checks for updated, added or removed privacy policy information from plugins.
	 *
	 * Caches the current info in post_meta of the policy page.
	 *
	 * @since 4.9.6
	 *
	 * @return array The privacy policy text/information added by core and plugins.
	 */
	public static function get_suggested_policy_text() {
		$policy_page_id = (int) get_option( 'gc_page_for_privacy_policy' );
		$checked        = array();
		$time           = time();
		$update_cache   = false;
		$new            = self::$policy_content;
		$old            = array();

		if ( $policy_page_id ) {
			$old = (array) get_post_meta( $policy_page_id, '_gc_suggested_privacy_policy_content' );
		}

		// Check for no-changes and updates.
		foreach ( $new as $new_key => $new_data ) {
			foreach ( $old as $old_key => $old_data ) {
				$found = false;

				if ( $new_data['policy_text'] === $old_data['policy_text'] ) {
					// Use the new plugin name in case it was changed, translated, etc.
					if ( $old_data['plugin_name'] !== $new_data['plugin_name'] ) {
						$old_data['plugin_name'] = $new_data['plugin_name'];
						$update_cache            = true;
					}

					// A plugin was re-activated.
					if ( ! empty( $old_data['removed'] ) ) {
						unset( $old_data['removed'] );
						$old_data['added'] = $time;
						$update_cache      = true;
					}

					$checked[] = $old_data;
					$found     = true;
				} elseif ( $new_data['plugin_name'] === $old_data['plugin_name'] ) {
					// The info for the policy was updated.
					$checked[]    = array(
						'plugin_name' => $new_data['plugin_name'],
						'policy_text' => $new_data['policy_text'],
						'updated'     => $time,
					);
					$found        = true;
					$update_cache = true;
				}

				if ( $found ) {
					unset( $new[ $new_key ], $old[ $old_key ] );
					continue 2;
				}
			}
		}

		if ( ! empty( $new ) ) {
			// A plugin was activated.
			foreach ( $new as $new_data ) {
				if ( ! empty( $new_data['plugin_name'] ) && ! empty( $new_data['policy_text'] ) ) {
					$new_data['added'] = $time;
					$checked[]         = $new_data;
				}
			}
			$update_cache = true;
		}

		if ( ! empty( $old ) ) {
			// A plugin was deactivated.
			foreach ( $old as $old_data ) {
				if ( ! empty( $old_data['plugin_name'] ) && ! empty( $old_data['policy_text'] ) ) {
					$data = array(
						'plugin_name' => $old_data['plugin_name'],
						'policy_text' => $old_data['policy_text'],
						'removed'     => $time,
					);

					$checked[] = $data;
				}
			}
			$update_cache = true;
		}

		if ( $update_cache && $policy_page_id ) {
			delete_post_meta( $policy_page_id, '_gc_suggested_privacy_policy_content' );
			// Update the cache.
			foreach ( $checked as $data ) {
				add_post_meta( $policy_page_id, '_gc_suggested_privacy_policy_content', $data );
			}
		}

		return $checked;
	}

	/**
	 * Adds a notice with a link to the guide when editing the privacy policy page.
	 *
	 * @since 4.9.6
	 * @since 5.0.0 The `$post` parameter was made optional.
	 *
	 * @global GC_Post $post Global post object.
	 *
	 * @param GC_Post|null $post The currently edited post. Default null.
	 */
	public static function notice( $post = null ) {
		if ( is_null( $post ) ) {
			global $post;
		} else {
			$post = get_post( $post );
		}

		if ( ! ( $post instanceof GC_Post ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_privacy_options' ) ) {
			return;
		}

		$current_screen = get_current_screen();
		$policy_page_id = (int) get_option( 'gc_page_for_privacy_policy' );

		if ( 'post' !== $current_screen->base || $policy_page_id !== $post->ID ) {
			return;
		}

		$message = __( '需要帮助来创建您的隐私政策页面？请查阅我们的指南来看看应该包括哪些内容，还有您的插件和主题推荐的政策。' );
		$url     = esc_url( admin_url( 'options-privacy.php?tab=policyguide' ) );
		$label   = __( '查阅《隐私政策指南》。' );

		if ( get_current_screen()->is_block_editor() ) {
			gc_enqueue_script( 'gc-notices' );
			$action = array(
				'url'   => $url,
				'label' => $label,
			);
			gc_add_inline_script(
				'gc-notices',
				sprintf(
					'gc.data.dispatch( "core/notices" ).createWarningNotice( "%s", { actions: [ %s ], isDismissible: false } )',
					$message,
					gc_json_encode( $action )
				),
				'after'
			);
		} else {
			$message .= sprintf(
					' <a href="%s" target="_blank">%s <span class="screen-reader-text">%s</span></a>',
					$url,
					$label,
					/* translators: Hidden accessibility text. */
					__( '（在新窗口中打开）' )
				);
			echo setting_error( $message, 'warning inline gc-pp-notice' );
		}
	}

	/**
	 * Outputs the privacy policy guide together with content from the theme and plugins.
	 *
	 * @since 4.9.6
	 */
	public static function privacy_policy_guide() {

		$content_array = self::get_suggested_policy_text();
		$content       = '';
		$date_format   = __( 'Y年n月j日' );

		foreach ( $content_array as $section ) {
			$class   = '';
			$meta    = '';
			$removed = '';

			if ( ! empty( $section['removed'] ) ) {
				$badge_class = ' red';
				$date        = date_i18n( $date_format, $section['removed'] );
				/* translators: %s: Date of plugin deactivation. */
				$badge_title = sprintf( __( '已移除%s。' ), $date );

				/* translators: %s: Date of plugin deactivation. */
				$removed = __( '您在%s禁用了此插件，因此您可能已经不需要此政策。' );
				$removed = setting_error( sprintf( $removed, $date ), 'primary inline' );
			} elseif ( ! empty( $section['updated'] ) ) {
				$badge_class = ' blue';
				$date        = date_i18n( $date_format, $section['updated'] );
				/* translators: %s: Date of privacy policy text update. */
				$badge_title = sprintf( __( '已更新%s。' ), $date );
			}

			$plugin_name = esc_html( $section['plugin_name'] );

			$sanitized_policy_name = sanitize_title_with_dashes( $plugin_name );
			?>
			<h4 class="privacy-settings-accordion-heading">
			<button aria-expanded="false" class="privacy-settings-accordion-trigger" aria-controls="privacy-settings-accordion-block-<?php echo $sanitized_policy_name; ?>" type="button">
				<span class="title"><?php echo $plugin_name; ?></span>
				<?php if ( ! empty( $section['removed'] ) || ! empty( $section['updated'] ) ) : ?>
				<span class="badge <?php echo $badge_class; ?>"> <?php echo $badge_title; ?></span>
				<?php endif; ?>
				<span class="icon"></span>
			</button>
			</h4>
			<div id="privacy-settings-accordion-block-<?php echo $sanitized_policy_name; ?>" class="privacy-settings-accordion-panel privacy-text-box-body" hidden="hidden">
				<?php
				echo $removed;
				echo $section['policy_text'];
				?>
				<?php if ( empty( $section['removed'] ) ) : ?>
				<div class="privacy-settings-accordion-actions">
					<span class="success" aria-hidden="true"><?php _e( '已复制！' ); ?></span>
					<button type="button" class="privacy-text-copy button">
						<span aria-hidden="true"><?php _e( '将建议的隐私政策文本复制到剪贴板' ); ?></span>
						<span class="screen-reader-text">
							<?php
							/* translators: Hidden accessibility text. %s: Plugin name. */
							printf( __( '从%s复制推荐的政策文本。' ), $plugin_name );
							?>
						</span>
					</button>
				</div>
				<?php endif; ?>
			</div>
			<?php
		}
	}

	/**
	 * Returns the default suggested privacy policy content.
	 *
	 * @since 4.9.6
	 * @since 5.0.0 Added the `$blocks` parameter.
	 *
	 * @param bool $description Whether to include the descriptions under the section headings. Default false.
	 * @param bool $blocks      Whether to format the content for the block editor. Default true.
	 * @return string The default policy content.
	 */
	public static function get_default_content( $description = false, $blocks = true ) {
		$suggested_text = '<strong class="privacy-policy-tutorial">' . __( '推荐的文本：' ) . ' </strong>';
		$content        = '';
		$strings        = array();

		// Start of the suggested privacy policy text.
		if ( $description ) {
			$strings[] = '<div class="gc-suggested-text">';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2>' . __( '我们是谁' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当列出您的系统URL、主办系统的公司、组织或个人及准确的联系信息。' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '此章节中，所需的信息的多少将取决于您所在国家或地区的商业法规要求。例如，您有可能需要展示您的实际地址、注册地址或公司注册代码。' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. %s: Site URL. */
			$strings[] = '<p>' . $suggested_text . sprintf( __( '我们的系统地址是：%s。' ), get_bloginfo( 'url', 'display' ) ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '我们收集何种及为何收集个人数据' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当列出您从系统用户和访问者处收集哪些个人数据，这可能包括如姓名、电子邮箱、个人账户设置等个人数据，如购买信息等交易数据、cookies中所包含的信息等技术数据。' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '您也应该表明您如何收集及存放敏感的个人数据，例如与健康相关的数据。' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在列出您收集的个人数据之外，您也需要表明您为何收集这些信息。这里的说明必须指出您的数据收集及保留的法律基础，或有无获得用户同意。' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '个人数据不仅由用户与您的系统的交互而产生，也会由其他技术过程产生，例如联系表单、评论、cookies、统计和第三方嵌入内容等。' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'GeChiUI默认不会收集关于访客的任何个人数据，而只会收集关于注册用户的，在用户资料页面显示的数据。但是您安装的一些插件可能会收集个人数据。您应该在下面加入相关信息。' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2>' . __( '评论' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当表明您在用户留下评论时收集何种信息。我们已将GeChiUI默认收集的信息列举如下。' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( '当访客留下评论时，我们会收集评论表单所显示的数据，和访客的IP地址及浏览器的user agent字符串来帮助检查垃圾评论。' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( '由您的电子邮箱所生成的匿名化字符串（又称为哈希）可能会被提供给Gravatar服务确认您是否有使用该服务。Gravatar服务的隐私政策在此：https://automattic.com/privacy/。在您的评论获批准后，您的头像将在您的评论旁公开展示。' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2>' . __( '媒体' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当表明用户在上传媒体文件时会披露何种信息。通常来说，所有已上传的文件都是公开可访问的。' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( '如果您向此系统上传图片，您应当避免上传那些有嵌入地理位置信息（EXIF GPS）的图片。此系统的访客将可以下载并提取此系统的图片中的位置信息。' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '联系表单' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'GeChiUI默认并不提供联系表单。如果您使用了联系表单插件，请在此章节表明您在访客提交联系表单时所收集个人数据的种类及保留期限。例如，您可以说明，您会将所有提交的联系表单保留一段时间以供用户服务之用，但您不会将这些信息用于市场宣传。' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2>' . __( 'Cookies' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当列出您的系统所使用的cookies，这也包括您的插件、社交媒体和统计程序所设置的cookies。我们已经为您列出了GeChiUI默认使用的cookies。' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( '如果您在我们的系统上留下评论，您可以选择用cookies保存您的名字、电子邮箱和网站地址。这是通过让您可以不用在评论时再次填写相关内容而向您提供方便。这些cookies会保留一年。' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( '如果您访问我们的登录页，我们会设置一个临时的cookie来确认您的浏览器是否接受cookies。此cookie不包含个人数据，且会在您关闭浏览器时被丢弃。' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( '当您登录时，我们也会设置多个cookies来保存您的登录信息及屏幕显示选项。登录cookies会保留两天，而屏幕显示选项cookies会保留一年。如果您选择了“记住我”，您的登录状态则会保留两周。如果您注销登陆了您的账户，用于登录的cookies将会被移除。' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( '如果您编辑或发布文章，我们会在您的浏览器中保存一个额外的cookie。这个cookie不包含个人数据而只记录了您刚才编辑的文章的ID。这个cookie会保留一天。' ) . '</p>';
		}

		if ( ! $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '来自其他网站的嵌入内容' ) . '</h2>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( '此系统上的文章可能会包含嵌入的内容（如视频、图片、文章等）。来自其他系统的嵌入内容的行为和您直接访问这些其他系统没有区别。' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( '这些系统可能会收集关于您的数据、使用cookies、嵌入额外的第三方跟踪程序及监视您与这些嵌入内容的交互，包括在您有这些系统的账户并登录了这些系统时，跟踪您与嵌入内容的交互。' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '统计' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应该说明您使用何种统计程序软件包，用户选择退出统计跟踪的方式，以及指向您统计服务提供商隐私政策的链接。' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'GeChiUI默认不会收集统计数据，但许多虚拟主机账户会收集匿名的统计数据。如果您安装了提供统计服务的GeChiUI插件，请将这些插件的信息列在此处。' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2>' . __( '我们与谁共享您的信息' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当列出您共享系统数据的所有第三方提供商，包括合作伙伴、云服务、支付服务及其他第三方服务提供商。您应当写明您与他们共享数据的种类及共享的原因。如果可能，您也应该提供指向他们隐私政策的链接。' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( 'GeChiUI默认不会与任何人共享任何个人数据。' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( '若您请求重置密码，您的IP地址将包含于密码重置邮件中。' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2>' . __( '我们保留多久您的信息' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当指出由系统收集或处理的个人数据的保留期限。尽管您有责任决定数据集的保存期限及保存原因，您也需要将这些信息列在此处。例如，您可以说明，您会保留联系表单条目六个月、保留统计记录一年、保留用户购买记录十年等。' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( '如果您留下评论，评论和其元数据将被无限期保存。我们这样做以便能识别并自动批准任何后续评论，而不用将这些后续评论加入待审队列。' ) . '</p>';
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . __( '对于本系统的注册用户，我们也会保存用户在个人资料中提供的个人信息。所有用户可以在任何时候查看、编辑或删除他们的个人信息（除了不能变更用户名外）、系统管理员也可以查看及编辑那些信息。' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2>' . __( '您对您的信息有什么权利' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当解释用户对于其个人数据所拥有的权利及他们行使权利的方式。' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( '如果您有此系统的账户，或曾经留下评论，您可以请求我们提供我们所拥有的您的个人数据的导出文件，这也包括了所有您提供给我们的数据。您也可以要求我们抹除所有关于您的个人数据。这不包括我们因管理、法规或安全需要而必须保留的数据。' ) . '</p>';
		}

		/* translators: Default privacy policy heading. */
		$strings[] = '<h2>' . __( '您的数据将发送到何处' ) . '</h2>';

		if ( $description ) {
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当列出所有将您的系统数据移出欧盟的行为，并描述这些数据受到了何种符合欧洲数据保护标准的保护方式。这些数据可能包括了您系统使用的虚拟主机、云存储及其他第三方服务。' ) . '</p>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '欧盟的数据保护法规要求，所有关于欧盟居民的数据，如有被转移出欧盟，必须受到与在欧盟内相同等级的保护。因此，除了列出这些数据的去向外，您也需要描述您或您的第三方提供商如何符合这些标准，如通过隐私盾（Privacy Shield）或类似协议、格式合同条款或具备约束性的公司规则。' ) . '</p>';
		} else {
			/* translators: Default privacy policy text. */
			$strings[] = '<p>' . $suggested_text . __( '访客评论可能会被自动垃圾评论监测服务检查。' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '联系信息' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当留下处理隐私相关问题的联系方式。如果您依法规设有数据保护官（Data Protection Officer），请留下其姓名和完整联系方式。' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '其他信息' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '如果您将您的系统用于商业活动，且您进行了更复杂的个人数据收集及处理，您应当在您的隐私政策中写下以下信息。' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '我们如何保护您的数据' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当解释您使用了何种措施来保护用户的数据。这包括了技术手段（例如加密）、安全手段（例如双因素身份验证）及其他措施（例如要求职员参加数据保护培训）。若您已经完成数据保护影响评估（Privacy Impact Assessment），也可在此提及。' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '我们有何种数据泄露处理流程' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '在此章节中，您应当说明您采取何种流程处理实际出现或潜在的数据泄露，如内部报告系统、联系机制及软件缺陷赏金计划等。' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '我们从哪些第三方接收数据' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '如果您的系统从第三方，如广告商，接收关于用户的数据，这些信息必须在您的隐私政策中涉及到第三方数据的章节予以体现。' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '我们通过用户数据进行何种自动决策及（或）归纳' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '如果您的系统提供包含自动决策的服务，如允许用户申请信用卡、或收集用户信息进入广告资料，您必须明示这些事件的发生，并说明信息如何被使用，通过何种汇总数据进行了何种决策，及在决策不受人工干预作出时用户有哪些权利。' ) . '</p>';
		}

		if ( $description ) {
			/* translators: Default privacy policy heading. */
			$strings[] = '<h2>' . __( '行业监管披露要求' ) . '</h2>';
			/* translators: Privacy policy tutorial. */
			$strings[] = '<p class="privacy-policy-tutorial">' . __( '如果您属于受法律监管的行业，或您受到其他隐私法律的管辖，您可能需要在此披露这些信息。' ) . '</p>';
			$strings[] = '</div>';
		}

		if ( $blocks ) {
			foreach ( $strings as $key => $string ) {
				if ( str_starts_with( $string, '<p>' ) ) {
					$strings[ $key ] = '<!-- gc:paragraph -->' . $string . '<!-- /gc:paragraph -->';
				}

				if ( str_starts_with( $string, '<h2>' ) ) {
					$strings[ $key ] = '<!-- gc:heading -->' . $string . '<!-- /gc:heading -->';
				}
			}
		}

		$content = implode( '', $strings );
		// End of the suggested privacy policy text.

		/**
		 * Filters the default content suggested for inclusion in a privacy policy.
		 *
		 * @since 4.9.6
		 * @since 5.0.0 Added the `$strings`, `$description`, and `$blocks` parameters.
		 * @deprecated 5.7.0 Use gc_add_privacy_policy_content() instead.
		 *
		 * @param string   $content     The default policy content.
		 * @param string[] $strings     An array of privacy policy content strings.
		 * @param bool     $description Whether policy descriptions should be included.
		 * @param bool     $blocks      Whether the content should be formatted for the block editor.
		 */
		return apply_filters_deprecated(
			'gc_get_default_privacy_policy_content',
			array( $content, $strings, $description, $blocks ),
			'5.7.0',
			'gc_add_privacy_policy_content()'
		);
	}

	/**
	 * Adds the suggested privacy policy text to the policy postbox.
	 *
	 * @since 4.9.6
	 */
	public static function add_suggested_content() {
		$content = self::get_default_content( false, false );
		gc_add_privacy_policy_content( __( 'GeChiUI' ), $content );
	}
}
