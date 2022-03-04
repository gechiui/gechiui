<?php

/**
 * The PHPMailer class has been moved to the gc-includes/PHPMailer subdirectory and now uses the PHPMailer\PHPMailer namespace.
 */
if ( function_exists( '_deprecated_file' ) ) {
	_deprecated_file(
		basename( __FILE__ ),
		'5.5.0',
		GCINC . '/PHPMailer/PHPMailer.php',
		__( 'PHPMailer类已移动到gc-includes/PHPMailer子目录，现在使用PHPMailer\PHPMailer名称空间。' )
	);
}

require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/Exception.php';

class_alias( PHPMailer\PHPMailer\PHPMailer::class, 'PHPMailer' );
class_alias( PHPMailer\PHPMailer\Exception::class, 'phpmailerException' );
