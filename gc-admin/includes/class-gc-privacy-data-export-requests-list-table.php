<?php
/**
 * List Table API: GC_Privacy_Data_Export_Requests_List_Table class
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

if ( ! class_exists( 'GC_Privacy_Requests_Table' ) ) {
	require_once ABSPATH . 'gc-admin/includes/class-gc-privacy-requests-table.php';
}

/**
 * GC_Privacy_Data_Export_Requests_Table class.
 *
 *
 */
class GC_Privacy_Data_Export_Requests_List_Table extends GC_Privacy_Requests_Table {
	/**
	 * Action name for the requests this table will work with.
	 *
	 *
	 * @var string $request_type Name of action.
	 */
	protected $request_type = 'export_personal_data';

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
		/** This filter is documented in gc-admin/includes/ajax-actions.php */
		$exporters       = apply_filters( 'gc_privacy_personal_data_exporters', array() );
		$exporters_count = count( $exporters );
		$status          = $item->status;
		$request_id      = $item->ID;
		$nonce           = gc_create_nonce( 'gc-privacy-export-personal-data-' . $request_id );

		$download_data_markup = '<span class="export-personal-data" ' .
			'data-exporters-count="' . esc_attr( $exporters_count ) . '" ' .
			'data-request-id="' . esc_attr( $request_id ) . '" ' .
			'data-nonce="' . esc_attr( $nonce ) .
			'">';

		$download_data_markup .= '<span class="export-personal-data-idle"><button type="button" class="button-link export-personal-data-handle">' . __( '下载个人数据' ) . '</button></span>' .
			'<span class="export-personal-data-processing hidden">' . __( '正在下载数据…' ) . ' <span class="export-progress"></span></span>' .
			'<span class="export-personal-data-success hidden"><button type="button" class="button-link export-personal-data-handle">' . __( '重新下载个人数据' ) . '</button></span>' .
			'<span class="export-personal-data-failed hidden">' . __( '下载失败。' ) . ' <button type="button" class="button-link">' . __( '重试' ) . '</button></span>';

		$download_data_markup .= '</span>';

		$row_actions['download-data'] = $download_data_markup;

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
							admin_url( 'export-personal-data.php' )
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
	 * Displays the next steps column.
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
				$exporters       = apply_filters( 'gc_privacy_personal_data_exporters', array() );
				$exporters_count = count( $exporters );
				$request_id      = $item->ID;
				$nonce           = gc_create_nonce( 'gc-privacy-export-personal-data-' . $request_id );

				echo '<div class="export-personal-data" ' .
					'data-send-as-email="1" ' .
					'data-exporters-count="' . esc_attr( $exporters_count ) . '" ' .
					'data-request-id="' . esc_attr( $request_id ) . '" ' .
					'data-nonce="' . esc_attr( $nonce ) .
					'">';

				?>
				<span class="export-personal-data-idle"><button type="button" class="button-link export-personal-data-handle"><?php _e( '发送资料导出链接' ); ?></button></span>
				<span class="export-personal-data-processing hidden"><?php _e( '邮件发送中…' ); ?> <span class="export-progress"></span></span>
				<span class="export-personal-data-success success-message hidden"><?php _e( '邮件已发送。' ); ?></span>
				<span class="export-personal-data-failed hidden"><?php _e( '邮件未能发送。' ); ?> <button type="button" class="button-link export-personal-data-handle"><?php _e( '重试' ); ?></button></span>
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
							admin_url( 'export-personal-data.php' )
						),
						'bulk-privacy_requests'
					)
				) . '">' . esc_html__( '移除请求' ) . '</a>';
				break;
		}
	}
}
