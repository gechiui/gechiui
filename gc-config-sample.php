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


/* 专业版本服务配置 */

/**
 * 设置GeChiUI专业版
 * 访问 https://www.gechiui.com/pro 获取专业版
 * 在这里填写：用户名和appkey
 * GECHIUI_USERNAME：登录 www.gechiui.com 的用户名
 * GECHIUI_APPKEY：登录 www.gechiui.com 的个人资料页面创建AppKey
 */
# define( 'GECHIUI_USERNAME', 'username' );
# define( 'GECHIUI_APPKEY', 'appkey' );

/**
 * 专业版功能-CDN
 * 开启CDN加速服务。真的CSS、JS、图片和字体等
 * 
 * SaaS子路径模式：可以指定站点根域名，解决子站点重复加载问题，如 https://www.gechiui.com/
 * SaaS子域名模式：可以指定为跟域名，解决子站点重复加载问题，如 https://www.gechiui.com/
 * 也可以指定自定义域名，如 https://cdn.gechiui.com/
 * GeChiUI官方CDN-专业版地址，如：https://cdn.gechiui.com/release/6.0.3/ 
 * 
 */
# define( 'GC_CDN_URL', 'https://cdn.gechiui.com/release/6.0.3/' );


/* 在此行和“停止编辑”行之间添加任何自定义值。 */



/* 停止编辑，到这里截止自定义值。 */

/** GeChiUI目录的绝对路径。 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** 设置GeChiUI变量和配置的文件。 */
require_once ABSPATH . 'gc-settings.php';
