<?php
/**
 * List Table API: GC_AppKeys_List_Table class
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/**
 * Class for displaying the list of appkey items.
 *
 *
 * @access private
 *
 * @see GC_List_Table
 */
class GC_AppKeys_List_Table extends GC_List_Table {

	/**
	 * Gets the list of columns.
	 *
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'name'      => __( '显示名称' ),
			'created'   => __( '创建日期' ),
			'last_used' => __( '最后使用' ),
			'last_ip'   => __( '最后登录IP地址' ),
			'revoke'    => __( '撤销' ),
		);
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 *
	 * @global int $user_id User ID.
	 */
	public function prepare_items() {
		global $user_id;
		$this->items = array_reverse( GC_AppKeys::get_user_appkeys( $user_id ) );
	}

	/**
	 * Handles the name column output.
	 *
	 *
	 * @param array $item The current appkey item.
	 */
	public function column_name( $item ) {
		echo esc_html( $item['name'] );
	}

	/**
	 * Handles the created column output.
	 *
	 *
	 * @param array $item The current appkey item.
	 */
	public function column_created( $item ) {
		if ( empty( $item['created'] ) ) {
			echo '&mdash;';
		} else {
			echo date_i18n( __( 'Y年n月j日' ), $item['created'] );
		}
	}

	/**
	 * Handles the last used column output.
	 *
	 *
	 * @param array $item The current appkey item.
	 */
	public function column_last_used( $item ) {
		if ( empty( $item['last_used'] ) ) {
			echo '&mdash;';
		} else {
			echo date_i18n( __( 'Y年n月j日' ), $item['last_used'] );
		}
	}

	/**
	 * Handles the last ip column output.
	 *
	 *
	 * @param array $item The current appkey item.
	 */
	public function column_last_ip( $item ) {
		if ( empty( $item['last_ip'] ) ) {
			echo '&mdash;';
		} else {
			echo $item['last_ip'];
		}
	}

	/**
	 * Handles the revoke column output.
	 *
	 *
	 * @param array $item The current appkey item.
	 */
	public function column_revoke( $item ) {
		$name = 'revoke-appkey-' . $item['uuid'];
		printf(
			'<button type="button" name="%1$s" id="%1$s" class="button delete" aria-label="%2$s">%3$s</button>',
			esc_attr( $name ),
			/* translators: %s: the appkey's given name. */
			esc_attr( sprintf( __( '撤销“%s”' ), $item['name'] ) ),
			__( '撤销' )
		);
	}

	/**
	 * Generates content for a single row of the table
	 *
	 *
	 * @param array  $item        The current item.
	 * @param string $column_name The current column name.
	 */
	protected function column_default( $item, $column_name ) {
		/**
		 * Fires for each custom column in the AppKeys list table.
		 *
		 * Custom columns are registered using the {@see 'manage_appkeys-user_columns'} filter.
		 *
		 *
		 * @param string $column_name Name of the custom column.
		 * @param array  $item        The appkey item.
		 */
		do_action( "manage_{$this->screen->id}_custom_column", $column_name, $item );
	}

	/**
	 * Generates custom table navigation to prevent conflicting nonces.
	 *
	 *
	 * @param string $which The location of the bulk actions: 'top' or 'bottom'.
	 */
	protected function display_tablenav( $which ) {
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php if ( 'bottom' === $which ) : ?>
				<div class="alignright">
					<button type="button" name="revoke-all-appkeys" id="revoke-all-appkeys" class="button delete"><?php _e( '撤消所有Appkey' ); ?></button>
				</div>
			<?php endif; ?>
			<div class="alignleft actions bulkactions">
				<?php $this->bulk_actions( $which ); ?>
			</div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
			<br class="clear" />
		</div>
		<?php
	}

	/**
	 * Generates content for a single row of the table.
	 *
	 *
	 * @param array $item The current item.
	 */
	public function single_row( $item ) {
		echo '<tr data-uuid="' . esc_attr( $item['uuid'] ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 *
	 * @return string Name of the default primary column, in this case, 'name'.
	 */
	protected function get_default_primary_column_name() {
		return 'name';
	}

	/**
	 * Prints the JavaScript template for the new row item.
	 *
	 */
	public function print_js_template_row() {
		list( $columns, $hidden, , $primary ) = $this->get_column_info();

		echo '<tr data-uuid="{{ data.uuid }}">';

		foreach ( $columns as $column_name => $display_name ) {
			$is_primary = $primary === $column_name;
			$classes    = "{$column_name} column-{$column_name}";

			if ( $is_primary ) {
				$classes .= ' has-row-actions column-primary';
			}

			if ( in_array( $column_name, $hidden, true ) ) {
				$classes .= ' hidden';
			}

			printf( '<td class="%s" data-colname="%s">', esc_attr( $classes ), esc_attr( gc_strip_all_tags( $display_name ) ) );

			switch ( $column_name ) {
				case 'name':
					echo '{{ data.name }}';
					break;
				case 'created':
					// JSON encoding automatically doubles backslashes to ensure they don't get lost when printing the inline JS.
					echo '<# print( gc.date.dateI18n( ' . gc_json_encode( __( 'Y年n月j日' ) ) . ', data.created ) ) #>';
					break;
				case 'last_used':
					echo '<# print( data.last_used !== null ? gc.date.dateI18n( ' . gc_json_encode( __( 'Y年n月j日' ) ) . ", data.last_used ) : '—' ) #>";
					break;
				case 'last_ip':
					echo "{{ data.last_ip || '—' }}";
					break;
				case 'revoke':
					printf(
						'<button type="button" class="button delete" aria-label="%1$s">%2$s</button>',
						/* translators: %s: the appkey's given name. */
						esc_attr( sprintf( __( '撤销“%s”' ), '{{ data.name }}' ) ),
						esc_html__( '撤销' )
					);
					break;
				default:
					/**
					 * Fires in the JavaScript row template for each custom column in the AppKeys list table.
					 *
					 * Custom columns are registered using the {@see 'manage_appkeys-user_columns'} filter.
					 *
				
					 *
					 * @param string $column_name Name of the custom column.
					 */
					do_action( "manage_{$this->screen->id}_custom_column_js_template", $column_name );
					break;
			}

			if ( $is_primary ) {
				echo '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( '显示详情' ) . '</span></button>';
			}

			echo '</td>';
		}

		echo '</tr>';
	}
}
