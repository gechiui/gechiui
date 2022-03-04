<?php
/**
 * Edit plugin file editor administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( is_multisite() && ! is_network_admin() ) {
	gc_redirect( network_admin_url( 'plugin-editor.php' ) );
	exit;
}

if ( ! current_user_can( 'edit_plugins' ) ) {
	gc_die( __( '抱歉，您不能编辑此站点的插件。' ) );
}

// Used in the HTML title tag.
$title       = __( '编辑插件' );
$parent_file = 'plugins.php';

$plugins = get_plugins();

if ( empty( $plugins ) ) {
	require_once ABSPATH . 'gc-admin/admin-header.php';
	?>
	<div class="wrap">
		<h1><?php echo esc_html( $title ); ?></h1>
		<div id="message" class="error"><p><?php _e( '当前没有可用的插件。' ); ?></p></div>
	</div>
	<?php
	require_once ABSPATH . 'gc-admin/admin-footer.php';
	exit;
}

$file   = '';
$plugin = '';
if ( isset( $_REQUEST['file'] ) ) {
	$file = gc_unslash( $_REQUEST['file'] );
}

if ( isset( $_REQUEST['plugin'] ) ) {
	$plugin = gc_unslash( $_REQUEST['plugin'] );
}

if ( empty( $plugin ) ) {
	if ( $file ) {

		// Locate the plugin for a given plugin file being edited.
		$file_dirname = dirname( $file );
		foreach ( array_keys( $plugins ) as $plugin_candidate ) {
			if ( $plugin_candidate === $file || ( '.' !== $file_dirname && dirname( $plugin_candidate ) === $file_dirname ) ) {
				$plugin = $plugin_candidate;
				break;
			}
		}

		// Fallback to the file as the plugin.
		if ( empty( $plugin ) ) {
			$plugin = $file;
		}
	} else {
		$plugin = array_keys( $plugins );
		$plugin = $plugin[0];
	}
}

$plugin_files = get_plugin_files( $plugin );

if ( empty( $file ) ) {
	$file = $plugin_files[0];
}

$file      = validate_file_to_edit( $file, $plugin_files );
$real_file = GC_PLUGIN_DIR . '/' . $file;

// Handle fallback editing of file when JavaScript is not available.
$edit_error     = null;
$posted_content = null;

if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	$r = gc_edit_theme_plugin_file( gc_unslash( $_POST ) );
	if ( is_gc_error( $r ) ) {
		$edit_error = $r;
		if ( check_ajax_referer( 'edit-plugin_' . $file, 'nonce', false ) && isset( $_POST['newcontent'] ) ) {
			$posted_content = gc_unslash( $_POST['newcontent'] );
		}
	} else {
		gc_redirect(
			add_query_arg(
				array(
					'a'      => 1, // This means "success" for some reason.
					'plugin' => $plugin,
					'file'   => $file,
				),
				admin_url( 'plugin-editor.php' )
			)
		);
		exit;
	}
}

// List of allowable extensions.
$editable_extensions = gc_get_plugin_file_editable_extensions( $plugin );

if ( ! is_file( $real_file ) ) {
	gc_die( sprintf( '<p>%s</p>', __( '文件不存在。请检查文件名，然后再试。' ) ) );
} else {
	// Get the extension of the file.
	if ( preg_match( '/\.([^.]+)$/', $real_file, $matches ) ) {
		$ext = strtolower( $matches[1] );
		// If extension is not in the acceptable list, skip it.
		if ( ! in_array( $ext, $editable_extensions, true ) ) {
			gc_die( sprintf( '<p>%s</p>', __( '无法编辑该类型的文件。' ) ) );
		}
	}
}

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
				'<p>' . __( '您可以使用插件文件编辑器对插件的单个 PHP 文件进行修改。 请注意，如果您进行了修改，插件更新后将会覆盖您的自定义修改。' ) . '</p>' .
				'<p>' . __( '请从下拉菜单中选择要编辑的插件，然后点选您希望编辑的文件。在编辑完成后，请您不要忘记保存（点击“更新文件”按钮）。' ) . '</p>' .
				'<p>' . __( '“文档”菜单列出了我们从该文件中找到的所有函数。点击“查询”按钮可查看有关该函数的页面。' ) . '</p>' .
				'<p id="editor-keyboard-trap-help-1">' . __( '用键盘导航时：' ) . '</p>' .
				'<ul>' .
				'<li id="editor-keyboard-trap-help-2">' . __( '在编辑区域中，Tab键将输入一个制表符。' ) . '</li>' .
				'<li id="editor-keyboard-trap-help-3">' . __( '要移开此区域，请先按Esc键再按Tab键。' ) . '</li>' .
				'<li id="editor-keyboard-trap-help-4">' . __( '致屏幕阅读器用户：在表单模式中，您可能需要按Esc键两次。' ) . '</li>' .
				'</ul>' .
				'<p>' . __( '若您不希望您所做的修改因插件升级而被覆盖，请考虑自己编写插件。右侧的链接提供了自行制作插件的一些方法和指导。' ) . '</p>' .
				( is_network_admin() ? '<p>' . __( '从此页面对文件的任何编辑都将应用到站点网络中的所有站点。' ) . '</p>' : '' ),
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/plugins-editor-screen/">编辑插件文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://developer.gechiui.com/plugins/">编写插件文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

$settings = array(
	'codeEditor' => gc_enqueue_code_editor( array( 'file' => $real_file ) ),
);
gc_enqueue_script( 'gc-theme-plugin-editor' );
gc_add_inline_script( 'gc-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { gc.themePluginEditor.init( $( "#template" ), %s ); } )', gc_json_encode( $settings ) ) );
gc_add_inline_script( 'gc-theme-plugin-editor', sprintf( 'gc.themePluginEditor.themeOrPlugin = "plugin";' ) );

require_once ABSPATH . 'gc-admin/admin-header.php';

update_recently_edited( GC_PLUGIN_DIR . '/' . $file );

if ( ! empty( $posted_content ) ) {
	$content = $posted_content;
} else {
	$content = file_get_contents( $real_file );
}

if ( '.php' === substr( $real_file, strrpos( $real_file, '.' ) ) ) {
	$functions = gc_doc_link_parse( $content );

	if ( ! empty( $functions ) ) {
		$docs_select  = '<select name="docs-list" id="docs-list">';
		$docs_select .= '<option value="">' . __( '函数名&hellip;' ) . '</option>';
		foreach ( $functions as $function ) {
			$docs_select .= '<option value="' . esc_attr( $function ) . '">' . esc_html( $function ) . '()</option>';
		}
		$docs_select .= '</select>';
	}
}

$content = esc_textarea( $content );
?>
<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<?php if ( isset( $_GET['a'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible">
		<p><?php _e( '文件修改成功。' ); ?></p>
	</div>
<?php elseif ( is_gc_error( $edit_error ) ) : ?>
	<div id="message" class="notice notice-error">
		<p><?php _e( '在试图更新文件时遇到了错误，您可能需要修正问题并重试更新。' ); ?></p>
		<pre><?php echo esc_html( $edit_error->get_error_message() ? $edit_error->get_error_message() : $edit_error->get_error_code() ); ?></pre>
	</div>
<?php endif; ?>

<div class="fileedit-sub">
<div class="alignleft">
<h2>
	<?php
	if ( is_plugin_active( $plugin ) ) {
		if ( is_writable( $real_file ) ) {
			/* translators: %s: Plugin file name. */
			printf( __( '正在编辑%s（已启用）' ), '<strong>' . esc_html( $file ) . '</strong>' );
		} else {
			/* translators: %s: Plugin file name. */
			printf( __( '正在浏览%s（已启用）' ), '<strong>' . esc_html( $file ) . '</strong>' );
		}
	} else {
		if ( is_writable( $real_file ) ) {
			/* translators: %s: Plugin file name. */
			printf( __( '正在编辑%s（未启用）' ), '<strong>' . esc_html( $file ) . '</strong>' );
		} else {
			/* translators: %s: Plugin file name. */
			printf( __( '正在浏览%s（未启用）' ), '<strong>' . esc_html( $file ) . '</strong>' );
		}
	}
	?>
</h2>
</div>
<div class="alignright">
	<form action="plugin-editor.php" method="get">
		<label for="plugin" id="theme-plugin-editor-selector"><?php _e( '选择要编辑的插件：' ); ?> </label>
		<select name="plugin" id="plugin">
		<?php
		foreach ( $plugins as $plugin_key => $a_plugin ) {
			$plugin_name = $a_plugin['Name'];
			if ( $plugin_key === $plugin ) {
				$selected = " selected='selected'";
			} else {
				$selected = '';
			}
			$plugin_name = esc_attr( $plugin_name );
			$plugin_key  = esc_attr( $plugin_key );
			echo "\n\t<option value=\"$plugin_key\" $selected>$plugin_name</option>";
		}
		?>
		</select>
		<?php submit_button( __( '选择' ), '', 'Submit', false ); ?>
	</form>
</div>
<br class="clear" />
</div>

<div id="templateside">
	<h2 id="plugin-files-label"><?php _e( '插件文件' ); ?></h2>

	<?php
	$plugin_editable_files = array();
	foreach ( $plugin_files as $plugin_file ) {
		if ( preg_match( '/\.([^.]+)$/', $plugin_file, $matches ) && in_array( $matches[1], $editable_extensions, true ) ) {
			$plugin_editable_files[] = $plugin_file;
		}
	}
	?>
	<ul role="tree" aria-labelledby="plugin-files-label">
	<li role="treeitem" tabindex="-1" aria-expanded="true" aria-level="1" aria-posinset="1" aria-setsize="1">
		<ul role="group">
			<?php gc_print_plugin_file_tree( gc_make_plugin_file_tree( $plugin_editable_files ) ); ?>
		</ul>
	</ul>
</div>

<form name="template" id="template" action="plugin-editor.php" method="post">
	<?php gc_nonce_field( 'edit-plugin_' . $file, 'nonce' ); ?>
	<div>
		<label for="newcontent" id="theme-plugin-editor-label"><?php _e( '选择的文件内容：' ); ?></label>
		<textarea cols="70" rows="25" name="newcontent" id="newcontent" aria-describedby="editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4"><?php echo $content; ?></textarea>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="file" value="<?php echo esc_attr( $file ); ?>" />
		<input type="hidden" name="plugin" value="<?php echo esc_attr( $plugin ); ?>" />
	</div>

	<?php if ( ! empty( $docs_select ) ) : ?>
		<div id="documentation" class="hide-if-no-js">
			<label for="docs-list"><?php _e( '文档：' ); ?></label>
			<?php echo $docs_select; ?>
			<input disabled id="docs-lookup" type="button" class="button" value="<?php esc_attr_e( '查询' ); ?>" onclick="if ( '' != jQuery('#docs-list').val() ) { window.open( 'https://api.gechiui.com/core/handbook/1.0/?function=' + escape( jQuery( '#docs-list' ).val() ) + '&amp;locale=<?php echo urlencode( get_user_locale() ); ?>&amp;version=<?php echo urlencode( get_bloginfo( 'version' ) ); ?>&amp;redirect=true'); }" />
		</div>
	<?php endif; ?>

	<?php if ( is_writable( $real_file ) ) : ?>
		<div class="editor-notices">
		<?php if ( in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ) { ?>
			<div class="notice notice-warning inline active-plugin-edit-warning">
				<p><?php _e( '<strong>警告：</strong>不推荐修改已启用的插件。' ); ?></p>
			</div>
		<?php } ?>
		</div>
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

	<?php gc_print_file_editor_templates(); ?>
</form>
<br class="clear" />
</div>
<?php
$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_gc_pointers', true ) );
if ( ! in_array( 'plugin_editor_notice', $dismissed_pointers, true ) ) :
	// Get a back URL.
	$referer = gc_get_referer();

	$excluded_referer_basenames = array( 'plugin-editor.php', 'gc-login.php' );

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
					<p><?php _e( '此操作可在 GeChiUI 仪表盘中直接编辑您的插件。不建议直接编辑插件，直接编辑插件可能会引入不兼容的更改而使站点故障，且您的修改可能会在未来的更新中丢失。' ); ?></p>
					<p><?php _e( '如果您必须直接修改此插件，请使用资源管理器将文件复制一份并保留修改前的版本。这样当出现问题时您就能恢复到正常的版本。' ); ?></p>
				</div>
				<p>
					<a class="button file-editor-warning-go-back" href="<?php echo esc_url( $return_url ); ?>"><?php _e( '返回' ); ?></a>
					<button type="button" class="file-editor-warning-dismiss button button-primary"><?php _e( '我明白' ); ?></button>
				</p>
			</div>
		</div>
	</div>
	<?php
endif; // Editor warning notice.

require_once ABSPATH . 'gc-admin/admin-footer.php';
