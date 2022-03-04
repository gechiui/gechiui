<?php
/**
 * GeChiUI的基本配置
 *
 * 文件gc-config.php，是PHP创建脚本在安装过程中使用此文件。
 * 您可以将该文件复制到“gc-config.php”中
 * 并赋值。
 *
 * 此文件包含以下配置:
 *
 * * 数据库设置
 * * 密钥
 * * 数据库表前缀
 * * ABSPATH
 *
 * @link https://www.gechiui.com/support/article/editing-gc-config-php/
 *
 * @package GeChiUI
 */

// ** 数据库设置-您可以从网络主机获取此信息 ** //
/** GeChiUI数据库的名称 */
define( 'DB_NAME', 'database_name_here' );

/** 数据库用户名 */
define( 'DB_USER', 'username_here' );

/** 数据库密码 */
define( 'DB_PASSWORD', 'password_here' );

/** 数据库主机名 */
define( 'DB_HOST', 'localhost' );

/** 用于创建数据库表的数据库字符集。 */
define( 'DB_CHARSET', 'utf8' );

/** 数据库对照类型。不要改变。 */
define( 'DB_COLLATE', '' );

/**#@+
 * 身份验证唯一的 keys and salts.
 *
 * 将这些更改为不同的独特字符串！你可以使用
 * 这个 {@link https://api.gechiui.com/secret-key/1.1/salt/ www.GeChiUI.com secret-key service}.
 *
 * 您可以随时更改这些设置，以使所有现有cookie无效。
 * 这将迫使所有用户重新登录。
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'put your unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
define( 'NONCE_KEY',        'put your unique phrase here' );
define( 'AUTH_SALT',        'put your unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
define( 'NONCE_SALT',       'put your unique phrase here' );

/**#@-*/

/**
 * GeChiUI数据库表前缀。
 *
 * 给您的数据库表指定一个唯一的前缀，那么一个数据库中可以有多个安装。
 * 请只写数字、字母和下划线！
 */
$table_prefix = 'gc_';

/**
 * 面向开发人员：GeChiUI调试模式。
 *
 * 将其更改为true，以在开发过程中显示通知。
 * 强烈建议插件和主题开发人员在其开发环境中使用GC_DEBUG
 *
 * 有关可用于调试的其他常量的信息，请访问文档。
 *
 * @link https://www.gechiui.com/support/article/debugging-in-gechiui/
 */
define( 'GC_DEBUG', false );

/* 在此行和“停止编辑”行之间添加任何自定义值。 */



/* 停止编辑，到这里截止自定义值。 */

/** GeChiUI目录的绝对路径。 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** 设置GeChiUI变量和配置的文件。 */
require_once ABSPATH . 'gc-settings.php';
