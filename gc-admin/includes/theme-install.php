<?php
/**
 * GeChiUI Theme Installation Administration API
 *
 * @package GeChiUI
 * @subpackage Administration
 */

$themes_allowedtags = array(
	'a'       => array(
		'href'   => array(),
		'title'  => array(),
		'target' => array(),
	),
	'abbr'    => array( 'title' => array() ),
	'acronym' => array( 'title' => array() ),
	'code'    => array(),
	'pre'     => array(),
	'em'      => array(),
	'strong'  => array(),
	'div'     => array(),
	'p'       => array(),
	'ul'      => array(),
	'ol'      => array(),
	'li'      => array(),
	'h1'      => array(),
	'h2'      => array(),
	'h3'      => array(),
	'h4'      => array(),
	'h5'      => array(),
	'h6'      => array(),
	'img'     => array(
		'src'   => array(),
		'class' => array(),
		'alt'   => array(),
	),
);

$theme_field_defaults = array(
	'description'  => true,
	'sections'     => false,
	'tested'       => true,
	'requires'     => true,
	'rating'       => true,
	'downloaded'   => true,
	'downloadlink' => true,
	'last_updated' => true,
	'homepage'     => true,
	'tags'         => true,
	'num_ratings'  => true,
);

/**
 * Retrieves the list of GeChiUI theme features (aka theme tags).
 *
 * @deprecated 3.1.0 Use get_theme_feature_list() instead.
 *
 * @return array
 */
function install_themes_feature_list() {
	_deprecated_function( __FUNCTION__, '3.1.0', 'get_theme_feature_list()' );

	$cache = get_transient( 'gcorg_theme_feature_list' );
	if ( ! $cache ) {
		set_transient( 'gcorg_theme_feature_list', array(), 3 * HOUR_IN_SECONDS );
	}

	if ( $cache ) {
		return $cache;
	}

	$feature_list = themes_api( 'feature_list', array() );
	if ( is_gc_error( $feature_list ) ) {
		return array();
	}

	set_transient( 'gcorg_theme_feature_list', $feature_list, 3 * HOUR_IN_SECONDS );

	return $feature_list;
}

/**
 * Displays search form for searching themes.
 *
 * @param bool $type_selector
 */
function install_theme_search_form( $type_selector = true ) {
	$type = isset( $_REQUEST['type'] ) ? gc_unslash( $_REQUEST['type'] ) : 'term';
	$term = isset( $_REQUEST['s'] ) ? gc_unslash( $_REQUEST['s'] ) : '';
	if ( ! $type_selector ) {
		echo '<p class="install-help">' . __( '根据关键词搜索主题。' ) . '</p>';
	}
	?>
<form id="search-themes" method="get">
	<input type="hidden" name="tab" value="search" />
	<?php if ( $type_selector ) : ?>
	<label class="screen-reader-text" for="typeselector">
		<?php
		/* translators: Hidden accessibility text. */
		_e( '搜索类型' );
		?>
	</label>
	<select	name="type" id="typeselector">
	<option value="term" <?php selected( 'term', $type ); ?>><?php _e( '关键字' ); ?></option>
	<option value="tag" <?php selected( 'tag', $type ); ?>><?php _ex( 'Tag', 'Theme Installer' ); ?></option>
	</select>
	<label class="screen-reader-text" for="s">
		<?php
		switch ( $type ) {
			case 'term':
				/* translators: Hidden accessibility text. */
				_e( '根据关键词查找' );
				break;
			case 'author':
				/* translators: Hidden accessibility text. */
				_e( '根据作者查找' );
				break;
			case 'tag':
				/* translators: Hidden accessibility text. */
				_e( '根据标签查找' );
				break;
		}
		?>
	</label>
	<?php else : ?>
	<label class="screen-reader-text" for="s">
		<?php
		/* translators: Hidden accessibility text. */
		_e( '根据关键词查找' );
		?>
	</label>
	<?php endif; ?>
	<input type="search" name="s" id="s" size="30" value="<?php echo esc_attr( $term ); ?>" autofocus="autofocus" />
	<?php submit_button( __( '搜索' ), '', 'search', false ); ?>
</form>
	<?php
}

/**
 * Displays tags filter for themes.
 *
 */
function install_themes_dashboard() {
	install_theme_search_form( false );
	?>
<h4><?php _e( '特性筛选' ); ?></h4>
<p class="install-help"><?php _e( '根据主题特性寻找主题。' ); ?></p>

<form method="get">
	<input type="hidden" name="tab" value="search" />
	<?php
	$feature_list = get_theme_feature_list();
	echo '<div class="feature-filter">';

	foreach ( (array) $feature_list as $feature_name => $features ) {
		$feature_name = esc_html( $feature_name );
		echo '<div class="feature-name">' . $feature_name . '</div>';

		echo '<ol class="feature-group">';
		foreach ( $features as $feature => $feature_name ) {
			$feature_name = esc_html( $feature_name );
			$feature      = esc_attr( $feature );
			?>

<li>
	<input type="checkbox" name="features[]" id="feature-id-<?php echo $feature; ?>" value="<?php echo $feature; ?>" />
	<label for="feature-id-<?php echo $feature; ?>"><?php echo $feature_name; ?></label>
</li>

<?php	} ?>
</ol>
<br class="clear" />
		<?php
	}
	?>

</div>
<br class="clear" />
	<?php submit_button( __( '寻找主题' ), '', 'search' ); ?>
</form>
	<?php
}

/**
 * Displays a form to upload themes from zip files.
 *
 */
function install_themes_upload() {
	?>
<p class="install-help"><?php _e( '如果您有.zip格式的主题，可以在这里通过上传的方式安装。' ); ?></p>
<form method="post" enctype="multipart/form-data" class="gc-upload-form" action="<?php echo esc_url( self_admin_url( 'update.php?action=upload-theme' ) ); ?>">
	<?php gc_nonce_field( 'theme-upload' ); ?>
	<label class="screen-reader-text" for="themezip">
		<?php
		/* translators: Hidden accessibility text. */
		_e( '主题压缩文件' );
		?>
	</label>
	<input type="file" id="themezip" name="themezip" accept=".zip" />
	<?php submit_button( __( '立即安装' ), '', 'install-theme-submit', false ); ?>
</form>
	<?php
}

/**
 * Prints a theme on the Install Themes pages.
 *
 * @deprecated 3.4.0
 *
 * @global GC_Theme_Install_List_Table $gc_list_table
 *
 * @param object $theme
 */
function display_theme( $theme ) {
	_deprecated_function( __FUNCTION__, '3.4.0' );
	global $gc_list_table;
	if ( ! isset( $gc_list_table ) ) {
		$gc_list_table = _get_list_table( 'GC_Theme_Install_List_Table' );
	}
	$gc_list_table->prepare_items();
	$gc_list_table->single_row( $theme );
}

/**
 * Displays theme content based on theme list.
 *
 * @global GC_Theme_Install_List_Table $gc_list_table
 */
function display_themes() {
	global $gc_list_table;

	if ( ! isset( $gc_list_table ) ) {
		$gc_list_table = _get_list_table( 'GC_Theme_Install_List_Table' );
	}
	$gc_list_table->prepare_items();
	$gc_list_table->display();

}

/**
 * Displays theme information in dialog box form.
 *
 * @global GC_Theme_Install_List_Table $gc_list_table
 */
function install_theme_information() {
	global $gc_list_table;

	$theme = themes_api( 'theme_information', array( 'slug' => gc_unslash( $_REQUEST['theme'] ) ) );

	if ( is_gc_error( $theme ) ) {
		gc_die( $theme );
	}

	iframe_header( __( '安装主题' ) );
	if ( ! isset( $gc_list_table ) ) {
		$gc_list_table = _get_list_table( 'GC_Theme_Install_List_Table' );
	}
	$gc_list_table->theme_installer_single( $theme );
	iframe_footer();
	exit;
}
