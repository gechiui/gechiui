<?php
/**
 * List Table API: GC_Links_List_Table class
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/**
 * Core class used to implement displaying links in a list table.
 *
 *
 * @access private
 *
 * @see GC_List_Table
 */
class GC_Links_List_Table extends GC_List_Table {

	/**
	 * Constructor.
	 *
	 *
	 * @see GC_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		parent::__construct(
			array(
				'plural' => 'bookmarks',
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			)
		);
	}

	/**
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'manage_links' );
	}

	/**
	 * @global int    $cat_id
	 * @global string $s
	 * @global string $orderby
	 * @global string $order
	 */
	public function prepare_items() {
		global $cat_id, $s, $orderby, $order;

		gc_reset_vars( array( 'action', 'cat_id', 'link_id', 'orderby', 'order', 's' ) );

		$args = array(
			'hide_invisible' => 0,
			'hide_empty'     => 0,
		);

		if ( 'all' !== $cat_id ) {
			$args['category'] = $cat_id;
		}
		if ( ! empty( $s ) ) {
			$args['search'] = $s;
		}
		if ( ! empty( $orderby ) ) {
			$args['orderby'] = $orderby;
		}
		if ( ! empty( $order ) ) {
			$args['order'] = $order;
		}

		$this->items = get_bookmarks( $args );
	}

	/**
	 */
	public function no_items() {
		_e( '未找到链接。' );
	}

	/**
	 * @return array
	 */
	protected function get_bulk_actions() {
		$actions           = array();
		$actions['delete'] = __( '删除' );

		return $actions;
	}

	/**
	 * @global int $cat_id
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		global $cat_id;

		if ( 'top' !== $which ) {
			return;
		}
		?>
		<div class="alignleft actions">
			<?php
			$dropdown_options = array(
				'selected'        => $cat_id,
				'name'            => 'cat_id',
				'taxonomy'        => 'link_category',
				'show_option_all' => get_taxonomy( 'link_category' )->labels->all_items,
				'hide_empty'      => true,
				'hierarchical'    => 1,
				'show_count'      => 0,
				'orderby'         => 'name',
			);

			echo '<label class="screen-reader-text" for="cat_id">' . get_taxonomy( 'link_category' )->labels->filter_by_item . '</label>';

			gc_dropdown_categories( $dropdown_options );

			submit_button( __( '筛选' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
			?>
		</div>
		<?php
	}

	/**
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'         => '<input type="checkbox" />',
			'name'       => _x( '名称', 'link name' ),
			'url'        => __( 'URL' ),
			'categories' => __( '分类' ),
			'rel'        => __( '关系' ),
			'visible'    => __( '可见性' ),
			'rating'     => __( '评级' ),
		);
	}

	/**
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'name'    => 'name',
			'url'     => 'url',
			'visible' => 'visible',
			'rating'  => 'rating',
		);
	}

	/**
	 * Get the name of the default primary column.
	 *
	 *
	 * @return string Name of the default primary column, in this case, 'name'.
	 */
	protected function get_default_primary_column_name() {
		return 'name';
	}

	/**
	 * Handles the checkbox column output.
	 *
	 *
	 * @param object $item The current link object.
	 */
	public function column_cb( $item ) {
		// Restores the more descriptive, specific name for use within this method.
		$link = $item;

		?>
		<label class="screen-reader-text" for="cb-select-<?php echo $link->link_id; ?>">
			<?php
			/* translators: %s: Link name. */
			printf( __( '选择%s' ), $link->link_name );
			?>
		</label>
		<input type="checkbox" name="linkcheck[]" id="cb-select-<?php echo $link->link_id; ?>" value="<?php echo esc_attr( $link->link_id ); ?>" />
		<?php
	}

	/**
	 * Handles the link name column output.
	 *
	 *
	 * @param object $link The current link object.
	 */
	public function column_name( $link ) {
		$edit_link = get_edit_bookmark_link( $link );
		printf(
			'<strong><a class="row-title" href="%s" aria-label="%s">%s</a></strong>',
			$edit_link,
			/* translators: %s: Link name. */
			esc_attr( sprintf( __( '编辑“%s”' ), $link->link_name ) ),
			$link->link_name
		);
	}

	/**
	 * Handles the link URL column output.
	 *
	 *
	 * @param object $link The current link object.
	 */
	public function column_url( $link ) {
		$short_url = url_shorten( $link->link_url );
		echo "<a href='$link->link_url'>$short_url</a>";
	}

	/**
	 * Handles the link categories column output.
	 *
	 *
	 * @global int $cat_id
	 *
	 * @param object $link The current link object.
	 */
	public function column_categories( $link ) {
		global $cat_id;

		$cat_names = array();
		foreach ( $link->link_category as $category ) {
			$cat = get_term( $category, 'link_category', OBJECT, 'display' );
			if ( is_gc_error( $cat ) ) {
				echo $cat->get_error_message();
			}
			$cat_name = $cat->name;
			if ( (int) $cat_id !== $category ) {
				$cat_name = "<a href='link-manager.php?cat_id=$category'>$cat_name</a>";
			}
			$cat_names[] = $cat_name;
		}
		echo implode( ', ', $cat_names );
	}

	/**
	 * Handles the link relation column output.
	 *
	 *
	 * @param object $link The current link object.
	 */
	public function column_rel( $link ) {
		echo empty( $link->link_rel ) ? '<br />' : $link->link_rel;
	}

	/**
	 * Handles the link visibility column output.
	 *
	 *
	 * @param object $link The current link object.
	 */
	public function column_visible( $link ) {
		if ( 'Y' === $link->link_visible ) {
			_e( '是' );
		} else {
			_e( '否' );
		}
	}

	/**
	 * Handles the link rating column output.
	 *
	 *
	 * @param object $link The current link object.
	 */
	public function column_rating( $link ) {
		echo $link->link_rating;
	}

	/**
	 * Handles the default column output.
	 *
	 *
	 * @param object $item        Link object.
	 * @param string $column_name Current column name.
	 */
	public function column_default( $item, $column_name ) {
		/**
		 * Fires for each registered custom link column.
		 *
		 *
		 * @param string $column_name Name of the custom column.
		 * @param int    $link_id     Link ID.
		 */
		do_action( 'manage_link_custom_column', $column_name, $item->link_id );
	}

	public function display_rows() {
		foreach ( $this->items as $link ) {
			$link                = sanitize_bookmark( $link );
			$link->link_name     = esc_attr( $link->link_name );
			$link->link_category = gc_get_link_cats( $link->link_id );
			?>
		<tr id="link-<?php echo $link->link_id; ?>">
			<?php $this->single_row_columns( $link ); ?>
		</tr>
			<?php
		}
	}

	/**
	 * Generates and displays row action links.
	 *
	 *
	 * @param object $item        Link being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output for links, or an empty string
	 *                if the current column is not the primary column.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		// Restores the more descriptive, specific name for use within this method.
		$link      = $item;
		$edit_link = get_edit_bookmark_link( $link );

		$actions           = array();
		$actions['edit']   = '<a href="' . $edit_link . '">' . __( '编辑' ) . '</a>';
		$actions['delete'] = sprintf(
			'<a class="submitdelete" href="%s" onclick="return confirm( \'%s\' );">%s</a>',
			gc_nonce_url( "link.php?action=delete&amp;link_id=$link->link_id", 'delete-bookmark_' . $link->link_id ),
			/* translators: %s: Link name. */
			esc_js( sprintf( __( "您将要删除此链接 '%s'\n  “取消”停止，“确定”删除。" ), $link->link_name ) ),
			__( '删除' )
		);

		return $this->row_actions( $actions );
	}
}
