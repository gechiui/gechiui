<?php
/**
 * The classic widget administration screen, for use in widgets.php.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$widgets_access = get_user_setting( 'widgets_access' );
if ( isset( $_GET['widgets-access'] ) ) {
	check_admin_referer( 'widgets-access' );

	$widgets_access = 'on' === $_GET['widgets-access'] ? 'on' : 'off';
	set_user_setting( 'widgets_access', $widgets_access );
}

if ( 'on' === $widgets_access ) {
	add_filter( 'admin_body_class', 'gc_widgets_access_body_class' );
} else {
	gc_enqueue_script( 'admin-widgets' );

	if ( gc_is_mobile() ) {
		gc_enqueue_script( 'jquery-touch-punch' );
	}
}

/**
 * Fires early before the Widgets administration screen loads,
 * after scripts are enqueued.
 *
 */
do_action( 'sidebar_admin_setup' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
				'<p>' . __( '小工具是可以放置在任何边栏中的“块”。要使用小工具布置边栏，请用鼠标按住小工具的标题栏，将其拖至相应边栏的相应位置上。默认情况下，只有第一个边栏是展开的，要展开其他边栏区域，请点击它们的标题栏。' ) . '</p>
	<p>' . __( '您可从“可用小工具”区域选择需要的小工具。在拖拽其至边栏后，它将自动展开，以便您配置其设置选项。当您设置完毕后，请点击“保存”按钮，改动才会生效。点击“删除”将移除该小工具。' ) . '</p>',
	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'removing-reusing',
		'title'   => __( '移除后重新使用' ),
		'content' =>
				'<p>' . __( '如果您想移除某个小工具但保留其设置以备以后之用，只需将其拖拽到“未启用的小工具”区域中，在需要时，可随时拖回需要的边栏。这一点在您准备改用边栏数目更少的主题时很有用。' ) . '</p>
	<p>' . __( '大部分小工具可以多次使用。您可以为每个小工具起一个标题，通常这个标题会在系统中显示出来，但这不是必须的。' ) . '</p>
	<p>' . __( '在“显示选项”中启用“无障碍模式“，您就可以使用“添加”和“编辑”按钮，而无须进行拖拽。' ) . '</p>',
	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'missing-widgets',
		'title'   => __( '丢失的小工具' ),
		'content' =>
				'<p>' . __( '很多主题在用户自行设置边栏之前显示一些默认小工具，但是这些小工具不会在边栏管理工具中显示。当您对边栏做出改动后，原来的默认小工具将消失，您可以在“可用小工具”中找到它们并重新添加。' ) . '</p>' .
					'<p>' . __( '在更换主题时，边栏的数量通常各不相同，有时这些冲突会给更换主题的过程带来一些小麻烦。如果您更换主题后发现边栏小工具缺失，请看看本页面下方的“未启用”区域，您的小工具及其设置将会被保存在那里。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/appearance-widgets-screen/">小工具文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

// These are the widgets grouped by sidebar.
$sidebars_widgets = gc_get_sidebars_widgets();

if ( empty( $sidebars_widgets ) ) {
	$sidebars_widgets = gc_get_widget_defaults();
}

foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
	if ( 'gc_inactive_widgets' === $sidebar_id ) {
		continue;
	}

	if ( ! is_registered_sidebar( $sidebar_id ) ) {
		if ( ! empty( $widgets ) ) { // Register the inactive_widgets area as sidebar.
			register_sidebar(
				array(
					'name'          => __( '未启用的边栏' ),
					'id'            => $sidebar_id,
					'class'         => 'inactive-sidebar orphan-sidebar',
					'description'   => __( '这个边栏不再可用，当前不在系统的任何位置使用。要移除这个未启用的边栏，请移除其下所有的小工具。' ),
					'before_widget' => '',
					'after_widget'  => '',
					'before_title'  => '',
					'after_title'   => '',
				)
			);
		} else {
			unset( $sidebars_widgets[ $sidebar_id ] );
		}
	}
}

// Register the inactive_widgets area as sidebar.
register_sidebar(
	array(
		'name'          => __( '未启用的小工具' ),
		'id'            => 'gc_inactive_widgets',
		'class'         => 'inactive-sidebar',
		'description'   => __( '将小工具拖至这里，将它们从边栏移除，但同时保留设置。' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	)
);

retrieve_widgets();

// We're saving a widget without JS.
if ( isset( $_POST['savewidget'] ) || isset( $_POST['removewidget'] ) ) {
	$widget_id = $_POST['widget-id'];
	check_admin_referer( "save-delete-widget-$widget_id" );

	$number = isset( $_POST['multi_number'] ) ? (int) $_POST['multi_number'] : '';
	if ( $number ) {
		foreach ( $_POST as $key => $val ) {
			if ( is_array( $val ) && preg_match( '/__i__|%i%/', key( $val ) ) ) {
				$_POST[ $key ] = array( $number => array_shift( $val ) );
				break;
			}
		}
	}

	$sidebar_id = $_POST['sidebar'];
	$position   = isset( $_POST[ $sidebar_id . '_position' ] ) ? (int) $_POST[ $sidebar_id . '_position' ] - 1 : 0;

	$id_base = $_POST['id_base'];
	$sidebar = isset( $sidebars_widgets[ $sidebar_id ] ) ? $sidebars_widgets[ $sidebar_id ] : array();

	// Delete.
	if ( isset( $_POST['removewidget'] ) && $_POST['removewidget'] ) {

		if ( ! in_array( $widget_id, $sidebar, true ) ) {
			gc_redirect( admin_url( 'widgets.php?error=0' ) );
			exit;
		}

		$sidebar = array_diff( $sidebar, array( $widget_id ) );
		$_POST   = array(
			'sidebar'            => $sidebar_id,
			'widget-' . $id_base => array(),
			'the-widget-id'      => $widget_id,
			'delete_widget'      => '1',
		);

		/**
		 * Fires immediately after a widget has been marked for deletion.
		 *
		 * @param string $widget_id  ID of the widget marked for deletion.
		 * @param string $sidebar_id ID of the sidebar the widget was deleted from.
		 * @param string $id_base    ID base for the widget.
		 */
		do_action( 'delete_widget', $widget_id, $sidebar_id, $id_base );
	}

	$_POST['widget-id'] = $sidebar;

	foreach ( (array) $gc_registered_widget_updates as $name => $control ) {
		if ( $name !== $id_base || ! is_callable( $control['callback'] ) ) {
			continue;
		}

		ob_start();
			call_user_func_array( $control['callback'], $control['params'] );
		ob_end_clean();

		break;
	}

	$sidebars_widgets[ $sidebar_id ] = $sidebar;

	// Remove old position.
	if ( ! isset( $_POST['delete_widget'] ) ) {
		foreach ( $sidebars_widgets as $key => $sb ) {
			if ( is_array( $sb ) ) {
				$sidebars_widgets[ $key ] = array_diff( $sb, array( $widget_id ) );
			}
		}
		array_splice( $sidebars_widgets[ $sidebar_id ], $position, 0, $widget_id );
	}

	gc_set_sidebars_widgets( $sidebars_widgets );
	gc_redirect( admin_url( 'widgets.php?message=0' ) );
	exit;
}

// Remove inactive widgets without JS.
if ( isset( $_POST['removeinactivewidgets'] ) ) {
	check_admin_referer( 'remove-inactive-widgets', '_gcnonce_remove_inactive_widgets' );

	if ( $_POST['removeinactivewidgets'] ) {
		foreach ( $sidebars_widgets['gc_inactive_widgets'] as $key => $widget_id ) {
			$pieces       = explode( '-', $widget_id );
			$multi_number = array_pop( $pieces );
			$id_base      = implode( '-', $pieces );
			$widget       = get_option( 'widget_' . $id_base );
			unset( $widget[ $multi_number ] );
			update_option( 'widget_' . $id_base, $widget );
			unset( $sidebars_widgets['gc_inactive_widgets'][ $key ] );
		}

		gc_set_sidebars_widgets( $sidebars_widgets );
	}

	gc_redirect( admin_url( 'widgets.php?message=0' ) );
	exit;
}

// Output the widget form without JS.
if ( isset( $_GET['editwidget'] ) && $_GET['editwidget'] ) {
	$widget_id = $_GET['editwidget'];

	if ( isset( $_GET['addnew'] ) ) {
		// Default to the first sidebar.
		$keys    = array_keys( $gc_registered_sidebars );
		$sidebar = reset( $keys );

		if ( isset( $_GET['base'] ) && isset( $_GET['num'] ) ) { // Multi-widget.
			// Copy minimal info from an existing instance of this widget to a new instance.
			foreach ( $gc_registered_widget_controls as $control ) {
				if ( $_GET['base'] === $control['id_base'] ) {
					$control_callback                                = $control['callback'];
					$multi_number                                    = (int) $_GET['num'];
					$control['params'][0]['number']                  = -1;
					$control['id']                                   = $control['id_base'] . '-' . $multi_number;
					$widget_id                                       = $control['id'];
					$gc_registered_widget_controls[ $control['id'] ] = $control;
					break;
				}
			}
		}
	}

	if ( isset( $gc_registered_widget_controls[ $widget_id ] ) && ! isset( $control ) ) {
		$control          = $gc_registered_widget_controls[ $widget_id ];
		$control_callback = $control['callback'];
	} elseif ( ! isset( $gc_registered_widget_controls[ $widget_id ] ) && isset( $gc_registered_widgets[ $widget_id ] ) ) {
		$name = esc_html( strip_tags( $gc_registered_widgets[ $widget_id ]['name'] ) );
	}

	if ( ! isset( $name ) ) {
		$name = esc_html( strip_tags( $control['name'] ) );
	}

	if ( ! isset( $sidebar ) ) {
		$sidebar = isset( $_GET['sidebar'] ) ? $_GET['sidebar'] : 'gc_inactive_widgets';
	}

	if ( ! isset( $multi_number ) ) {
		$multi_number = isset( $control['params'][0]['number'] ) ? $control['params'][0]['number'] : '';
	}

	$id_base = isset( $control['id_base'] ) ? $control['id_base'] : $control['id'];

	// Show the widget form.
	$width = ' style="width:' . max( $control['width'], 350 ) . 'px"';
	$key   = isset( $_GET['key'] ) ? (int) $_GET['key'] : 0;

	require_once ABSPATH . 'gc-admin/admin-header.php'; ?>
	<div class="wrap">
	<div class="page-header"><h2 class="header-title"><?php echo esc_html( $title ); ?></h2></div>
	<div class="editwidget"<?php echo $width; ?>>
	<h2>
	<?php
	/* translators: %s: Widget name. */
	printf( __( '小工具%s' ), $name );
	?>
	</h2>

	<form action="widgets.php" method="post">
	<div class="widget-inside">
	<?php
	if ( is_callable( $control_callback ) ) {
		call_user_func_array( $control_callback, $control['params'] );
	} else {
		echo '<p>' . __( '这个小工具没有提供选项。' ) . "</p>\n";
	}
	?>
	</div>

	<p class="describe"><?php _e( '请选择将小工具放置于哪个边栏，然后指定它在边栏中的位置。' ); ?></p>
	<div class="widget-position">
	<table class="widefat"><thead><tr><th><?php _e( '边栏' ); ?></th><th><?php _e( '位置' ); ?></th></tr></thead><tbody>
	<?php
	foreach ( $gc_registered_sidebars as $sbname => $sbvalue ) {
		echo "\t\t<tr><td><label><input type='radio' name='sidebar' value='" . esc_attr( $sbname ) . "'" . checked( $sbname, $sidebar, false ) . " /> $sbvalue[name]</label></td><td>";
		if ( 'gc_inactive_widgets' === $sbname || 'orphaned_widgets' === substr( $sbname, 0, 16 ) ) {
			echo '&nbsp;';
		} else {
			if ( ! isset( $sidebars_widgets[ $sbname ] ) || ! is_array( $sidebars_widgets[ $sbname ] ) ) {
				$j                           = 1;
				$sidebars_widgets[ $sbname ] = array();
			} else {
				$j = count( $sidebars_widgets[ $sbname ] );
				if ( isset( $_GET['addnew'] ) || ! in_array( $widget_id, $sidebars_widgets[ $sbname ], true ) ) {
					$j++;
				}
			}
			$selected = '';
			echo "\t\t<select name='{$sbname}_position'>\n";
			echo "\t\t<option value=''>" . __( '&mdash;选择&mdash;' ) . "</option>\n";
			for ( $i = 1; $i <= $j; $i++ ) {
				if ( in_array( $widget_id, $sidebars_widgets[ $sbname ], true ) ) {
					$selected = selected( $i, $key + 1, false );
				}
				echo "\t\t<option value='$i'$selected> $i </option>\n";
			}
			echo "\t\t</select>\n";
		}
		echo "</td></tr>\n";
	}
	?>
	</tbody></table>
	</div>

	<div class="widget-control-actions">
		<div class="alignleft">
			<?php if ( ! isset( $_GET['addnew'] ) ) : ?>
				<input type="submit" name="removewidget" id="removewidget" class="button-link button-link-delete widget-control-remove" value="<?php _e( '删除' ); ?>" />
				<span class="widget-control-close-wrapper">
					| <a href="widgets.php" class="button-link widget-control-close"><?php _e( '取消' ); ?></a>
				</span>
			<?php else : ?>
				<a href="widgets.php" class="button-link widget-control-close"><?php _e( '取消' ); ?></a>
			<?php endif; ?>
		</div>
		<div class="alignright">
			<?php submit_button( __( '保存小工具' ), 'primary alignright', 'savewidget', false ); ?>
			<input type="hidden" name="widget-id" class="widget-id" value="<?php echo esc_attr( $widget_id ); ?>" />
			<input type="hidden" name="id_base" class="id_base" value="<?php echo esc_attr( $id_base ); ?>" />
			<input type="hidden" name="multi_number" class="multi_number" value="<?php echo esc_attr( $multi_number ); ?>" />
			<?php gc_nonce_field( "save-delete-widget-$widget_id" ); ?>
		</div>
		<br class="clear" />
	</div>

	</form>
	</div>
	</div>
	<?php
	require_once ABSPATH . 'gc-admin/admin-footer.php';
	exit;
}

$messages = array(
	__( '更改已保存。' ),
);

$errors = array(
	__( '保存时发生错误。' ),
	__( '显示小工具设置页时发生错误。' ),
);

if ( isset( $_GET['message'] ) && isset( $messages[ $_GET['message'] ] ) ) {
	add_settings_error( 'general', 'settings_updated', $messages[ $_GET['message'] ], 'success' );
}
if ( isset( $_GET['error'] ) && isset( $errors[ $_GET['error'] ] ) ) {
	add_settings_error( 'general', 'settings_updated', $errors[ $_GET['error'] ], 'danger' );
}

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
	<div class="page-header">
		<h2 class="header-title"><?php echo esc_html( $title ); ?></h2>
		<?php
		if ( current_user_can( 'customize' ) ) {
			printf(
				' <a class="btn btn-primary btn-tone btn-sm hide-if-no-customize" href="%1$s">%2$s</a>',
				esc_url(
					add_query_arg(
						array(
							array( 'autofocus' => array( 'panel' => 'widgets' ) ),
							'return' => urlencode( remove_query_arg( gc_removable_query_args(), gc_unslash( $_SERVER['REQUEST_URI'] ) ) ),
						),
						admin_url( 'customize.php' )
					)
				),
				__( '使用实时预览管理' )
			);
		}

		$nonce = gc_create_nonce( 'widgets-access' );
		?>
	</div>
<div class="widget-access-link">
	<a id="access-on" href="widgets.php?widgets-access=on&_gcnonce=<?php echo urlencode( $nonce ); ?>"><?php _e( '启用无障碍模式' ); ?></a><a id="access-off" href="widgets.php?widgets-access=off&_gcnonce=<?php echo urlencode( $nonce ); ?>"><?php _e( '停用无障碍模式' ); ?></a>
</div>

<?php
/**
 * Fires before the Widgets administration page content loads.
 *
 */
do_action( 'widgets_admin_page' );
?>

<div class="widget-liquid-left">
<div id="widgets-left">
	<div id="available-widgets" class="widgets-holder-wrap">
		<div class="sidebar-name">
			<button type="button" class="handlediv hide-if-no-js" aria-expanded="true">
				<span class="screen-reader-text"><?php _e( '可用小工具' ); ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>
			<h4><?php _e( '可用小工具' ); ?> <span id="removing-widget"><?php _ex( '禁用', 'removing-widget' ); ?> <span></span></span></h4>
		</div>
		<div class="widget-holder">
			<div class="sidebar-description">
				<p class="description"><?php _e( '要启用某一小工具，将它拖动到侧栏或点击它。要禁用某一小工具并删除其设置，将它拖回来。' ); ?></p>
			</div>
			<div id="widget-list">
				<?php gc_list_widgets(); ?>
			</div>
			<br class='clear' />
		</div>
		<br class="clear" />
	</div>

<?php

$theme_sidebars = array();
foreach ( $gc_registered_sidebars as $sidebar => $registered_sidebar ) {
	if ( false !== strpos( $registered_sidebar['class'], 'inactive-sidebar' ) || 'orphaned_widgets' === substr( $sidebar, 0, 16 ) ) {
		$wrap_class = 'widgets-holder-wrap';
		if ( ! empty( $registered_sidebar['class'] ) ) {
			$wrap_class .= ' ' . $registered_sidebar['class'];
		}

		$is_inactive_widgets = 'gc_inactive_widgets' === $registered_sidebar['id'];
		?>
		<div class="<?php echo esc_attr( $wrap_class ); ?>">
			<div class="widget-holder inactive">
				<?php gc_list_widget_controls( $registered_sidebar['id'], $registered_sidebar['name'] ); ?>

				<?php if ( $is_inactive_widgets ) { ?>
				<div class="remove-inactive-widgets">
					<form action="" method="post">
						<p>
							<?php
							$attributes = array( 'id' => 'inactive-widgets-control-remove' );

							if ( empty( $sidebars_widgets['gc_inactive_widgets'] ) ) {
								$attributes['disabled'] = '';
							}

							submit_button( __( '清理未启用的小工具' ), 'delete', 'removeinactivewidgets', false, $attributes );
							?>
							<span class="spinner"></span>
						</p>
						<?php gc_nonce_field( 'remove-inactive-widgets', '_gcnonce_remove_inactive_widgets' ); ?>
					</form>
				</div>
				<?php } ?>
			</div>
			<?php if ( $is_inactive_widgets ) { ?>
			<p class="description"><?php _e( '这会从未启用的小工具列表中清除所有项目，您将无法还原任何自定义选项。' ); ?></p>
			<?php } ?>
		</div>
		<?php

	} else {
		$theme_sidebars[ $sidebar ] = $registered_sidebar;
	}
}

?>
</div>
</div>
<?php

$i                    = 0;
$split                = 0;
$single_sidebar_class = '';
$sidebars_count       = count( $theme_sidebars );

if ( $sidebars_count > 1 ) {
	$split = (int) ceil( $sidebars_count / 2 );
} else {
	$single_sidebar_class = ' single-sidebar';
}

?>
<div class="widget-liquid-right">
<div id="widgets-right" class="gc-clearfix<?php echo $single_sidebar_class; ?>">
<div class="sidebars-column-1">
<?php

foreach ( $theme_sidebars as $sidebar => $registered_sidebar ) {
	$wrap_class = 'widgets-holder-wrap';
	if ( ! empty( $registered_sidebar['class'] ) ) {
		$wrap_class .= ' sidebar-' . $registered_sidebar['class'];
	}

	if ( $i > 0 ) {
		$wrap_class .= ' closed';
	}

	if ( $split && $i === $split ) {
		?>
		</div><div class="sidebars-column-2">
		<?php
	}

	?>
	<div class="<?php echo esc_attr( $wrap_class ); ?>">
		<?php
		// Show the control forms for each of the widgets in this sidebar.
		gc_list_widget_controls( $sidebar, $registered_sidebar['name'] );
		?>
	</div>
	<?php

	$i++;
}

?>
</div>
</div>
</div>
<form method="post">
<?php gc_nonce_field( 'save-sidebar-widgets', '_gcnonce_widgets', false ); ?>
</form>
<br class="clear" />
</div>

<div class="widgets-chooser">
	<ul class="widgets-chooser-sidebars"></ul>
	<div class="widgets-chooser-actions">
		<button class="btn btn-primary btn-tone widgets-chooser-cancel"><?php _e( '取消' ); ?></button>
		<button class="btn btn-primary widgets-chooser-add"><?php _e( '添加小工具' ); ?></button>
	</div>
</div>

<?php

/**
 * Fires after the available widgets and sidebars have loaded, before the admin footer.
 *
 */
do_action( 'sidebar_admin_page' );
require_once ABSPATH . 'gc-admin/admin-footer.php';
