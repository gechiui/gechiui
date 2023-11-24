<?php
/**
 * User Profile Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * This is a profile page.
 *
 * @var bool
 */
define( 'IS_PROFILE_PAGE', true );
if( 'up_mobile' == $_GET['action'] ) { 
    require_once __DIR__ . '/src/up_mobile.php'; 
}else{
    /** Load User Editing Page */
    require_once __DIR__ . '/user-edit.php';
}
