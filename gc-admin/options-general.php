<?php
/**
 * General settings administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

/** GeChiUI Translation Installation API */
require_once ABSPATH . 'gc-admin/includes/translation-install.php';

if ( ! current_user_can( 'manage_options' ) ) {
	gc_die( __( '抱歉，您不能管理此系统的选项。' ) );
}

// Used in the HTML title tag.
$title       = __( '常规选项' );
$parent_file = 'options-general.php';
/* translators: Date and time format for exact current time, mainly about timezones, see https://www.php.net/manual/datetime.format.php */
$timezone_format = _x( 'Y-m-d H:i:s', 'timezone date format' );

add_action( 'admin_head', 'options_general_add_js' );

$options_help = '<p>' . __( '本页面中的选项决定您系统的基本配置。' ) . '</p>' .
	'<p>' . __( '大多数的主题在每个页面的顶端、浏览器标题栏、feed中显示系统标题。许多主题也显示副标题。' ) . '</p>';

if ( ! is_multisite() ) {
	$options_help .= '<p>' . __( '“GeChiUI地址”和“系统地址”可以相同（example.com），也可以不同。例如，GeChiUI的核心文件（example.com/gechiui）可以不置于根目录，而在子目录。' ) . '</p>' .
		'<p>' . __( '如果您希望允许访客自行注册（另外一种方式是由系统管理员代为注册），请勾选“成员资格”复选框。默认用户角色应用于所有新注册的用户，不论其是由管理员代为注册，抑或是自行注册。' ) . '</p>';
}

$options_help .= '<p>' . __( '您可以设置语言，翻译文件将被自动下载并安装（如果您的文件系统可写）。' ) . '</p>' .
	'<p>' . __( 'UTC是世界协调时间。' ) . '</p>' .
	'<p>' . __( '调整完成后，记得点击页面下方“保存更改”按钮使设置生效。' ) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => $options_help,
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/settings-general-screen/">常规设置文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
<div class="page-header"><h2 class="header-title"><?php echo esc_html( $title ); ?></h2></div>
<form method="post" action="options.php" novalidate="novalidate">
	<?php settings_fields( 'general' ); ?>
	<div class="card">
		<div class="card-body">
			<table class="form-table" role="presentation">

			<tr>
			<th scope="row"><label for="blogname"><?php _e( '系统标题' ); ?></label></th>
			<td><input name="blogname" type="text" id="blogname" value="<?php form_option( 'blogname' ); ?>" class="regular-text" /></td>
			</tr>

			<tr>
			<th scope="row"><label for="blogdescription"><?php _e( '副标题' ); ?></label></th>
			<td><input name="blogdescription" type="text" id="blogdescription" aria-describedby="tagline-description" value="<?php form_option( 'blogdescription' ); ?>" class="regular-text" />
			<p class="description" id="tagline-description"><?php _e( '用简洁的文字描述本系统。' ); ?></p></td>
			</tr>

			<?php
			if ( ! is_multisite() ) {
				$gc_site_url_class = '';
				$gc_home_class     = '';
				if ( defined( 'GC_SITEURL' ) ) {
					$gc_site_url_class = ' disabled';
				}
				if ( defined( 'GC_HOME' ) ) {
					$gc_home_class = ' disabled';
				}
				?>

			<tr>
			<th scope="row"><label for="siteurl"><?php _e( 'GeChiUI地址（URL）' ); ?></label></th>
			<td><input name="siteurl" type="url" id="siteurl" value="<?php form_option( 'siteurl' ); ?>"<?php disabled( defined( 'GC_SITEURL' ) ); ?> class="regular-text code<?php echo $gc_site_url_class; ?>" /></td>
			</tr>

			<tr>
			<th scope="row"><label for="home"><?php _e( '系统地址（URL）' ); ?></label></th>
			<td><input name="home" type="url" id="home" aria-describedby="home-description" value="<?php form_option( 'home' ); ?>"<?php disabled( defined( 'GC_HOME' ) ); ?> class="regular-text code<?php echo $gc_home_class; ?>" />
				<?php if ( ! defined( 'GC_HOME' ) ) : ?>
			<p class="description" id="home-description">
					<?php
					printf(
						/* translators: %s: Documentation URL. */
						__( '如果您想<a href="%s">让您的系统主页与GeChiUI安装目录不同</a>，请在此输入地址。' ),
						__( 'https://www.gechiui.com/support/giving-gechiui-its-own-directory/' )
					);
					?>
			</p>
			<?php endif; ?>
			</td>
			</tr>

			<?php } ?>

			<tr>
			<th scope="row"><label for="new_admin_email"><?php _e( '管理员电子邮箱' ); ?></label></th>
			<td><input name="new_admin_email" type="email" id="new_admin_email" aria-describedby="new-admin-email-description" value="<?php form_option( 'admin_email' ); ?>" class="regular-text ltr" />
			<p class="description" id="new-admin-email-description"><?php _e( '这个地址将被用于管理目的。如果您修改这个地址，我们将会向新电子邮箱发送一封邮件来确认。<strong>新的电子邮箱直到获得确认才会生效。</strong>' ); ?></p>
			<?php
			$new_admin_email = get_option( 'new_admin_email' );
			if ( $new_admin_email && get_option( 'admin_email' ) !== $new_admin_email ) :
				?>
				<div class="updated inline">
				<p>
				<?php
					printf(
						/* translators: %s: New admin email. */
						__( '您即将修改管理员电子邮箱为%s。' ),
						'<code>' . esc_html( $new_admin_email ) . '</code>'
					);
					printf(
						' <a href="%1$s">%2$s</a>',
						esc_url( gc_nonce_url( admin_url( 'options.php?dismiss=new_admin_email' ), 'dismiss-' . get_current_blog_id() . '-new_admin_email' ) ),
						__( '取消' )
					);
				?>
				</p>
				</div>
			<?php endif; ?>
			</td>
			</tr>

			<?php if ( ! is_multisite() ) { ?>

			<tr>
			<th scope="row"><?php _e( '成员资格' ); ?></th>
			<td> <fieldset><legend class="screen-reader-text"><span><?php _e( '成员资格' ); ?></span></legend><label for="users_can_register">
			<input name="users_can_register" type="checkbox" id="users_can_register" value="1" <?php checked( '1', get_option( 'users_can_register' ) ); ?> />
				<?php _e( '任何人都可以注册' ); ?></label>
			</fieldset></td>
			</tr>

			<tr>
			<th scope="row"><label for="default_role"><?php _e( '新用户默认角色' ); ?></label></th>
			<td>
			<select name="default_role" id="default_role"><?php gc_dropdown_roles( get_option( 'default_role' ) ); ?></select>
			</td>
			</tr>

				<?php
			}

			$languages    = get_available_languages();
			$translations = gc_get_available_translations();
			if ( ! is_multisite() && defined( 'GCLANG' ) && '' !== GCLANG && 'zh_CN' !== GCLANG && ! in_array( GCLANG, $languages, true ) ) {
				$languages[] = GCLANG;
			}

			if ( ! empty( $languages ) || ! empty( $translations ) ) {
				?>
				<tr>
					<th scope="row"><label for="GCLANG"><?php _e( '系统语言' ); ?><span class="dashicons dashicons-translation" aria-hidden="true"></span></label></th>
					<td>
						<?php
						$locale = get_locale();
						if ( ! in_array( $locale, $languages, true ) ) {
							$locale = '';
						}

						gc_dropdown_languages(
							array(
								'name'                        => 'GCLANG',
								'id'                          => 'GCLANG',
								'selected'                    => $locale,
								'languages'                   => $languages,
								'translations'                => $translations,
								'show_available_translations' => current_user_can( 'install_languages' ) && gc_can_install_language_pack(),
							)
						);

						// Add note about deprecated GCLANG constant.
						if ( defined( 'GCLANG' ) && ( '' !== GCLANG ) && GCLANG !== $locale ) {
							_deprecated_argument(
								'define()',
								'4.0.0',
								/* translators: 1: GCLANG, 2: gc-config.php */
								sprintf( __( '在您的%2$s文件中的%1$s常量已不需要。' ), 'GCLANG', 'gc-config.php' )
							);
						}
						?>
					</td>
				</tr>
				<?php
			}
			?>
			<tr>
			<?php
			$current_offset = get_option( 'gmt_offset' );
			$tzstring       = get_option( 'timezone_string' );

			$check_zone_info = true;

			// Remove old Etc mappings. Fallback to gmt_offset.
			if ( false !== strpos( $tzstring, 'Etc/GMT' ) ) {
				$tzstring = '';
			}

			if ( empty( $tzstring ) ) { // Create a UTC+- zone if no timezone string exists.
				$check_zone_info = false;
				if ( 0 == $current_offset ) {
					$tzstring = 'UTC+0';
				} elseif ( $current_offset < 0 ) {
					$tzstring = 'UTC' . $current_offset;
				} else {
					$tzstring = 'UTC+' . $current_offset;
				}
			}

			?>
			<th scope="row"><label for="timezone_string"><?php _e( '时区' ); ?></label></th>
			<td>
			<select id="timezone_string" name="timezone_string" aria-describedby="timezone-description">
				<?php echo gc_timezone_choice( $tzstring, get_user_locale() ); ?>
			</select>

			<p class="description" id="timezone-description">
			<?php
				printf(
					/* translators: %s: UTC abbreviation */
					__( '选择与您在同一时区的城市或一个%s（协调世界时）时区偏移。' ),
					'<abbr>UTC</abbr>'
				);
				?>
			</p>

			<p class="timezone-info">
				<span id="utc-time">
				<?php
					printf(
						/* translators: %s: UTC time. */
						__( '协调世界时为%s。' ),
						'<code>' . date_i18n( $timezone_format, false, true ) . '</code>'
					);
					?>
				</span>
			<?php if ( get_option( 'timezone_string' ) || ! empty( $current_offset ) ) : ?>
				<span id="local-time">
				<?php
					printf(
						/* translators: %s: Local time. */
						__( '当地时间为%s。' ),
						'<code>' . date_i18n( $timezone_format ) . '</code>'
					);
				?>
				</span>
			<?php endif; ?>
			</p>

			<?php if ( $check_zone_info && $tzstring ) : ?>
			<p class="timezone-info">
			<span>
				<?php
				$now = new DateTime( 'now', new DateTimeZone( $tzstring ) );
				$dst = (bool) $now->format( 'I' );

				if ( $dst ) {
					_e( '该时区目前使用夏令时制。' );
				} else {
					_e( '该时区当前使用标准时间。' );
				}
				?>
				<br />
				<?php
				if ( in_array( $tzstring, timezone_identifiers_list(), true ) ) {
					$transitions = timezone_transitions_get( timezone_open( $tzstring ), time() );

					// 0 index is the state at current time, 1 index is the next transition, if any.
					if ( ! empty( $transitions[1] ) ) {
						echo ' ';
						$message = $transitions[1]['isdst'] ?
							/* translators: %s: Date and time. */
							__( '日光节约时间于此开始：%s。' ) :
							/* translators: %s: Date and time. */
							__( '标准时间于此开始：%s。' );
						printf(
							$message,
							'<code>' . gc_date( __( 'Y年n月j日' ) . ' ' . __( 'ag:i' ), $transitions[1]['ts'] ) . '</code>'
						);
					} else {
						_e( '该时区不实施夏令时。' );
					}
				}
				?>
				</span>
			</p>
			<?php endif; ?>
			</td>

			</tr>
			<tr>
			<th scope="row"><?php _e( '日期格式' ); ?></th>
			<td>
				<fieldset><legend class="screen-reader-text"><span><?php _e( '日期格式' ); ?></span></legend>
			<?php
				/**
				 * Filters the default date formats.
				 *
				 *
				 * @param string[] $default_date_formats Array of default date formats.
				 */
				$date_formats = array_unique( apply_filters( 'date_formats', array( __( 'Y年n月j日' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );

				$custom = true;

			foreach ( $date_formats as $format ) {
				echo "\t<label><input type='radio' name='date_format' value='" . esc_attr( $format ) . "'";
				if ( get_option( 'date_format' ) === $format ) { // checked() uses "==" rather than "===".
					echo " checked='checked'";
					$custom = false;
				}
				echo ' /> <span class="date-time-text format-i18n">' . date_i18n( $format ) . '</span><code>' . esc_html( $format ) . "</code></label><br />\n";
			}

				echo '<label><input type="radio" name="date_format" id="date_format_custom_radio" value="\c\u\s\t\o\m"';
				checked( $custom );
				echo '/> <span class="date-time-text date-time-custom-text">' . __( '自定义：' ) . '<span class="screen-reader-text"> ' . __( '在下框中输入自定义日期格式' ) . '</span></span></label>' .
					'<label for="date_format_custom" class="screen-reader-text">' . __( '自定义日期格式：' ) . '</label>' .
					'<input type="text" name="date_format_custom" id="date_format_custom" value="' . esc_attr( get_option( 'date_format' ) ) . '" class="small-text" />' .
					'<br />' .
					'<p><strong>' . __( '预览：' ) . '</strong> <span class="example">' . date_i18n( get_option( 'date_format' ) ) . '</span>' .
					"<span class='spinner'></span>\n" . '</p>';
			?>
				</fieldset>
			</td>
			</tr>
			<tr>
			<th scope="row"><?php _e( '时间格式' ); ?></th>
			<td>
				<fieldset><legend class="screen-reader-text"><span><?php _e( '时间格式' ); ?></span></legend>
			<?php
				/**
				 * Filters the default time formats.
				 *
				 *
				 * @param string[] $default_time_formats Array of default time formats.
				 */
				$time_formats = array_unique( apply_filters( 'time_formats', array( __( 'ag:i' ), 'g:i A', 'H:i' ) ) );

				$custom = true;

			foreach ( $time_formats as $format ) {
				echo "\t<label><input type='radio' name='time_format' value='" . esc_attr( $format ) . "'";
				if ( get_option( 'time_format' ) === $format ) { // checked() uses "==" rather than "===".
					echo " checked='checked'";
					$custom = false;
				}
				echo ' /> <span class="date-time-text format-i18n">' . date_i18n( $format ) . '</span><code>' . esc_html( $format ) . "</code></label><br />\n";
			}

				echo '<label><input type="radio" name="time_format" id="time_format_custom_radio" value="\c\u\s\t\o\m"';
				checked( $custom );
				echo '/> <span class="date-time-text date-time-custom-text">' . __( '自定义：' ) . '<span class="screen-reader-text"> ' . __( '在下框中输入自定义时间格式' ) . '</span></span></label>' .
					'<label for="time_format_custom" class="screen-reader-text">' . __( '自定义时间格式：' ) . '</label>' .
					'<input type="text" name="time_format_custom" id="time_format_custom" value="' . esc_attr( get_option( 'time_format' ) ) . '" class="small-text" />' .
					'<br />' .
					'<p><strong>' . __( '预览：' ) . '</strong> <span class="example">' . date_i18n( get_option( 'time_format' ) ) . '</span>' .
					"<span class='spinner'></span>\n" . '</p>';

				echo "\t<p class='date-time-doc'>" . __( '<a href="https://www.gechiui.com/support/formatting-date-and-time/">日期和时间格式文档</a>。' ) . "</p>\n";
			?>
				</fieldset>
			</td>
			</tr>
			<tr>
			<th scope="row"><label for="start_of_week"><?php _e( '一星期开始于' ); ?></label></th>
			<td><select name="start_of_week" id="start_of_week">
			<?php
			/**
			 * @global GC_Locale $gc_locale GeChiUI date and time locale object.
			 */
			global $gc_locale;

			for ( $day_index = 0; $day_index <= 6; $day_index++ ) :
				$selected = ( get_option( 'start_of_week' ) == $day_index ) ? 'selected="selected"' : '';
				echo "\n\t<option value='" . esc_attr( $day_index ) . "' $selected>" . $gc_locale->get_weekday( $day_index ) . '</option>';
			endfor;
			?>
			</select></td>
			</tr>
			<?php do_settings_fields( 'general', 'default' ); ?>
			</table>

			<?php do_settings_sections( 'general' ); ?>

			<?php submit_button(); ?>
	 	</div>
	</div>
</form>
</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
