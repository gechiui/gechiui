<?php

/**
 * The SMTP class has been moved to the gc-includes/PHPMailer subdirectory and now uses the PHPMailer\PHPMailer namespace.
 */
_deprecated_file(
	basename( __FILE__ ),
	'5.5.0',
	GCINC . '/PHPMailer/SMTP.php',
	__( 'SMTP类已移动到gc-includes/PHPMailer子目录，现在使用PHPMailer\PHPMailer命名空间。' )
);

require_once __DIR__ . '/PHPMailer/SMTP.php';

class_alias( PHPMailer\PHPMailer\SMTP::class, 'SMTP' );
