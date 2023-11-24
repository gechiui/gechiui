<?php
/**
 * Theme file editor administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( is_multisite() && ! is_network_admin() ) {
	gc_redirect( network_admin_url( 'theme-editor.php' ) );
	exit;
}

if ( ! current_user_can( 'edit_themes' ) ) {
	gc_die( '<p>' . __( '抱歉，您不能在此系统上编辑模板。' ) . '</p>' );
}

// Used in the HTML title tag.
$title       = __( '编辑主题' );
$parent_file = 'themes.php';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
				'<p>' . __( '您可以使用主题文件编辑器来编辑构成主题的各个 CSS 和 PHP 文件。' ) . '</p>' .
				'<p>' . __( '自下拉菜单选取您想要编辑的主题，并点击“选择”后，即可开始编辑。列表中会出现全部的模板文件。点击其中任何一个文件，即可将其载入编辑框内。' ) . '</p>' .
				'<p>' . __( '对于PHP文件，您可以使用“文档”下拉列表选择我们从文件中识别出的函数。点击“查询”按钮可查看有关该函数的参考页面。' ) . '</p>' .
				'<p id="editor-keyboard-trap-help-1">' . __( '用键盘导航时：' ) . '</p>' .
				'<ul>' .
				'<li id="editor-keyboard-trap-help-2">' . __( '在编辑区域中，Tab键将输入一个制表符。' ) . '</li>' .
				'<li id="editor-keyboard-trap-help-3">' . __( '要移开此区域，请先按Esc键再按Tab键。' ) . '</li>' .
				'<li id="editor-keyboard-trap-help-4">' . __( '致屏幕阅读器用户：在表单模式中，您可能需要按Esc键两次。' ) . '</li>' .
				'</ul>' .
				'<p>' . __( '当编辑完成时，请点击“更新文件”。' ) . '</p>' .
				'<p>' . __( '<strong>建议：</strong>如果您在线编辑正在使用的主题，请当心您的系统崩溃。' ) . '</p>' .
				'<p>' . sprintf(
					/* translators: %s: Link to documentation on child themes. */
					__( '升级新版本的主题将会导致您所做的修改被覆盖。要避免类似悲剧的发生，请考虑创建一个<a href="%s">子主题</a>。' ),
					__( 'https://developer.gechiui.com/themes/advanced-topics/child-themes/' )
				) . '</p>' .
				( is_network_admin() ? '<p>' . __( '从此页面对文件的任何编辑都将应用到SaaS平台中的所有系统。' ) . '</p>' : '' ),
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://developer.gechiui.com/themes/">主题开发文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/appearance-editor-screen/">编辑主题文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/editing-files/">文件编辑文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://developer.gechiui.com/themes/basics/template-tags/">模板标签文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

gc_reset_vars( array( 'action', 'error', 'file', 'theme' ) );

if ( $theme ) {
	$stylesheet = $theme;
} else {
	$stylesheet = get_stylesheet();
}

$theme = gc_get_theme( $stylesheet );

if ( ! $theme->exists() ) {
	gc_die( __( '请求的主题不存在。' ) );
}

if ( $theme->errors() && 'theme_no_stylesheet' === $theme->errors()->get_error_code() ) {
	gc_die( __( '请求的主题不存在。' ) . ' ' . $theme->errors()->get_error_message() );
}

$allowed_files = array();
$style_files   = array();

$file_types = gc_get_theme_file_editable_extensions( $theme );

foreach ( $file_types as $type ) {
	switch ( $type ) {
		case 'php':
			$allowed_files += $theme->get_files( 'php', -1 );
			break;
		case 'css':
			$style_files                = $theme->get_files( 'css', -1 );
			$allowed_files['style.css'] = $style_files['style.css'];
			$allowed_files             += $style_files;
			break;
		default:
			$allowed_files += $theme->get_files( $type, -1 );
			break;
	}
}

// Move functions.php and style.css to the top.
if ( isset( $allowed_files['functions.php'] ) ) {
	$allowed_files = array( 'functions.php' => $allowed_files['functions.php'] ) + $allowed_files;
}
if ( isset( $allowed_files['style.css'] ) ) {
	$allowed_files = array( 'style.css' => $allowed_files['style.css'] ) + $allowed_files;
}

if ( empty( $file ) ) {
	$relative_file = 'style.css';
	$file          = $allowed_files['style.css'];
} else {
	$relative_file = gc_unslash( $file );
	$file          = $theme->get_stylesheet_directory() . '/' . $relative_file;
}

validate_file_to_edit( $file, $allowed_files );

// Handle fallback editing of file when JavaScript is not available.
$edit_error     = null;
$posted_content = null;

if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	$r = gc_edit_theme_plugin_file( gc_unslash( $_POST ) );
	if ( is_gc_error( $r ) ) {
		$edit_error = $r;
		if ( check_ajax_referer( 'edit-theme_' . $stylesheet . '_' . $relative_file, 'nonce', false ) && isset( $_POST['newcontent'] ) ) {
			$posted_content = gc_unslash( $_POST['newcontent'] );
		}
	} else {
		gc_redirect(
			add_query_arg(
				array(
					'a'     => 1, // This means "success" for some reason.
					'theme' => $stylesheet,
					'file'  => $relative_file,
				),
				admin_url( 'theme-editor.php' )
			)
		);
		exit;
	}
}

$settings = array(
	'codeEditor' => gc_enqueue_code_editor( compact( 'file' ) ),
);
gc_enqueue_script( 'gc-theme-plugin-editor' );
gc_add_inline_script( 'gc-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { gc.themePluginEditor.init( $( "#template" ), %s ); } )', gc_json_encode( $settings ) ) );
gc_add_inline_script( 'gc-theme-plugin-editor', 'gc.themePluginEditor.themeOrPlugin = "theme";' );

update_recently_edited( $file );

if ( ! is_file( $file ) ) {
	$error = true;
}

$content = '';
if ( ! empty( $posted_content ) ) {
	$content = $posted_content;
} elseif ( ! $error && filesize( $file ) > 0 ) {
	$f       = fopen( $file, 'r' );
	$content = fread( $f, filesize( $file ) );

	if ( '.php' === substr( $file, strrpos( $file, '.' ) ) ) {
		$functions = gc_doc_link_parse( $content );

		$docs_select  = '<select name="docs-list" id="docs-list">';
		$docs_select .= '<option value="">' . esc_attr__( '函数名&hellip;' ) . '</option>';
		foreach ( $functions as $function ) {
			$docs_select .= '<option value="' . esc_attr( urlencode( $function ) ) . '">' . htmlspecialchars( $function ) . '()</option>';
		}
		$docs_select .= '</select>';
	}

	$content = esc_textarea( $content );
}

$file_description = get_file_description( $relative_file );
$file_show        = array_search( $file, array_filter( $allowed_files ), true );
$description      = esc_html( $file_description );
if ( $file_description !== $file_show ) {
	$description .= ' <span>(' . esc_html( $file_show ) . ')</span>';
}

if ( isset( $_GET['a'] ) ) {
	$message = __( '文件修改成功。' );
	add_settings_error( 'general', 'settings_updated', $message, 'success' );
} elseif ( is_gc_error( $edit_error ) ) {
	$message = __( '在试图更新文件时遇到了错误，您可能需要修正问题并重试更新。' );
	$message .= '<pre>' .esc_html( $edit_error->get_error_message() ? $edit_error->get_error_message() : $edit_error->get_error_code() ) .'</pre>';
	add_settings_error( 'general', 'settings_updated', $message, 'danger' );
}

if ( preg_match( '/\.css$/', $file ) && ! gc_is_block_theme() && current_user_can( 'customize' ) ) {
	$message = __( '您知道吗？' );
	$message .= '<p>';
	$message .= sprintf( __( '您不需要在这里修改您的CSS——您可以在<a href="%s">内建的CSS编辑器</a>中编辑并实时预览您的CSS。' ), esc_url( add_query_arg( 'autofocus[section]', 'custom_css', admin_url( 'customize.php' ) ) ) );
	$message .= '</p>';
	add_settings_error( 'general', 'settings_updated', $message, 'primary lg' );
}
if ( $theme->errors() ) {
	$message = '<strong>' . __( '该主题受损。' ) . '</strong> ' . $theme->errors()->get_error_message();
	add_settings_error( 'general', 'settings_updated', $message, 'danger' );
}
require_once ABSPATH . 'gc-admin/admin-header.php';
?>
<div class="wrap">
<div class="page-header"><h2 class="header-title"><?php echo esc_html( $title ); ?></h2></div>

<div class="fileedit-sub">
<div class="alignleft">
<h2>
	<?php
	echo $theme->display( 'Name' );
	if ( $description ) {
		echo ': ' . $description;}
	?>
</h2>
</div>
<div class="alignright">
	<form action="theme-editor.php" method="get">
		<label for="theme" id="theme-plugin-editor-selector"><?php _e( '选择要编辑的主题：' ); ?> </label>
		<select name="theme" id="theme">
		<?php
		foreach ( gc_get_themes( array( 'errors' => null ) ) as $a_stylesheet => $a_theme ) {
			if ( $a_theme->errors() && 'theme_no_stylesheet' === $a_theme->errors()->get_error_code() ) {
				continue;
			}

			$selected = ( $a_stylesheet === $stylesheet ) ? ' selected="selected"' : '';
			echo "\n\t" . '<option value="' . esc_attr( $a_stylesheet ) . '"' . $selected . '>' . $a_theme->display( 'Name' ) . '</option>';
		}
		?>
		</select>
		<?php submit_button( __( '选择' ), '', 'Submit', false ); ?>
	</form>
</div>
<br class="clear" />
</div>

<div id="templateside">
	<h2 id="theme-files-label"><?php _e( '主题文件' ); ?></h2>
	<ul role="tree" aria-labelledby="theme-files-label">
		<?php if ( $theme->parent() ) : ?>
			<li class="howto">
				<?php
				printf(
					/* translators: %s: Link to edit parent theme. */
					__( '此主题使用从父主题“%s”继承下来的模板。' ),
					sprintf(
						'<a href="%s">%s</a>',
						self_admin_url( 'theme-editor.php?theme=' . urlencode( $theme->get_template() ) ),
						$theme->parent()->display( 'Name' )
					)
				);
				?>
			</li>
		<?php endif; ?>
		<li role="treeitem" tabindex="-1" aria-expanded="true" aria-level="1" aria-posinset="1" aria-setsize="1">
			<ul role="group">
				<?php gc_print_theme_file_tree( gc_make_theme_file_tree( $allowed_files ) ); ?>
			</ul>
		</li>
	</ul>
</div>

<?php
if ( $error ) :
	echo '<div class="error"><p>' . __( '文件不存在。请检查文件名，然后再试。' ) . '</p></div>';
else :
	?>
	<form name="template" id="template" action="theme-editor.php" method="post">
		<?php gc_nonce_field( 'edit-theme_' . $stylesheet . '_' . $relative_file, 'nonce' ); ?>
		<div>
			<label for="newcontent" id="theme-plugin-editor-label"><?php _e( '选择的文件内容：' ); ?></label>
			<textarea cols="70" rows="30" name="newcontent" id="newcontent" aria-describedby="editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4"><?php echo $content; ?></textarea>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="file" value="<?php echo esc_attr( $relative_file ); ?>" />
			<input type="hidden" name="theme" value="<?php echo esc_attr( $theme->get_stylesheet() ); ?>" />
		</div>

		<?php if ( ! empty( $functions ) ) : ?>
			<div id="documentation" class="hide-if-no-js">
				<label for="docs-list"><?php _e( '文档：' ); ?></label>
				<?php echo $docs_select; ?>
				<input disabled id="docs-lookup" type="button" class="btn btn-primary btn-tone btn-sm" value="<?php esc_attr_e( '查询' ); ?>" onclick="if ( '' != jQuery('#docs-list').val() ) { window.open( 'https://api.gechiui.com/core/handbook/1.0/?function=' + escape( jQuery( '#docs-list' ).val() ) + '&amp;locale=<?php echo urlencode( get_user_locale() ); ?>&amp;version=<?php echo urlencode( get_bloginfo( 'version' ) ); ?>&amp;redirect=true'); }" />
			</div>
		<?php endif; ?>

		<div>
			<div class="editor-notices">
				<?php if ( is_child_theme() && $theme->get_stylesheet() === get_template() ) : 
						echo setting_error( __( '这是您当前父主题中的一个文件。' ), 'warning inline' );
				endif; ?>
			</div>
			<?php if ( is_writable( $file ) ) : ?>
				<p class="submit">
					<?php submit_button( __( '更新文件' ), 'primary', 'submit', false ); ?>
					<span class="spinner"></span>
				</p>
			<?php else : ?>
				<p>
					<?php
					printf(
						/* translators: %s: Documentation URL. */
						__( '在您保存修改前，您需要将此文件设置为可写。请参见<a href="%s">更改文件权限文档</a>。' ),
						__( 'https://www.gechiui.com/support/changing-file-permissions/' )
					);
					?>
				</p>
			<?php endif; ?>
		</div>

		<?php gc_print_file_editor_templates(); ?>
	</form>
	<?php
endif; // End if $error.
?>
<br class="clear" />
</div>
<?php
$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_gc_pointers', true ) );
if ( ! in_array( 'theme_editor_notice', $dismissed_pointers, true ) ) :
	// Get a back URL.
	$referer = gc_get_referer();

	$excluded_referer_basenames = array( 'theme-editor.php', 'gc-login.php' );

	$return_url = admin_url( '/' );
	if ( $referer ) {
		$referer_path = parse_url( $referer, PHP_URL_PATH );
		if ( is_string( $referer_path ) && ! in_array( basename( $referer_path ), $excluded_referer_basenames, true ) ) {
			$return_url = $referer;
		}
	}
	?>
	<div id="file-editor-warning" class="notification-dialog-wrap file-editor-warning hide-if-no-js hidden">
		<div class="notification-dialog-background"></div>
		<div class="notification-dialog">
			<div class="file-editor-warning-content">
				<div class="file-editor-warning-message">
					<h1><?php _e( '小心！' ); ?></h1>
					<p>
						<?php
						_e( '此操作可在 GeChiUI 仪表盘中直接编辑您的主题。 不推荐！ 直接编辑您的主题可能会破坏您的系统，且您的更改可能会在未来的更新中丢失。' );
						?>
					</p>
						<?php
						if ( ! $theme->parent() ) {
							echo '<p>';
							printf(
								/* translators: %s: Link to documentation on child themes. */
								__( '如果您需要修改主题CSS之外的内容，您应该试试<a href="%s">创建子主题</a>。' ),
								esc_url( __( 'https://developer.gechiui.com/themes/advanced-topics/child-themes/' ) )
							);
							echo '</p>';
						}
						?>
					<p><?php _e( '如果您仍要直接进行修改，请使用资源管理器将文件复制一份并保留修改前的版本。这样当出现问题时您就能恢复到正常的版本。' ); ?></p>
				</div>
				<p>
					<a class="btn btn-primary btn-tone file-editor-warning-go-back" href="<?php echo esc_url( $return_url ); ?>"><?php _e( '返回' ); ?></a>
					<button type="button" class="file-editor-warning-dismiss btn btn-primary"><?php _e( '我明白' ); ?></button>
				</p>
			</div>
		</div>
	</div>
	<?php
endif; // Editor warning notice.

require_once ABSPATH . 'gc-admin/admin-footer.php';
