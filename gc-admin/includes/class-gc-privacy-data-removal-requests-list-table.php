<?php
/**
 * List Table API: GC_Privacy_Data_Removal_Requests_List_Table class
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

if ( ! class_exists( 'GC_Privacy_Requests_Table' ) ) {
	require_once ABSPATH . 'gc-admin/includes/class-gc-privacy-requests-table.php';
}

/**
 * GC_Privacy_Data_Removal_Requests_List_Table class.
 *
 *
 */
class GC_Privacy_Data_Removal_Requests_List_Table extends GC_Privacy_Requests_Table {
	/**
	 * Action name for the requests this table will work with.
	 *
	 *
	 * @var string $request_type Name of action.
	 */
	protected $request_type = 'remove_personal_data';

	/**
	 * Post type for the requests.
	 *
	 *
	 * @var string $post_type The post type.
	 */
	protected $post_type = 'user_request';

	/**
	 * Actions column.
	 *
	 *
	 * @param GC_User_Request $item Item being shown.
	 * @return string Email column markup.
	 */
	public function column_email( $item ) {
		$row_actions = array();

		// Allow the administrator to "force remove" the personal data even if confirmation has not yet been received.
		$status      = $item->status;
		$request_id  = $item->ID;
		$row_actions = array();
		if ( 'request-confirmed' !== $status ) {
			/** This filter is documented in gc-admin/includes/ajax-actions.php */
			$erasers       = apply_filters( 'gc_privacy_personal_data_erasers', array() );
			$erasers_count = count( $erasers );
			$nonce         = gc_create_nonce( 'gc-privacy-erase-personal-data-' . $request_id );

			$remove_data_markup = '<span class="remove-personal-data force-remove-personal-data" ' .
				'data-erasers-count="' . esc_attr( $erasers_count ) . '" ' .
				'data-request-id="' . esc_attr( $request_id ) . '" ' .
				'data-nonce="' . esc_attr( $nonce ) .
				'">';

			$remove_data_markup .= '<span class="remove-personal-data-idle"><button type="button" class="button-link remove-personal-data-handle">' . __( '强制抹除个人数据' ) . '</button></span>' .
				'<span class="remove-personal-data-processing hidden">' . __( '正在抹除数据…' ) . ' <span class="erasure-progress"></span></span>' .
				'<span class="remove-personal-data-success hidden">' . __( '抹除完成。' ) . '</span>' .
				'<span class="remove-personal-data-failed hidden">' . __( '强行抹除已失败。' ) . ' <button type="button" class="button-link remove-personal-data-handle">' . __( '重试' ) . '</button></span>';

			$remove_data_markup .= '</span>';

			$row_actions['remove-data'] = $remove_data_markup;
		}

		if ( 'request-completed' !== $status ) {
			$complete_request_markup  = '<span>';
			$complete_request_markup .= sprintf(
				'<a href="%s" class="complete-request" aria-label="%s">%s</a>',
				esc_url(
					gc_nonce_url(
						add_query_arg(
							array(
								'action'     => 'complete',
								'request_id' => array( $request_id ),
							),
							admin_url( 'erase-personal-data.php' )
						),
						'bulk-privacy_requests'
					)
				),
				esc_attr(
					sprintf(
						/* translators: %s: Request email. */
						__( '将导出请求&#8220;%s&#8221;标记为已完成。' ),
						$item->email
					)
				),
				__( '完成请求' )
			);
			$complete_request_markup .= '</span>';
		}

		if ( ! empty( $complete_request_markup ) ) {
			$row_actions['complete-request'] = $complete_request_markup;
		}

		return sprintf( '<a href="%1$s">%2$s</a> %3$s', esc_url( 'mailto:' . $item->email ), $item->email, $this->row_actions( $row_actions ) );
	}

	/**
	 * Next steps column.
	 *
	 *
	 * @param GC_User_Request $item Item being shown.
	 */
	public function column_next_steps( $item ) {
		$status = $item->status;

		switch ( $status ) {
			case 'request-pending':
				esc_html_e( '等待确认' );
				break;
			case 'request-confirmed':
				/** This filter is documented in gc-admin/includes/ajax-actions.php */
				$erasers       = apply_filters( 'gc_privacy_personal_data_erasers', array() );
				$erasers_count = count( $erasers );
				$request_id    = $item->ID;
				$nonce         = gc_create_nonce( 'gc-privacy-erase-personal-data-' . $request_id );

				echo '<div class="remove-personal-data" ' .
					'data-force-erase="1" ' .
					'data-erasers-count="' . esc_attr( $erasers_count ) . '" ' .
					'data-request-id="' . esc_attr( $request_id ) . '" ' .
					'data-nonce="' . esc_attr( $nonce ) .
					'">';

				?>
				<span class="remove-personal-data-idle"><button type="button" class="button-link remove-personal-data-handle"><?php _e( '抹除个人数据' ); ?></button></span>
				<span class="remove-personal-data-processing hidden"><?php _e( '正在抹除数据…' ); ?> <span class="erasure-progress"></span></span>
				<span class="remove-personal-data-success success-message hidden" ><?php _e( '抹除完成。' ); ?></span>
				<span class="remove-personal-data-failed hidden"><?php _e( '数据抹除已失败。' ); ?> <button type="button" class="button-link remove-personal-data-handle"><?php _e( '重试' ); ?></button></span>
				<?php

				echo '</div>';

				break;
			case 'request-failed':
				echo '<button type="submit" class="button-link" name="privacy_action_email_retry[' . $item->ID . ']" id="privacy_action_email_retry[' . $item->ID . ']">' . __( '重试' ) . '</button>';
				break;
			case 'request-completed':
				echo '<a href="' . esc_url(
					gc_nonce_url(
						add_query_arg(
							array(
								'action'     => 'delete',
								'request_id' => array( $item->ID ),
							),
							admin_url( 'erase-personal-data.php' )
						),
						'bulk-privacy_requests'
					)
				) . '">' . esc_html__( '移除请求' ) . '</a>';
				break;
		}
	}

}
