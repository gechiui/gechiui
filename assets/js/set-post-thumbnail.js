/**
 * @output gc-admin/js/set-post-thumbnail.js
 */

/* global ajaxurl, post_id, alert */
/* exported GCSetAsThumbnail */

window.GCSetAsThumbnail = function( id, nonce ) {
	var $link = jQuery('a#gc-post-thumbnail-' + id);

	$link.text( gc.i18n.__( '正在保存…' ) );
	jQuery.post(ajaxurl, {
		action: 'set-post-thumbnail', post_id: post_id, thumbnail_id: id, _ajax_nonce: nonce, cookie: encodeURIComponent( document.cookie )
	}, function(str){
		var win = window.dialogArguments || opener || parent || top;
		$link.text( gc.i18n.__( '用作特色图片' ) );
		if ( str == '0' ) {
			alert( gc.i18n.__( '无法设置为缩略图。请尝试其他附件。' ) );
		} else {
			jQuery('a.gc-post-thumbnail').show();
			$link.text( gc.i18n.__( '完成' ) );
			$link.fadeOut( 2000 );
			win.GCSetThumbnailID(id);
			win.GCSetThumbnailHTML(str);
		}
	}
	);
};
