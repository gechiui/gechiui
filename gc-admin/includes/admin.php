<?php
/**
 * Core Administration API
 *
 * @package GeChiUI
 * @subpackage Administration
 */

if ( ! defined( 'GC_ADMIN' ) ) {
	/*
	 * This file is being included from a file other than gc-admin/admin.php, so
	 * some setup was skipped. Make sure the admin message catalog is loaded since
	 * load_default_textdomain() will not have done so in this context.
	 */
	$admin_locale = get_locale();
	load_textdomain( 'default', GC_LANG_DIR . '/admin-' . $admin_locale . '.mo', $admin_locale );
	unset( $admin_locale );
}

/** GeChiUI Administration Hooks */
require_once ABSPATH . 'gc-admin/includes/admin-filters.php';

/** GeChiUI Bookmark Administration API */
require_once ABSPATH . 'gc-admin/includes/bookmark.php';

/** GeChiUI Comment Administration API */
require_once ABSPATH . 'gc-admin/includes/comment.php';

/** GeChiUI Administration File API */
require_once ABSPATH . 'gc-admin/includes/file.php';

/** GeChiUI Image Administration API */
require_once ABSPATH . 'gc-admin/includes/image.php';

/** GeChiUI Media Administration API */
require_once ABSPATH . 'gc-admin/includes/media.php';

/** GeChiUI Import Administration API */
require_once ABSPATH . 'gc-admin/includes/import.php';

/** GeChiUI Misc Administration API */
require_once ABSPATH . 'gc-admin/includes/misc.php';

/** GeChiUI Misc Administration API */
require_once ABSPATH . 'gc-admin/includes/class-gc-privacy-policy-content.php';

/** GeChiUI Options Administration API */
require_once ABSPATH . 'gc-admin/includes/options.php';

/** GeChiUI Plugin Administration API */
require_once ABSPATH . 'gc-admin/includes/plugin.php';

/** GeChiUI Post Administration API */
require_once ABSPATH . 'gc-admin/includes/post.php';

/** GeChiUI Administration Screen API */
require_once ABSPATH . 'gc-admin/includes/class-gc-screen.php';
require_once ABSPATH . 'gc-admin/includes/screen.php';

/** GeChiUI Taxonomy Administration API */
require_once ABSPATH . 'gc-admin/includes/taxonomy.php';

/** GeChiUI Template Administration API */
require_once ABSPATH . 'gc-admin/includes/template.php';

/** GeChiUI List Table Administration API and base class */
require_once ABSPATH . 'gc-admin/includes/class-gc-list-table.php';
require_once ABSPATH . 'gc-admin/includes/class-gc-list-table-compat.php';
require_once ABSPATH . 'gc-admin/includes/list-table.php';

/** GeChiUI Theme Administration API */
require_once ABSPATH . 'gc-admin/includes/theme.php';

/** GeChiUI Privacy Functions */
require_once ABSPATH . 'gc-admin/includes/privacy-tools.php';

/** GeChiUI Privacy List Table classes. */
// Previously in gc-admin/includes/user.php. Need to be loaded for backward compatibility.
require_once ABSPATH . 'gc-admin/includes/class-gc-privacy-requests-table.php';
require_once ABSPATH . 'gc-admin/includes/class-gc-privacy-data-export-requests-list-table.php';
require_once ABSPATH . 'gc-admin/includes/class-gc-privacy-data-removal-requests-list-table.php';

/** GeChiUI User Administration API */
require_once ABSPATH . 'gc-admin/includes/user.php';

/** GeChiUI Site Icon API */
require_once ABSPATH . 'gc-admin/includes/class-gc-site-icon.php';

/** GeChiUI Update Administration API */
require_once ABSPATH . 'gc-admin/includes/update.php';

/** GeChiUI Deprecated Administration API */
require_once ABSPATH . 'gc-admin/includes/deprecated.php';

/** GeChiUI Multisite support API */
if ( is_multisite() ) {
	require_once ABSPATH . 'gc-admin/includes/ms-admin-filters.php';
	require_once ABSPATH . 'gc-admin/includes/ms.php';
	require_once ABSPATH . 'gc-admin/includes/ms-deprecated.php';
}
