<?php
/**
 * Comment Moderation Administration Screen.
 *
 * Redirects to edit-comments.php?comment_status=moderated.
 *
 * @package GeChiUI
 * @subpackage Administration
 */
require_once dirname( __DIR__ ) . '/gc-load.php';
gc_redirect( admin_url( 'edit-comments.php?comment_status=moderated' ) );
exit;
