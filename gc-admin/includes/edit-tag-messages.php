<?php
/**
 * Edit Tags Administration: Messages
 *
 * @package GeChiUI
 * @subpackage Administration
 */

$messages = array();
// 0 = unused. Messages start at index 1.
$messages['_item'] = array(
	0 => '',
	1 => __( '项目已添加。' ),
	2 => __( '项目已删除。' ),
	3 => __( '项目已更新。' ),
	4 => __( '项目未被添加。' ),
	5 => __( '项目未更新。' ),
	6 => __( '多个项目已被删除。' ),
);

$messages['category'] = array(
	0 => '',
	1 => __( '分类已添加。' ),
	2 => __( '分类已删除。' ),
	3 => __( '分类已更新。' ),
	4 => __( '分类未被添加。' ),
	5 => __( '分类未被更新。' ),
	6 => __( '分类已被删除。' ),
);

$messages['post_tag'] = array(
	0 => '',
	1 => __( '标签已添加。' ),
	2 => __( '标签已删除。' ),
	3 => __( '标签已更新。' ),
	4 => __( '标签未被添加。' ),
	5 => __( '标签未被更新。' ),
	6 => __( '标签已被删除。' ),
);

/**
 * Filters the messages displayed when a tag is updated.
 *
 * @param array[] $messages Array of arrays of messages to be displayed, keyed by taxonomy name.
 */
$messages = apply_filters( 'term_updated_messages', $messages );

$message = false;
if ( isset( $_REQUEST['message'] ) && (int) $_REQUEST['message'] ) {
	$msg = (int) $_REQUEST['message'];
	if ( isset( $messages[ $taxonomy ][ $msg ] ) ) {
		$message = $messages[ $taxonomy ][ $msg ];
	} elseif ( ! isset( $messages[ $taxonomy ] ) && isset( $messages['_item'][ $msg ] ) ) {
		$message = $messages['_item'][ $msg ];
	}
}
