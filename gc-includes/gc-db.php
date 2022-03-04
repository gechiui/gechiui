<?php
/**
 * GeChiUI database access abstraction class
 *
 * Original code from {@link http://php.justinvincent.com Justin Vincent (justin@visunet.ie)}
 *
 * @package GeChiUI
 * @subpackage Database
 *
 */

/**
 *
 */
define( 'EZSQL_VERSION', 'GC1.25' );

/**
 *
 */
define( 'OBJECT', 'OBJECT' );
// phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ConstantNotUpperCase
define( 'object', 'OBJECT' ); // Back compat.

/**
 *
 */
define( 'OBJECT_K', 'OBJECT_K' );

/**
 *
 */
define( 'ARRAY_A', 'ARRAY_A' );

/**
 *
 */
define( 'ARRAY_N', 'ARRAY_N' );

/**
 * GeChiUI database access abstraction class.
 *
 * This class is used to interact with a database without needing to use raw SQL statements.
 * By default, GeChiUI uses this class to instantiate the global $gcdb object, providing
 * access to the GeChiUI database.
 *
 * It is possible to replace this class with your own by setting the $gcdb global variable
 * in gc-content/db.php file to your class. The gcdb class will still be included, so you can
 * extend it or simply use your own.
 *
 * @link https://developer.gechiui.com/reference/classes/gcdb/
 *
 *
 */
class gcdb {

	/**
	 * Whether to show SQL/DB errors.
	 *
	 * Default is to show errors if both GC_DEBUG and GC_DEBUG_DISPLAY evaluate to true.
	 *
	 *
	 * @var bool
	 */
	public $show_errors = false;

	/**
	 * Whether to suppress errors during the DB bootstrapping. Default false.
	 *
	 *
	 * @var bool
	 */
	public $suppress_errors = false;

	/**
	 * The error encountered during the last query.
	 *
	 *
	 * @var string
	 */
	public $last_error = '';

	/**
	 * The number of queries made.
	 *
	 *
	 * @var int
	 */
	public $num_queries = 0;

	/**
	 * Count of rows returned by the last query.
	 *
	 *
	 * @var int
	 */
	public $num_rows = 0;

	/**
	 * Count of rows affected by the last query.
	 *
	 *
	 * @var int
	 */
	public $rows_affected = 0;

	/**
	 * The ID generated for an AUTO_INCREMENT column by the last query (usually INSERT).
	 *
	 *
	 * @var int
	 */
	public $insert_id = 0;

	/**
	 * The last query made.
	 *
	 *
	 * @var string
	 */
	public $last_query;

	/**
	 * Results of the last query.
	 *
	 *
	 * @var stdClass[]|null
	 */
	public $last_result;

	/**
	 * Database query result.
	 *
	 * Possible values:
	 *
	 * - For successful SELECT, SHOW, DESCRIBE, or EXPLAIN queries:
	 *   - `mysqli_result` instance when the `mysqli` driver is in use
	 *   - `resource` when the older `mysql` driver is in use
	 * - `true` for other query types that were successful
	 * - `null` if a query is yet to be made or if the result has since been flushed
	 * - `false` if the query returned an error
	 *
	 *
	 * @var mysqli_result|resource|bool|null
	 */
	protected $result;

	/**
	 * Cached column info, for sanity checking data before inserting.
	 *
	 *
	 * @var array
	 */
	protected $col_meta = array();

	/**
	 * Calculated character sets keyed by table name.
	 *
	 *
	 * @var string[]
	 */
	protected $table_charset = array();

	/**
	 * Whether text fields in the current query need to be sanity checked.
	 *
	 *
	 * @var bool
	 */
	protected $check_current_query = true;

	/**
	 * Flag to ensure we don't run into recursion problems when checking the collation.
	 *
	 *
	 * @see gcdb::check_safe_collation()
	 * @var bool
	 */
	private $checking_collation = false;

	/**
	 * Saved info on the table column.
	 *
	 *
	 * @var array
	 */
	protected $col_info;

	/**
	 * Log of queries that were executed, for debugging purposes.
	 *
	 *
	 * @var array[] {
	 *     Array of arrays containing information about queries that were executed.
	 *
	 *     @type array ...$0 {
	 *         Data for each query.
	 *
	 *         @type string $0 The query's SQL.
	 *         @type float  $1 Total time spent on the query, in seconds.
	 *         @type string $2 Comma-separated list of the calling functions.
	 *         @type float  $3 Unix timestamp of the time at the start of the query.
	 *         @type array  $4 Custom query data.
	 *     }
	 * }
	 */
	public $queries;

	/**
	 * The number of times to retry reconnecting before dying. Default 5.
	 *
	 *
	 * @see gcdb::check_connection()
	 * @var int
	 */
	protected $reconnect_retries = 5;

	/**
	 * GeChiUI table prefix.
	 *
	 * You can set this to have multiple GeChiUI installations in a single database.
	 * The second reason is for possible security precautions.
	 *
	 *
	 * @var string
	 */
	public $prefix = '';

	/**
	 * GeChiUI base table prefix.
	 *
	 *
	 * @var string
	 */
	public $base_prefix;

	/**
	 * Whether the database queries are ready to start executing.
	 *
	 *
	 * @var bool
	 */
	public $ready = false;

	/**
	 * Blog ID.
	 *
	 *
	 * @var int
	 */
	public $blogid = 0;

	/**
	 * Site ID.
	 *
	 *
	 * @var int
	 */
	public $siteid = 0;

	/**
	 * List of GeChiUI per-site tables.
	 *
	 *
	 * @see gcdb::tables()
	 * @var string[]
	 */
	public $tables = array(
		'posts',
		'comments',
		'links',
		'options',
		'postmeta',
		'terms',
		'term_taxonomy',
		'term_relationships',
		'termmeta',
		'commentmeta',
	);

	/**
	 * List of deprecated GeChiUI tables.
	 *
	 * 'categories', 'post2cat', and 'link2cat' were deprecated in 2.3.0, db version 5539.
	 *
	 *
	 * @see gcdb::tables()
	 * @var string[]
	 */
	public $old_tables = array( 'categories', 'post2cat', 'link2cat' );

	/**
	 * List of GeChiUI global tables.
	 *
	 *
	 * @see gcdb::tables()
	 * @var string[]
	 */
	public $global_tables = array( 'users', 'usermeta' );

	/**
	 * List of Multisite global tables.
	 *
	 *
	 * @see gcdb::tables()
	 * @var string[]
	 */
	public $ms_global_tables = array(
		'blogs',
		'blogmeta',
		'signups',
		'site',
		'sitemeta',
		'sitecategories',
		'registration_log',
	);

	/**
	 * GeChiUI Comments table.
	 *
	 *
	 * @var string
	 */
	public $comments;

	/**
	 * GeChiUI Comment Metadata table.
	 *
	 *
	 * @var string
	 */
	public $commentmeta;

	/**
	 * GeChiUI Links table.
	 *
	 *
	 * @var string
	 */
	public $links;

	/**
	 * GeChiUI Options table.
	 *
	 *
	 * @var string
	 */
	public $options;

	/**
	 * GeChiUI Post Metadata table.
	 *
	 *
	 * @var string
	 */
	public $postmeta;

	/**
	 * GeChiUI Posts table.
	 *
	 *
	 * @var string
	 */
	public $posts;

	/**
	 * GeChiUI Terms table.
	 *
	 *
	 * @var string
	 */
	public $terms;

	/**
	 * GeChiUI Term Relationships table.
	 *
	 *
	 * @var string
	 */
	public $term_relationships;

	/**
	 * GeChiUI Term Taxonomy table.
	 *
	 *
	 * @var string
	 */
	public $term_taxonomy;

	/**
	 * GeChiUI Term Meta table.
	 *
	 *
	 * @var string
	 */
	public $termmeta;

	//
	// Global and Multisite tables
	//

	/**
	 * GeChiUI User Metadata table.
	 *
	 *
	 * @var string
	 */
	public $usermeta;

	/**
	 * GeChiUI Users table.
	 *
	 *
	 * @var string
	 */
	public $users;

	/**
	 * Multisite Blogs table.
	 *
	 *
	 * @var string
	 */
	public $blogs;

	/**
	 * Multisite Blog Metadata table.
	 *
	 *
	 * @var string
	 */
	public $blogmeta;

	/**
	 * Multisite Registration Log table.
	 *
	 *
	 * @var string
	 */
	public $registration_log;

	/**
	 * Multisite Signups table.
	 *
	 *
	 * @var string
	 */
	public $signups;

	/**
	 * Multisite Sites table.
	 *
	 *
	 * @var string
	 */
	public $site;

	/**
	 * Multisite Sitewide Terms table.
	 *
	 *
	 * @var string
	 */
	public $sitecategories;

	/**
	 * Multisite Site Metadata table.
	 *
	 *
	 * @var string
	 */
	public $sitemeta;

	/**
	 * Format specifiers for DB columns.
	 *
	 * Columns not listed here default to %s. Initialized during GC load.
	 * Keys are column names, values are format types: 'ID' => '%d'.
	 *
	 *
	 * @see gcdb::prepare()
	 * @see gcdb::insert()
	 * @see gcdb::update()
	 * @see gcdb::delete()
	 * @see gc_set_gcdb_vars()
	 * @var array
	 */
	public $field_types = array();

	/**
	 * Database table columns charset.
	 *
	 *
	 * @var string
	 */
	public $charset;

	/**
	 * Database table columns collate.
	 *
	 *
	 * @var string
	 */
	public $collate;

	/**
	 * Database Username.
	 *
	 *
	 * @var string
	 */
	protected $dbuser;

	/**
	 * Database Password.
	 *
	 *
	 * @var string
	 */
	protected $dbpassword;

	/**
	 * Database Name.
	 *
	 *
	 * @var string
	 */
	protected $dbname;

	/**
	 * Database Host.
	 *
	 *
	 * @var string
	 */
	protected $dbhost;

	/**
	 * Database handle.
	 *
	 * Possible values:
	 *
	 * - `mysqli` instance when the `mysqli` driver is in use
	 * - `resource` when the older `mysql` driver is in use
	 * - `null` if the connection is yet to be made or has been closed
	 * - `false` if the connection has failed
	 *
	 *
	 * @var mysqli|resource|false|null
	 */
	protected $dbh;

	/**
	 * A textual description of the last query/get_row/get_var call.
	 *
	 *
	 * @var string
	 */
	public $func_call;

	/**
	 * Whether MySQL is used as the database engine.
	 *
	 * Set in gcdb::db_connect() to true, by default. This is used when checking
	 * against the required MySQL version for GeChiUI. Normally, a replacement
	 * database drop-in (db.php) will skip these checks, but setting this to true
	 * will force the checks to occur.
	 *
	 *
	 * @var bool
	 */
	public $is_mysql = null;

	/**
	 * A list of incompatible SQL modes.
	 *
	 *
	 * @var string[]
	 */
	protected $incompatible_modes = array(
		'NO_ZERO_DATE',
		'ONLY_FULL_GROUP_BY',
		'STRICT_TRANS_TABLES',
		'STRICT_ALL_TABLES',
		'TRADITIONAL',
		'ANSI',
	);

	/**
	 * Whether to use mysqli over mysql. Default false.
	 *
	 *
	 * @var bool
	 */
	private $use_mysqli = false;

	/**
	 * Whether we've managed to successfully connect at some point.
	 *
	 *
	 * @var bool
	 */
	private $has_connected = false;

	/**
	 * Time when the last query was performed.
	 *
	 * Only set when `SAVEQUERIES` is defined and truthy.
	 *
	 *
	 * @var float
	 */
	public $time_start = null;

	/**
	 * The last SQL error that was encountered.
	 *
	 *
	 * @var GC_Error|string
	 */
	public $error = null;

	/**
	 * Connects to the database server and selects a database.
	 *
	 * Does the actual setting up
	 * of the class properties and connection to the database.
	 *
	 *
	 * @link https://core.trac.gechiui.com/ticket/3354
	 *
	 * @param string $dbuser     Database user.
	 * @param string $dbpassword Database password.
	 * @param string $dbname     Database name.
	 * @param string $dbhost     Database host.
	 */
	public function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {
		if ( GC_DEBUG && GC_DEBUG_DISPLAY ) {
			$this->show_errors();
		}

		// Use the `mysqli` extension if it exists unless `GC_USE_EXT_MYSQL` is defined as true.
		if ( function_exists( 'mysqli_connect' ) ) {
			$this->use_mysqli = true;

			if ( defined( 'GC_USE_EXT_MYSQL' ) ) {
				$this->use_mysqli = ! GC_USE_EXT_MYSQL;
			}
		}

		$this->dbuser     = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname     = $dbname;
		$this->dbhost     = $dbhost;

		// gc-config.php creation will manually connect when ready.
		if ( defined( 'GC_SETUP_CONFIG' ) ) {
			return;
		}

		$this->db_connect();
	}

	/**
	 * Makes private properties readable for backward compatibility.
	 *
	 *
	 * @param string $name The private member to get, and optionally process.
	 * @return mixed The private member.
	 */
	public function __get( $name ) {
		if ( 'col_info' === $name ) {
			$this->load_col_info();
		}

		return $this->$name;
	}

	/**
	 * Makes private properties settable for backward compatibility.
	 *
	 *
	 * @param string $name  The private member to set.
	 * @param mixed  $value The value to set.
	 */
	public function __set( $name, $value ) {
		$protected_members = array(
			'col_meta',
			'table_charset',
			'check_current_query',
		);
		if ( in_array( $name, $protected_members, true ) ) {
			return;
		}
		$this->$name = $value;
	}

	/**
	 * Makes private properties check-able for backward compatibility.
	 *
	 *
	 * @param string $name The private member to check.
	 * @return bool If the member is set or not.
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Makes private properties un-settable for backward compatibility.
	 *
	 *
	 * @param string $name  The private member to unset
	 */
	public function __unset( $name ) {
		unset( $this->$name );
	}

	/**
	 * Sets $this->charset and $this->collate.
	 *
	 */
	public function init_charset() {
		$charset = '';
		$collate = '';

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$charset = 'utf8';
			if ( defined( 'DB_COLLATE' ) && DB_COLLATE ) {
				$collate = DB_COLLATE;
			} else {
				$collate = 'utf8_general_ci';
			}
		} elseif ( defined( 'DB_COLLATE' ) ) {
			$collate = DB_COLLATE;
		}

		if ( defined( 'DB_CHARSET' ) ) {
			$charset = DB_CHARSET;
		}

		$charset_collate = $this->determine_charset( $charset, $collate );

		$this->charset = $charset_collate['charset'];
		$this->collate = $charset_collate['collate'];
	}

	/**
	 * Determines the best charset and collation to use given a charset and collation.
	 *
	 * For example, when able, utf8mb4 should be used instead of utf8.
	 *
	 *
	 * @param string $charset The character set to check.
	 * @param string $collate The collation to check.
	 * @return array {
	 *     The most appropriate character set and collation to use.
	 *
	 *     @type string $charset Character set.
	 *     @type string $collate Collation.
	 * }
	 */
	public function determine_charset( $charset, $collate ) {
		if ( ( $this->use_mysqli && ! ( $this->dbh instanceof mysqli ) ) || empty( $this->dbh ) ) {
			return compact( 'charset', 'collate' );
		}

		if ( 'utf8' === $charset && $this->has_cap( 'utf8mb4' ) ) {
			$charset = 'utf8mb4';
		}

		if ( 'utf8mb4' === $charset && ! $this->has_cap( 'utf8mb4' ) ) {
			$charset = 'utf8';
			$collate = str_replace( 'utf8mb4_', 'utf8_', $collate );
		}

		if ( 'utf8mb4' === $charset ) {
			// _general_ is outdated, so we can upgrade it to _unicode_, instead.
			if ( ! $collate || 'utf8_general_ci' === $collate ) {
				$collate = 'utf8mb4_unicode_ci';
			} else {
				$collate = str_replace( 'utf8_', 'utf8mb4_', $collate );
			}
		}

		// _unicode_520_ is a better collation, we should use that when it's available.
		if ( $this->has_cap( 'utf8mb4_520' ) && 'utf8mb4_unicode_ci' === $collate ) {
			$collate = 'utf8mb4_unicode_520_ci';
		}

		return compact( 'charset', 'collate' );
	}

	/**
	 * Sets the connection's character set.
	 *
	 *
	 * @param mysqli|resource $dbh     The connection returned by `mysqli_connect()` or `mysql_connect()`.
	 * @param string          $charset Optional. The character set. Default null.
	 * @param string          $collate Optional. The collation. Default null.
	 */
	public function set_charset( $dbh, $charset = null, $collate = null ) {
		if ( ! isset( $charset ) ) {
			$charset = $this->charset;
		}
		if ( ! isset( $collate ) ) {
			$collate = $this->collate;
		}
		if ( $this->has_cap( 'collation' ) && ! empty( $charset ) ) {
			$set_charset_succeeded = true;

			if ( $this->use_mysqli ) {
				if ( function_exists( 'mysqli_set_charset' ) && $this->has_cap( 'set_charset' ) ) {
					$set_charset_succeeded = mysqli_set_charset( $dbh, $charset );
				}

				if ( $set_charset_succeeded ) {
					$query = $this->prepare( 'SET NAMES %s', $charset );
					if ( ! empty( $collate ) ) {
						$query .= $this->prepare( ' COLLATE %s', $collate );
					}
					mysqli_query( $dbh, $query );
				}
			} else {
				if ( function_exists( 'mysql_set_charset' ) && $this->has_cap( 'set_charset' ) ) {
					$set_charset_succeeded = mysql_set_charset( $charset, $dbh );
				}
				if ( $set_charset_succeeded ) {
					$query = $this->prepare( 'SET NAMES %s', $charset );
					if ( ! empty( $collate ) ) {
						$query .= $this->prepare( ' COLLATE %s', $collate );
					}
					mysql_query( $query, $dbh );
				}
			}
		}
	}

	/**
	 * Changes the current SQL mode, and ensures its GeChiUI compatibility.
	 *
	 * If no modes are passed, it will ensure the current MySQL server modes are compatible.
	 *
	 *
	 * @param array $modes Optional. A list of SQL modes to set. Default empty array.
	 */
	public function set_sql_mode( $modes = array() ) {
		if ( empty( $modes ) ) {
			if ( $this->use_mysqli ) {
				$res = mysqli_query( $this->dbh, 'SELECT @@SESSION.sql_mode' );
			} else {
				$res = mysql_query( 'SELECT @@SESSION.sql_mode', $this->dbh );
			}

			if ( empty( $res ) ) {
				return;
			}

			if ( $this->use_mysqli ) {
				$modes_array = mysqli_fetch_array( $res );
				if ( empty( $modes_array[0] ) ) {
					return;
				}
				$modes_str = $modes_array[0];
			} else {
				$modes_str = mysql_result( $res, 0 );
			}

			if ( empty( $modes_str ) ) {
				return;
			}

			$modes = explode( ',', $modes_str );
		}

		$modes = array_change_key_case( $modes, CASE_UPPER );

		/**
		 * Filters the list of incompatible SQL modes to exclude.
		 *
		 *
		 * @param array $incompatible_modes An array of incompatible modes.
		 */
		$incompatible_modes = (array) apply_filters( 'incompatible_sql_modes', $this->incompatible_modes );

		foreach ( $modes as $i => $mode ) {
			if ( in_array( $mode, $incompatible_modes, true ) ) {
				unset( $modes[ $i ] );
			}
		}

		$modes_str = implode( ',', $modes );

		if ( $this->use_mysqli ) {
			mysqli_query( $this->dbh, "SET SESSION sql_mode='$modes_str'" );
		} else {
			mysql_query( "SET SESSION sql_mode='$modes_str'", $this->dbh );
		}
	}

	/**
	 * Sets the table prefix for the GeChiUI tables.
	 *
	 *
	 * @param string $prefix          Alphanumeric name for the new prefix.
	 * @param bool   $set_table_names Optional. Whether the table names, e.g. gcdb::$posts,
	 *                                should be updated or not. Default true.
	 * @return string|GC_Error Old prefix or GC_Error on error.
	 */
	public function set_prefix( $prefix, $set_table_names = true ) {

		if ( preg_match( '|[^a-z0-9_]|i', $prefix ) ) {
			return new GC_Error( 'invalid_db_prefix', 'Invalid database prefix' );
		}

		$old_prefix = is_multisite() ? '' : $prefix;

		if ( isset( $this->base_prefix ) ) {
			$old_prefix = $this->base_prefix;
		}

		$this->base_prefix = $prefix;

		if ( $set_table_names ) {
			foreach ( $this->tables( 'global' ) as $table => $prefixed_table ) {
				$this->$table = $prefixed_table;
			}

			if ( is_multisite() && empty( $this->blogid ) ) {
				return $old_prefix;
			}

			$this->prefix = $this->get_blog_prefix();

			foreach ( $this->tables( 'blog' ) as $table => $prefixed_table ) {
				$this->$table = $prefixed_table;
			}

			foreach ( $this->tables( 'old' ) as $table => $prefixed_table ) {
				$this->$table = $prefixed_table;
			}
		}
		return $old_prefix;
	}

	/**
	 * Sets blog ID.
	 *
	 *
	 * @param int $blog_id
	 * @param int $network_id Optional.
	 * @return int Previous blog ID.
	 */
	public function set_blog_id( $blog_id, $network_id = 0 ) {
		if ( ! empty( $network_id ) ) {
			$this->siteid = $network_id;
		}

		$old_blog_id  = $this->blogid;
		$this->blogid = $blog_id;

		$this->prefix = $this->get_blog_prefix();

		foreach ( $this->tables( 'blog' ) as $table => $prefixed_table ) {
			$this->$table = $prefixed_table;
		}

		foreach ( $this->tables( 'old' ) as $table => $prefixed_table ) {
			$this->$table = $prefixed_table;
		}

		return $old_blog_id;
	}

	/**
	 * Gets blog prefix.
	 *
	 *
	 * @param int $blog_id Optional.
	 * @return string Blog prefix.
	 */
	public function get_blog_prefix( $blog_id = null ) {
		if ( is_multisite() ) {
			if ( null === $blog_id ) {
				$blog_id = $this->blogid;
			}

			$blog_id = (int) $blog_id;

			if ( defined( 'MULTISITE' ) && ( 0 === $blog_id || 1 === $blog_id ) ) {
				return $this->base_prefix;
			} else {
				return $this->base_prefix . $blog_id . '_';
			}
		} else {
			return $this->base_prefix;
		}
	}

	/**
	 * Returns an array of GeChiUI tables.
	 *
	 * Also allows for the `CUSTOM_USER_TABLE` and `CUSTOM_USER_META_TABLE` to override the GeChiUI users
	 * and usermeta tables that would otherwise be determined by the prefix.
	 *
	 * The `$scope` argument can take one of the following:
	 *
	 * - 'all' - returns 'all' and 'global' tables. No old tables are returned.
	 * - 'blog' - returns the blog-level tables for the queried blog.
	 * - 'global' - returns the global tables for the installation, returning multisite tables only on multisite.
	 * - 'ms_global' - returns the multisite global tables, regardless if current installation is multisite.
	 * - 'old' - returns tables which are deprecated.
	 *
	 *
	 * @uses gcdb::$tables
	 * @uses gcdb::$old_tables
	 * @uses gcdb::$global_tables
	 * @uses gcdb::$ms_global_tables
	 *
	 * @param string $scope   Optional. Possible values include 'all', 'global', 'ms_global', 'blog',
	 *                        or 'old' tables. Default 'all'.
	 * @param bool   $prefix  Optional. Whether to include table prefixes. If blog prefix is requested,
	 *                        then the custom users and usermeta tables will be mapped. Default true.
	 * @param int    $blog_id Optional. The blog_id to prefix. Used only when prefix is requested.
	 *                        Defaults to `gcdb::$blogid`.
	 * @return string[] Table names. When a prefix is requested, the key is the unprefixed table name.
	 */
	public function tables( $scope = 'all', $prefix = true, $blog_id = 0 ) {
		switch ( $scope ) {
			case 'all':
				$tables = array_merge( $this->global_tables, $this->tables );
				if ( is_multisite() ) {
					$tables = array_merge( $tables, $this->ms_global_tables );
				}
				break;
			case 'blog':
				$tables = $this->tables;
				break;
			case 'global':
				$tables = $this->global_tables;
				if ( is_multisite() ) {
					$tables = array_merge( $tables, $this->ms_global_tables );
				}
				break;
			case 'ms_global':
				$tables = $this->ms_global_tables;
				break;
			case 'old':
				$tables = $this->old_tables;
				break;
			default:
				return array();
		}

		if ( $prefix ) {
			if ( ! $blog_id ) {
				$blog_id = $this->blogid;
			}
			$blog_prefix   = $this->get_blog_prefix( $blog_id );
			$base_prefix   = $this->base_prefix;
			$global_tables = array_merge( $this->global_tables, $this->ms_global_tables );
			foreach ( $tables as $k => $table ) {
				if ( in_array( $table, $global_tables, true ) ) {
					$tables[ $table ] = $base_prefix . $table;
				} else {
					$tables[ $table ] = $blog_prefix . $table;
				}
				unset( $tables[ $k ] );
			}

			if ( isset( $tables['users'] ) && defined( 'CUSTOM_USER_TABLE' ) ) {
				$tables['users'] = CUSTOM_USER_TABLE;
			}

			if ( isset( $tables['usermeta'] ) && defined( 'CUSTOM_USER_META_TABLE' ) ) {
				$tables['usermeta'] = CUSTOM_USER_META_TABLE;
			}
		}

		return $tables;
	}

	/**
	 * Selects a database using the current or provided database connection.
	 *
	 * The database name will be changed based on the current database connection.
	 * On failure, the execution will bail and display a DB error.
	 *
	 *
	 * @param string          $db  Database name.
	 * @param mysqli|resource $dbh Optional database connection.
	 */
	public function select( $db, $dbh = null ) {
		if ( is_null( $dbh ) ) {
			$dbh = $this->dbh;
		}

		if ( $this->use_mysqli ) {
			$success = mysqli_select_db( $dbh, $db );
		} else {
			$success = mysql_select_db( $db, $dbh );
		}
		if ( ! $success ) {
			$this->ready = false;
			if ( ! did_action( 'template_redirect' ) ) {
				gc_load_translations_early();

				$message = '<h1>' . __( '不能选择数据库' ) . "</h1>\n";

				$message .= '<p>' . sprintf(
					/* translators: %s: Database name. */
					__( '我们能够连接到数据库服务器（这意味着您的用户名和密码正确），但未能选择%s数据库。' ),
					'<code>' . htmlspecialchars( $db, ENT_QUOTES ) . '</code>'
				) . "</p>\n";

				$message .= "<ul>\n";
				$message .= '<li>' . __( '您确定它存在吗？' ) . "</li>\n";

				$message .= '<li>' . sprintf(
					/* translators: 1: Database user, 2: Database name. */
					__( '用户%1$s有权限使用数据库%2$s吗？' ),
					'<code>' . htmlspecialchars( $this->dbuser, ENT_QUOTES ) . '</code>',
					'<code>' . htmlspecialchars( $db, ENT_QUOTES ) . '</code>'
				) . "</li>\n";

				$message .= '<li>' . sprintf(
					/* translators: %s: Database name. */
					__( '在部分系统中您的数据库名称前缀是您的用户名，如是<code>username_%1$s</code>。可能是这种问题吗？' ),
					htmlspecialchars( $db, ENT_QUOTES )
				) . "</li>\n";

				$message .= "</ul>\n";

				$message .= '<p>' . sprintf(
					/* translators: %s: Support forums URL. */
					__( '如果您不知道如何设置数据库，您应该<strong>联系您的主机提供商</strong>。或者您也可以在<a href="%s">GeChiUI支持论坛</a>获得帮助。' ),
					__( 'https://www.gechiui.com/support/forums/' )
				) . "</p>\n";

				$this->bail( $message, 'db_select_fail' );
			}
		}
	}

	/**
	 * Do not use, deprecated.
	 *
	 * Use esc_sql() or gcdb::prepare() instead.
	 *
	 * @deprecated 3.6.0 Use gcdb::prepare()
	 * @see gcdb::prepare()
	 * @see esc_sql()
	 *
	 * @param string $string
	 * @return string
	 */
	public function _weak_escape( $string ) {
		if ( func_num_args() === 1 && function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __METHOD__, '3.6.0', 'gcdb::prepare() or esc_sql()' );
		}
		return addslashes( $string );
	}

	/**
	 * Real escape, using mysqli_real_escape_string() or mysql_real_escape_string().
	 *
	 *
	 * @see mysqli_real_escape_string()
	 * @see mysql_real_escape_string()
	 *
	 * @param string $string String to escape.
	 * @return string Escaped string.
	 */
	public function _real_escape( $string ) {
		if ( ! is_scalar( $string ) ) {
			return '';
		}

		if ( $this->dbh ) {
			if ( $this->use_mysqli ) {
				$escaped = mysqli_real_escape_string( $this->dbh, $string );
			} else {
				$escaped = mysql_real_escape_string( $string, $this->dbh );
			}
		} else {
			$class = get_class( $this );

			gc_load_translations_early();
			/* translators: %s: Database access abstraction class, usually gcdb or a class extending gcdb. */
			_doing_it_wrong( $class, sprintf( __( '%s必须为转义设置数据库连接。' ), $class ), '3.6.0' );

			$escaped = addslashes( $string );
		}

		return $this->add_placeholder_escape( $escaped );
	}

	/**
	 * Escapes data. Works on arrays.
	 *
	 *
	 * @uses gcdb::_real_escape()
	 *
	 * @param string|array $data Data to escape.
	 * @return string|array Escaped data, in the same type as supplied.
	 */
	public function _escape( $data ) {
		if ( is_array( $data ) ) {
			foreach ( $data as $k => $v ) {
				if ( is_array( $v ) ) {
					$data[ $k ] = $this->_escape( $v );
				} else {
					$data[ $k ] = $this->_real_escape( $v );
				}
			}
		} else {
			$data = $this->_real_escape( $data );
		}

		return $data;
	}

	/**
	 * Do not use, deprecated.
	 *
	 * Use esc_sql() or gcdb::prepare() instead.
	 *
	 * @deprecated 3.6.0 Use gcdb::prepare()
	 * @see gcdb::prepare()
	 * @see esc_sql()
	 *
	 * @param string|array $data Data to escape.
	 * @return string|array Escaped data, in the same type as supplied.
	 */
	public function escape( $data ) {
		if ( func_num_args() === 1 && function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __METHOD__, '3.6.0', 'gcdb::prepare() or esc_sql()' );
		}
		if ( is_array( $data ) ) {
			foreach ( $data as $k => $v ) {
				if ( is_array( $v ) ) {
					$data[ $k ] = $this->escape( $v, 'recursive' );
				} else {
					$data[ $k ] = $this->_weak_escape( $v, 'internal' );
				}
			}
		} else {
			$data = $this->_weak_escape( $data, 'internal' );
		}

		return $data;
	}

	/**
	 * Escapes content by reference for insertion into the database, for security.
	 *
	 * @uses gcdb::_real_escape()
	 *
	 *
	 * @param string $string String to escape.
	 */
	public function escape_by_ref( &$string ) {
		if ( ! is_float( $string ) ) {
			$string = $this->_real_escape( $string );
		}
	}

	/**
	 * Prepares a SQL query for safe execution.
	 *
	 * Uses sprintf()-like syntax. The following placeholders can be used in the query string:
	 *
	 * - %d (integer)
	 * - %f (float)
	 * - %s (string)
	 *
	 * All placeholders MUST be left unquoted in the query string. A corresponding argument
	 * MUST be passed for each placeholder.
	 *
	 * Note: There is one exception to the above: for compatibility with old behavior,
	 * numbered or formatted string placeholders (eg, `%1$s`, `%5s`) will not have quotes
	 * added by this function, so should be passed with appropriate quotes around them.
	 *
	 * Literal percentage signs (`%`) in the query string must be written as `%%`. Percentage wildcards
	 * (for example, to use in LIKE syntax) must be passed via a substitution argument containing
	 * the complete LIKE string, these cannot be inserted directly in the query string.
	 * Also see gcdb::esc_like().
	 *
	 * Arguments may be passed as individual arguments to the method, or as a single array
	 * containing all arguments. A combination of the two is not supported.
	 *
	 * Examples:
	 *
	 *     $gcdb->prepare( "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d OR `other_field` LIKE %s", array( 'foo', 1337, '%bar' ) );
	 *     $gcdb->prepare( "SELECT DATE_FORMAT(`field`, '%%c') FROM `table` WHERE `column` = %s", 'foo' );
	 *
	 *              by updating the function signature. The second parameter was changed
	 *              from `$args` to `...$args`.
	 *
	 * @link https://www.php.net/sprintf Description of syntax.
	 *
	 * @param string      $query   Query statement with sprintf()-like placeholders.
	 * @param array|mixed $args    The array of variables to substitute into the query's placeholders
	 *                             if being called with an array of arguments, or the first variable
	 *                             to substitute into the query's placeholders if being called with
	 *                             individual arguments.
	 * @param mixed       ...$args Further variables to substitute into the query's placeholders
	 *                             if being called with individual arguments.
	 * @return string|void Sanitized query string, if there is a query to prepare.
	 */
	public function prepare( $query, ...$args ) {
		if ( is_null( $query ) ) {
			return;
		}

		// This is not meant to be foolproof -- but it will catch obviously incorrect usage.
		if ( strpos( $query, '%' ) === false ) {
			gc_load_translations_early();
			_doing_it_wrong(
				'gcdb::prepare',
				sprintf(
					/* translators: %s: gcdb::prepare() */
					__( '查询参数%s必须有占位符。' ),
					'gcdb::prepare()'
				),
				'3.9.0'
			);
		}

		// If args were passed as an array (as in vsprintf), move them up.
		$passed_as_array = false;
		if ( isset( $args[0] ) && is_array( $args[0] ) && 1 === count( $args ) ) {
			$passed_as_array = true;
			$args            = $args[0];
		}

		foreach ( $args as $arg ) {
			if ( ! is_scalar( $arg ) && ! is_null( $arg ) ) {
				gc_load_translations_early();
				_doing_it_wrong(
					'gcdb::prepare',
					sprintf(
						/* translators: %s: Value type. */
						__( '无效的值类型（%s）。' ),
						gettype( $arg )
					),
					'4.8.2'
				);
			}
		}

		/*
		 * Specify the formatting allowed in a placeholder. The following are allowed:
		 *
		 * - Sign specifier. eg, $+d
		 * - Numbered placeholders. eg, %1$s
		 * - Padding specifier, including custom padding characters. eg, %05s, %'#5s
		 * - Alignment specifier. eg, %05-s
		 * - Precision specifier. eg, %.2f
		 */
		$allowed_format = '(?:[1-9][0-9]*[$])?[-+0-9]*(?: |0|\'.)?[-+0-9]*(?:\.[0-9]+)?';

		/*
		 * If a %s placeholder already has quotes around it, removing the existing quotes and re-inserting them
		 * ensures the quotes are consistent.
		 *
		 * For backward compatibility, this is only applied to %s, and not to placeholders like %1$s, which are frequently
		 * used in the middle of longer strings, or as table name placeholders.
		 */
		$query = str_replace( "'%s'", '%s', $query ); // Strip any existing single quotes.
		$query = str_replace( '"%s"', '%s', $query ); // Strip any existing double quotes.
		$query = preg_replace( '/(?<!%)%s/', "'%s'", $query ); // Quote the strings, avoiding escaped strings like %%s.

		$query = preg_replace( "/(?<!%)(%($allowed_format)?f)/", '%\\2F', $query ); // Force floats to be locale-unaware.

		$query = preg_replace( "/%(?:%|$|(?!($allowed_format)?[sdF]))/", '%%\\1', $query ); // Escape any unescaped percents.

		// Count the number of valid placeholders in the query.
		$placeholders = preg_match_all( "/(^|[^%]|(%%)+)%($allowed_format)?[sdF]/", $query, $matches );

		$args_count = count( $args );

		if ( $args_count !== $placeholders ) {
			if ( 1 === $placeholders && $passed_as_array ) {
				// If the passed query only expected one argument, but the wrong number of arguments were sent as an array, bail.
				gc_load_translations_early();
				_doing_it_wrong(
					'gcdb::prepare',
					__( '此查询只期望一个占位符，但被发送了包含多个占位符的数组。' ),
					'4.9.0'
				);

				return;
			} else {
				/*
				 * If we don't have the right number of placeholders, but they were passed as individual arguments,
				 * or we were expecting multiple arguments in an array, throw a warning.
				 */
				gc_load_translations_early();
				_doing_it_wrong(
					'gcdb::prepare',
					sprintf(
						/* translators: 1: Number of placeholders, 2: Number of arguments passed. */
						__( '此查询未包含对应传入参数数量（%2$d）的正确数目的占位符（%1$d）。' ),
						$placeholders,
						$args_count
					),
					'4.8.3'
				);

				/*
				 * If we don't have enough arguments to match the placeholders,
				 * return an empty string to avoid a fatal error on PHP 8.
				 */
				if ( $args_count < $placeholders ) {
					$max_numbered_placeholder = ! empty( $matches[3] ) ? max( array_map( 'intval', $matches[3] ) ) : 0;

					if ( ! $max_numbered_placeholder || $args_count < $max_numbered_placeholder ) {
						return '';
					}
				}
			}
		}

		array_walk( $args, array( $this, 'escape_by_ref' ) );
		$query = vsprintf( $query, $args );

		return $this->add_placeholder_escape( $query );
	}

	/**
	 * First half of escaping for `LIKE` special characters `%` and `_` before preparing for SQL.
	 *
	 * Use this only before gcdb::prepare() or esc_sql(). Reversing the order is very bad for security.
	 *
	 * Example Prepared Statement:
	 *
	 *     $wild = '%';
	 *     $find = 'only 43% of planets';
	 *     $like = $wild . $gcdb->esc_like( $find ) . $wild;
	 *     $sql  = $gcdb->prepare( "SELECT * FROM $gcdb->posts WHERE post_content LIKE %s", $like );
	 *
	 * Example Escape Chain:
	 *
	 *     $sql  = esc_sql( $gcdb->esc_like( $input ) );
	 *
	 *
	 * @param string $text The raw text to be escaped. The input typed by the user
	 *                     should have no extra or deleted slashes.
	 * @return string Text in the form of a LIKE phrase. The output is not SQL safe.
	 *                Call gcdb::prepare() or gcdb::_real_escape() next.
	 */
	public function esc_like( $text ) {
		return addcslashes( $text, '_%\\' );
	}

	/**
	 * Prints SQL/DB error.
	 *
	 *
	 * @global array $EZSQL_ERROR Stores error information of query and error string.
	 *
	 * @param string $str The error to display.
	 * @return void|false Void if the showing of errors is enabled, false if disabled.
	 */
	public function print_error( $str = '' ) {
		global $EZSQL_ERROR;

		if ( ! $str ) {
			if ( $this->use_mysqli ) {
				$str = mysqli_error( $this->dbh );
			} else {
				$str = mysql_error( $this->dbh );
			}
		}
		$EZSQL_ERROR[] = array(
			'query'     => $this->last_query,
			'error_str' => $str,
		);

		if ( $this->suppress_errors ) {
			return false;
		}

		gc_load_translations_early();

		$caller = $this->get_caller();
		if ( $caller ) {
			/* translators: 1: Database error message, 2: SQL query, 3: Name of the calling function. */
			$error_str = sprintf( __( 'GeChiUI数据库查询%2$s时发生%1$s错误，这是由%3$s查询的' ), $str, $this->last_query, $caller );
		} else {
			/* translators: 1: Database error message, 2: SQL query. */
			$error_str = sprintf( __( '查询%2$s时，GeChiUI数据库发生%1$s错误' ), $str, $this->last_query );
		}

		error_log( $error_str );

		// Are we showing errors?
		if ( ! $this->show_errors ) {
			return false;
		}

		// If there is an error then take note of it.
		if ( is_multisite() ) {
			$msg = sprintf(
				"%s [%s]\n%s\n",
				__( 'GeChiUI数据库错误：' ),
				$str,
				$this->last_query
			);

			if ( defined( 'ERRORLOGFILE' ) ) {
				error_log( $msg, 3, ERRORLOGFILE );
			}
			if ( defined( 'DIEONDBERROR' ) ) {
				gc_die( $msg );
			}
		} else {
			$str   = htmlspecialchars( $str, ENT_QUOTES );
			$query = htmlspecialchars( $this->last_query, ENT_QUOTES );

			printf(
				'<div id="error"><p class="gcdberror"><strong>%s</strong> [%s]<br /><code>%s</code></p></div>',
				__( 'GeChiUI数据库错误：' ),
				$str,
				$query
			);
		}
	}

	/**
	 * Enables showing of database errors.
	 *
	 * This function should be used only to enable showing of errors.
	 * gcdb::hide_errors() should be used instead for hiding errors.
	 *
	 *
	 * @see gcdb::hide_errors()
	 *
	 * @param bool $show Optional. Whether to show errors. Default true.
	 * @return bool Whether showing of errors was previously active.
	 */
	public function show_errors( $show = true ) {
		$errors            = $this->show_errors;
		$this->show_errors = $show;
		return $errors;
	}

	/**
	 * Disables showing of database errors.
	 *
	 * By default database errors are not shown.
	 *
	 *
	 * @see gcdb::show_errors()
	 *
	 * @return bool Whether showing of errors was previously active.
	 */
	public function hide_errors() {
		$show              = $this->show_errors;
		$this->show_errors = false;
		return $show;
	}

	/**
	 * Enables or disables suppressing of database errors.
	 *
	 * By default database errors are suppressed.
	 *
	 *
	 * @see gcdb::hide_errors()
	 *
	 * @param bool $suppress Optional. Whether to suppress errors. Default true.
	 * @return bool Whether suppressing of errors was previously active.
	 */
	public function suppress_errors( $suppress = true ) {
		$errors                = $this->suppress_errors;
		$this->suppress_errors = (bool) $suppress;
		return $errors;
	}

	/**
	 * Kills cached query results.
	 *
	 */
	public function flush() {
		$this->last_result   = array();
		$this->col_info      = null;
		$this->last_query    = null;
		$this->rows_affected = 0;
		$this->num_rows      = 0;
		$this->last_error    = '';

		if ( $this->use_mysqli && $this->result instanceof mysqli_result ) {
			mysqli_free_result( $this->result );
			$this->result = null;

			// Sanity check before using the handle.
			if ( empty( $this->dbh ) || ! ( $this->dbh instanceof mysqli ) ) {
				return;
			}

			// Clear out any results from a multi-query.
			while ( mysqli_more_results( $this->dbh ) ) {
				mysqli_next_result( $this->dbh );
			}
		} elseif ( is_resource( $this->result ) ) {
			mysql_free_result( $this->result );
		}
	}

	/**
	 * Connects to and selects database.
	 *
	 * If `$allow_bail` is false, the lack of database connection will need to be handled manually.
	 *
	 *
	 * @param bool $allow_bail Optional. Allows the function to bail. Default true.
	 * @return bool True with a successful connection, false on failure.
	 */
	public function db_connect( $allow_bail = true ) {
		$this->is_mysql = true;

		/*
		 * Deprecated in 3.9+ when using MySQLi. No equivalent
		 * $new_link parameter exists for mysqli_* functions.
		 */
		$new_link     = defined( 'MYSQL_NEW_LINK' ) ? MYSQL_NEW_LINK : true;
		$client_flags = defined( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;

		if ( $this->use_mysqli ) {
			/*
			 * Set the MySQLi error reporting off because GeChiUI handles its own.
			 * This is due to the default value change from `MYSQLI_REPORT_OFF`
			 * to `MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT` in PHP 8.1.
			 */
			mysqli_report( MYSQLI_REPORT_OFF );

			$this->dbh = mysqli_init();

			$host    = $this->dbhost;
			$port    = null;
			$socket  = null;
			$is_ipv6 = false;

			$host_data = $this->parse_db_host( $this->dbhost );
			if ( $host_data ) {
				list( $host, $port, $socket, $is_ipv6 ) = $host_data;
			}

			/*
			 * If using the `mysqlnd` library, the IPv6 address needs to be enclosed
			 * in square brackets, whereas it doesn't while using the `libmysqlclient` library.
			 * @see https://bugs.php.net/bug.php?id=67563
			 */
			if ( $is_ipv6 && extension_loaded( 'mysqlnd' ) ) {
				$host = "[$host]";
			}

			if ( GC_DEBUG ) {
				mysqli_real_connect( $this->dbh, $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );
			} else {
				// phpcs:ignore GeChiUI.PHP.NoSilencedErrors.Discouraged
				@mysqli_real_connect( $this->dbh, $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );
			}

			if ( $this->dbh->connect_errno ) {
				$this->dbh = null;

				/*
				 * It's possible ext/mysqli is misconfigured. Fall back to ext/mysql if:
				 *  - We haven't previously connected, and
				 *  - GC_USE_EXT_MYSQL isn't set to false, and
				 *  - ext/mysql is loaded.
				 */
				$attempt_fallback = true;

				if ( $this->has_connected ) {
					$attempt_fallback = false;
				} elseif ( defined( 'GC_USE_EXT_MYSQL' ) && ! GC_USE_EXT_MYSQL ) {
					$attempt_fallback = false;
				} elseif ( ! function_exists( 'mysql_connect' ) ) {
					$attempt_fallback = false;
				}

				if ( $attempt_fallback ) {
					$this->use_mysqli = false;
					return $this->db_connect( $allow_bail );
				}
			}
		} else {
			if ( GC_DEBUG ) {
				$this->dbh = mysql_connect( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
			} else {
				// phpcs:ignore GeChiUI.PHP.NoSilencedErrors.Discouraged
				$this->dbh = @mysql_connect( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
			}
		}

		if ( ! $this->dbh && $allow_bail ) {
			gc_load_translations_early();

			// Load custom DB error template, if present.
			if ( file_exists( GC_CONTENT_DIR . '/db-error.php' ) ) {
				require_once GC_CONTENT_DIR . '/db-error.php';
				die();
			}

			$message = '<h1>' . __( '建立数据库连接时出错' ) . "</h1>\n";

			$message .= '<p>' . sprintf(
				/* translators: 1: gc-config.php, 2: Database host. */
				__( '您看到此页面，则表示您在 %1$s 文件中定义的用户名和密码信息不正确，或是我们无法与  数据库服务器 %2$s 进行通信。也可能是您主机的数据库服务器已关闭。' ),
				'<code>gc-config.php</code>',
				'<code>' . htmlspecialchars( $this->dbhost, ENT_QUOTES ) . '</code>'
			) . "</p>\n";

			$message .= "<ul>\n";
			$message .= '<li>' . __( '您确定用户名和密码正确吗？' ) . "</li>\n";
			$message .= '<li>' . __( '您确定输入的主机名正确吗？' ) . "</li>\n";
			$message .= '<li>' . __( '您确定数据库服务器在运行吗？' ) . "</li>\n";
			$message .= "</ul>\n";

			$message .= '<p>' . sprintf(
				/* translators: %s: Support forums URL. */
				__( '如果您不明白这些意味着什么，您应该联系您的主机提供商。如果您仍需要帮助，请访问<a href="%s">GeChiUI支持论坛</a>。' ),
				__( 'https://www.gechiui.com/support/forums/' )
			) . "</p>\n";

			$this->bail( $message, 'db_connect_fail' );

			return false;
		} elseif ( $this->dbh ) {
			if ( ! $this->has_connected ) {
				$this->init_charset();
			}

			$this->has_connected = true;

			$this->set_charset( $this->dbh );

			$this->ready = true;
			$this->set_sql_mode();
			$this->select( $this->dbname, $this->dbh );

			return true;
		}

		return false;
	}

	/**
	 * Parses the DB_HOST setting to interpret it for mysqli_real_connect().
	 *
	 * mysqli_real_connect() doesn't support the host param including a port or socket
	 * like mysql_connect() does. This duplicates how mysql_connect() detects a port
	 * and/or socket file.
	 *
	 *
	 * @param string $host The DB_HOST setting to parse.
	 * @return array|false Array containing the host, the port, the socket and
	 *                     whether it is an IPv6 address, in that order.
	 *                     False if $host couldn't be parsed.
	 */
	public function parse_db_host( $host ) {
		$port    = null;
		$socket  = null;
		$is_ipv6 = false;

		// First peel off the socket parameter from the right, if it exists.
		$socket_pos = strpos( $host, ':/' );
		if ( false !== $socket_pos ) {
			$socket = substr( $host, $socket_pos + 1 );
			$host   = substr( $host, 0, $socket_pos );
		}

		// We need to check for an IPv6 address first.
		// An IPv6 address will always contain at least two colons.
		if ( substr_count( $host, ':' ) > 1 ) {
			$pattern = '#^(?:\[)?(?P<host>[0-9a-fA-F:]+)(?:\]:(?P<port>[\d]+))?#';
			$is_ipv6 = true;
		} else {
			// We seem to be dealing with an IPv4 address.
			$pattern = '#^(?P<host>[^:/]*)(?::(?P<port>[\d]+))?#';
		}

		$matches = array();
		$result  = preg_match( $pattern, $host, $matches );

		if ( 1 !== $result ) {
			// Couldn't parse the address, bail.
			return false;
		}

		$host = '';
		foreach ( array( 'host', 'port' ) as $component ) {
			if ( ! empty( $matches[ $component ] ) ) {
				$$component = $matches[ $component ];
			}
		}

		return array( $host, $port, $socket, $is_ipv6 );
	}

	/**
	 * Checks that the connection to the database is still up. If not, try to reconnect.
	 *
	 * If this function is unable to reconnect, it will forcibly die, or if called
	 * after the {@see 'template_redirect'} hook has been fired, return false instead.
	 *
	 * If `$allow_bail` is false, the lack of database connection will need to be handled manually.
	 *
	 *
	 * @param bool $allow_bail Optional. Allows the function to bail. Default true.
	 * @return bool|void True if the connection is up.
	 */
	public function check_connection( $allow_bail = true ) {
		if ( $this->use_mysqli ) {
			if ( ! empty( $this->dbh ) && mysqli_ping( $this->dbh ) ) {
				return true;
			}
		} else {
			if ( ! empty( $this->dbh ) && mysql_ping( $this->dbh ) ) {
				return true;
			}
		}

		$error_reporting = false;

		// Disable warnings, as we don't want to see a multitude of "unable to connect" messages.
		if ( GC_DEBUG ) {
			$error_reporting = error_reporting();
			error_reporting( $error_reporting & ~E_WARNING );
		}

		for ( $tries = 1; $tries <= $this->reconnect_retries; $tries++ ) {
			// On the last try, re-enable warnings. We want to see a single instance
			// of the "unable to connect" message on the bail() screen, if it appears.
			if ( $this->reconnect_retries === $tries && GC_DEBUG ) {
				error_reporting( $error_reporting );
			}

			if ( $this->db_connect( false ) ) {
				if ( $error_reporting ) {
					error_reporting( $error_reporting );
				}

				return true;
			}

			sleep( 1 );
		}

		// If template_redirect has already happened, it's too late for gc_die()/dead_db().
		// Let's just return and hope for the best.
		if ( did_action( 'template_redirect' ) ) {
			return false;
		}

		if ( ! $allow_bail ) {
			return false;
		}

		gc_load_translations_early();

		$message = '<h1>' . __( '连接至数据库时出错' ) . "</h1>\n";

		$message .= '<p>' . sprintf(
			/* translators: %s: Database host. */
			__( '这意味着我们无法与数据库服务器 %s 进行通信。也可能是您主机的数据库服务器未在运行。' ),
			'<code>' . htmlspecialchars( $this->dbhost, ENT_QUOTES ) . '</code>'
		) . "</p>\n";

		$message .= "<ul>\n";
		$message .= '<li>' . __( '您确定数据库服务器在运行吗？' ) . "</li>\n";
		$message .= '<li>' . __( '您确定数据库服务器并未运行在高负载下吗？' ) . "</li>\n";
		$message .= "</ul>\n";

		$message .= '<p>' . sprintf(
			/* translators: %s: Support forums URL. */
			__( '如果您不明白这些意味着什么，您应该联系您的主机提供商。如果您仍需要帮助，请访问<a href="%s">GeChiUI支持论坛</a>。' ),
			__( 'https://www.gechiui.com/support/forums/' )
		) . "</p>\n";

		// We weren't able to reconnect, so we better bail.
		$this->bail( $message, 'db_connect_fail' );

		// Call dead_db() if bail didn't die, because this database is no more.
		// It has ceased to be (at least temporarily).
		dead_db();
	}

	/**
	 * Performs a database query, using current database connection.
	 *
	 * More information can be found on the documentation page.
	 *
	 *
	 * @link https://developer.gechiui.com/reference/classes/gcdb/
	 *
	 * @param string $query Database query.
	 * @return int|bool Boolean true for CREATE, ALTER, TRUNCATE and DROP queries. Number of rows
	 *                  affected/selected for all other queries. Boolean false on error.
	 */
	public function query( $query ) {
		if ( ! $this->ready ) {
			$this->check_current_query = true;
			return false;
		}

		/**
		 * Filters the database query.
		 *
		 * Some queries are made before the plugins have been loaded,
		 * and thus cannot be filtered with this method.
		 *
		 *
		 * @param string $query Database query.
		 */
		$query = apply_filters( 'query', $query );

		if ( ! $query ) {
			$this->insert_id = 0;
			return false;
		}

		$this->flush();

		// Log how the function was called.
		$this->func_call = "\$db->query(\"$query\")";

		// If we're writing to the database, make sure the query will write safely.
		if ( $this->check_current_query && ! $this->check_ascii( $query ) ) {
			$stripped_query = $this->strip_invalid_text_from_query( $query );
			// strip_invalid_text_from_query() can perform queries, so we need
			// to flush again, just to make sure everything is clear.
			$this->flush();
			if ( $stripped_query !== $query ) {
				$this->insert_id  = 0;
				$this->last_query = $query;

				gc_load_translations_early();

				$this->last_error = __( 'GeChiUI 数据库错误：无法执行查询，因为它包含无效数据。' );

				return false;
			}
		}

		$this->check_current_query = true;

		// Keep track of the last query for debug.
		$this->last_query = $query;

		$this->_do_query( $query );

		// Database server has gone away, try to reconnect.
		$mysql_errno = 0;
		if ( ! empty( $this->dbh ) ) {
			if ( $this->use_mysqli ) {
				if ( $this->dbh instanceof mysqli ) {
					$mysql_errno = mysqli_errno( $this->dbh );
				} else {
					// $dbh is defined, but isn't a real connection.
					// Something has gone horribly wrong, let's try a reconnect.
					$mysql_errno = 2006;
				}
			} else {
				if ( is_resource( $this->dbh ) ) {
					$mysql_errno = mysql_errno( $this->dbh );
				} else {
					$mysql_errno = 2006;
				}
			}
		}

		if ( empty( $this->dbh ) || 2006 === $mysql_errno ) {
			if ( $this->check_connection() ) {
				$this->_do_query( $query );
			} else {
				$this->insert_id = 0;
				return false;
			}
		}

		// If there is an error then take note of it.
		if ( $this->use_mysqli ) {
			if ( $this->dbh instanceof mysqli ) {
				$this->last_error = mysqli_error( $this->dbh );
			} else {
				$this->last_error = __( '无法从 MySQL 检索错误消息' );
			}
		} else {
			if ( is_resource( $this->dbh ) ) {
				$this->last_error = mysql_error( $this->dbh );
			} else {
				$this->last_error = __( '无法从 MySQL 检索错误消息' );
			}
		}

		if ( $this->last_error ) {
			// Clear insert_id on a subsequent failed insert.
			if ( $this->insert_id && preg_match( '/^\s*(insert|replace)\s/i', $query ) ) {
				$this->insert_id = 0;
			}

			$this->print_error();
			return false;
		}

		if ( preg_match( '/^\s*(create|alter|truncate|drop)\s/i', $query ) ) {
			$return_val = $this->result;
		} elseif ( preg_match( '/^\s*(insert|delete|update|replace)\s/i', $query ) ) {
			if ( $this->use_mysqli ) {
				$this->rows_affected = mysqli_affected_rows( $this->dbh );
			} else {
				$this->rows_affected = mysql_affected_rows( $this->dbh );
			}
			// Take note of the insert_id.
			if ( preg_match( '/^\s*(insert|replace)\s/i', $query ) ) {
				if ( $this->use_mysqli ) {
					$this->insert_id = mysqli_insert_id( $this->dbh );
				} else {
					$this->insert_id = mysql_insert_id( $this->dbh );
				}
			}
			// Return number of rows affected.
			$return_val = $this->rows_affected;
		} else {
			$num_rows = 0;
			if ( $this->use_mysqli && $this->result instanceof mysqli_result ) {
				while ( $row = mysqli_fetch_object( $this->result ) ) {
					$this->last_result[ $num_rows ] = $row;
					$num_rows++;
				}
			} elseif ( is_resource( $this->result ) ) {
				while ( $row = mysql_fetch_object( $this->result ) ) {
					$this->last_result[ $num_rows ] = $row;
					$num_rows++;
				}
			}

			// Log and return the number of rows selected.
			$this->num_rows = $num_rows;
			$return_val     = $num_rows;
		}

		return $return_val;
	}

	/**
	 * Internal function to perform the mysql_query() call.
	 *
	 *
	 * @see gcdb::query()
	 *
	 * @param string $query The query to run.
	 */
	private function _do_query( $query ) {
		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
			$this->timer_start();
		}

		if ( ! empty( $this->dbh ) && $this->use_mysqli ) {
			$this->result = mysqli_query( $this->dbh, $query );
		} elseif ( ! empty( $this->dbh ) ) {
			$this->result = mysql_query( $query, $this->dbh );
		}
		$this->num_queries++;

		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
			$this->log_query(
				$query,
				$this->timer_stop(),
				$this->get_caller(),
				$this->time_start,
				array()
			);
		}
	}

	/**
	 * Logs query data.
	 *
	 *
	 * @param string $query           The query's SQL.
	 * @param float  $query_time      Total time spent on the query, in seconds.
	 * @param string $query_callstack Comma-separated list of the calling functions.
	 * @param float  $query_start     Unix timestamp of the time at the start of the query.
	 * @param array  $query_data      Custom query data.
	 */
	public function log_query( $query, $query_time, $query_callstack, $query_start, $query_data ) {
		/**
		 * Filters the custom data to log alongside a query.
		 *
		 * Caution should be used when modifying any of this data, it is recommended that any additional
		 * information you need to store about a query be added as a new associative array element.
		 *
		 *
		 * @param array  $query_data      Custom query data.
		 * @param string $query           The query's SQL.
		 * @param float  $query_time      Total time spent on the query, in seconds.
		 * @param string $query_callstack Comma-separated list of the calling functions.
		 * @param float  $query_start     Unix timestamp of the time at the start of the query.
		 */
		$query_data = apply_filters( 'log_query_custom_data', $query_data, $query, $query_time, $query_callstack, $query_start );

		$this->queries[] = array(
			$query,
			$query_time,
			$query_callstack,
			$query_start,
			$query_data,
		);
	}

	/**
	 * Generates and returns a placeholder escape string for use in queries returned by ::prepare().
	 *
	 *
	 * @return string String to escape placeholders.
	 */
	public function placeholder_escape() {
		static $placeholder;

		if ( ! $placeholder ) {
			// If ext/hash is not present, compat.php's hash_hmac() does not support sha256.
			$algo = function_exists( 'hash' ) ? 'sha256' : 'sha1';
			// Old GC installs may not have AUTH_SALT defined.
			$salt = defined( 'AUTH_SALT' ) && AUTH_SALT ? AUTH_SALT : (string) rand();

			$placeholder = '{' . hash_hmac( $algo, uniqid( $salt, true ), $salt ) . '}';
		}

		/*
		 * Add the filter to remove the placeholder escaper. Uses priority 0, so that anything
		 * else attached to this filter will receive the query with the placeholder string removed.
		 */
		if ( false === has_filter( 'query', array( $this, 'remove_placeholder_escape' ) ) ) {
			add_filter( 'query', array( $this, 'remove_placeholder_escape' ), 0 );
		}

		return $placeholder;
	}

	/**
	 * Adds a placeholder escape string, to escape anything that resembles a printf() placeholder.
	 *
	 *
	 * @param string $query The query to escape.
	 * @return string The query with the placeholder escape string inserted where necessary.
	 */
	public function add_placeholder_escape( $query ) {
		/*
		 * To prevent returning anything that even vaguely resembles a placeholder,
		 * we clobber every % we can find.
		 */
		return str_replace( '%', $this->placeholder_escape(), $query );
	}

	/**
	 * Removes the placeholder escape strings from a query.
	 *
	 *
	 * @param string $query The query from which the placeholder will be removed.
	 * @return string The query with the placeholder removed.
	 */
	public function remove_placeholder_escape( $query ) {
		return str_replace( $this->placeholder_escape(), '%', $query );
	}

	/**
	 * Inserts a row into the table.
	 *
	 * Examples:
	 *
	 *     gcdb::insert( 'table', array( 'column' => 'foo', 'field' => 'bar' ) )
	 *     gcdb::insert( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
	 *
	 *
	 * @see gcdb::prepare()
	 * @see gcdb::$field_types
	 * @see gc_set_gcdb_vars()
	 *
	 * @param string       $table  Table name.
	 * @param array        $data   Data to insert (in column => value pairs).
	 *                             Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 *                             Sending a null value will cause the column to be set to NULL - the corresponding
	 *                             format is ignored in this case.
	 * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data.
	 *                             If string, that format will be used for all of the values in $data.
	 *                             A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                             If omitted, all values in $data will be treated as strings unless otherwise
	 *                             specified in gcdb::$field_types.
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public function insert( $table, $data, $format = null ) {
		return $this->_insert_replace_helper( $table, $data, $format, 'INSERT' );
	}

	/**
	 * Replaces a row in the table.
	 *
	 * Examples:
	 *
	 *     gcdb::replace( 'table', array( 'column' => 'foo', 'field' => 'bar' ) )
	 *     gcdb::replace( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
	 *
	 *
	 * @see gcdb::prepare()
	 * @see gcdb::$field_types
	 * @see gc_set_gcdb_vars()
	 *
	 * @param string       $table  Table name.
	 * @param array        $data   Data to insert (in column => value pairs).
	 *                             Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 *                             Sending a null value will cause the column to be set to NULL - the corresponding
	 *                             format is ignored in this case.
	 * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data.
	 *                             If string, that format will be used for all of the values in $data.
	 *                             A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                             If omitted, all values in $data will be treated as strings unless otherwise
	 *                             specified in gcdb::$field_types.
	 * @return int|false The number of rows affected, or false on error.
	 */
	public function replace( $table, $data, $format = null ) {
		return $this->_insert_replace_helper( $table, $data, $format, 'REPLACE' );
	}

	/**
	 * Helper function for insert and replace.
	 *
	 * Runs an insert or replace query based on $type argument.
	 *
	 *
	 * @see gcdb::prepare()
	 * @see gcdb::$field_types
	 * @see gc_set_gcdb_vars()
	 *
	 * @param string       $table  Table name.
	 * @param array        $data   Data to insert (in column => value pairs).
	 *                             Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 *                             Sending a null value will cause the column to be set to NULL - the corresponding
	 *                             format is ignored in this case.
	 * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data.
	 *                             If string, that format will be used for all of the values in $data.
	 *                             A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                             If omitted, all values in $data will be treated as strings unless otherwise
	 *                             specified in gcdb::$field_types.
	 * @param string       $type   Optional. Type of operation. Possible values include 'INSERT' or 'REPLACE'.
	 *                             Default 'INSERT'.
	 * @return int|false The number of rows affected, or false on error.
	 */
	public function _insert_replace_helper( $table, $data, $format = null, $type = 'INSERT' ) {
		$this->insert_id = 0;

		if ( ! in_array( strtoupper( $type ), array( 'REPLACE', 'INSERT' ), true ) ) {
			return false;
		}

		$data = $this->process_fields( $table, $data, $format );
		if ( false === $data ) {
			return false;
		}

		$formats = array();
		$values  = array();
		foreach ( $data as $value ) {
			if ( is_null( $value['value'] ) ) {
				$formats[] = 'NULL';
				continue;
			}

			$formats[] = $value['format'];
			$values[]  = $value['value'];
		}

		$fields  = '`' . implode( '`, `', array_keys( $data ) ) . '`';
		$formats = implode( ', ', $formats );

		$sql = "$type INTO `$table` ($fields) VALUES ($formats)";

		$this->check_current_query = false;
		return $this->query( $this->prepare( $sql, $values ) );
	}

	/**
	 * Updates a row in the table.
	 *
	 * Examples:
	 *
	 *     gcdb::update( 'table', array( 'column' => 'foo', 'field' => 'bar' ), array( 'ID' => 1 ) )
	 *     gcdb::update( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( 'ID' => 1 ), array( '%s', '%d' ), array( '%d' ) )
	 *
	 *
	 * @see gcdb::prepare()
	 * @see gcdb::$field_types
	 * @see gc_set_gcdb_vars()
	 *
	 * @param string       $table        Table name.
	 * @param array        $data         Data to update (in column => value pairs).
	 *                                   Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 *                                   Sending a null value will cause the column to be set to NULL - the corresponding
	 *                                   format is ignored in this case.
	 * @param array        $where        A named array of WHERE clauses (in column => value pairs).
	 *                                   Multiple clauses will be joined with ANDs.
	 *                                   Both $where columns and $where values should be "raw".
	 *                                   Sending a null value will create an IS NULL comparison - the corresponding
	 *                                   format will be ignored in this case.
	 * @param array|string $format       Optional. An array of formats to be mapped to each of the values in $data.
	 *                                   If string, that format will be used for all of the values in $data.
	 *                                   A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                   If omitted, all values in $data will be treated as strings unless otherwise
	 *                                   specified in gcdb::$field_types.
	 * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
	 *                                   If string, that format will be used for all of the items in $where.
	 *                                   A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                   If omitted, all values in $where will be treated as strings.
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function update( $table, $data, $where, $format = null, $where_format = null ) {
		if ( ! is_array( $data ) || ! is_array( $where ) ) {
			return false;
		}

		$data = $this->process_fields( $table, $data, $format );
		if ( false === $data ) {
			return false;
		}
		$where = $this->process_fields( $table, $where, $where_format );
		if ( false === $where ) {
			return false;
		}

		$fields     = array();
		$conditions = array();
		$values     = array();
		foreach ( $data as $field => $value ) {
			if ( is_null( $value['value'] ) ) {
				$fields[] = "`$field` = NULL";
				continue;
			}

			$fields[] = "`$field` = " . $value['format'];
			$values[] = $value['value'];
		}
		foreach ( $where as $field => $value ) {
			if ( is_null( $value['value'] ) ) {
				$conditions[] = "`$field` IS NULL";
				continue;
			}

			$conditions[] = "`$field` = " . $value['format'];
			$values[]     = $value['value'];
		}

		$fields     = implode( ', ', $fields );
		$conditions = implode( ' AND ', $conditions );

		$sql = "UPDATE `$table` SET $fields WHERE $conditions";

		$this->check_current_query = false;
		return $this->query( $this->prepare( $sql, $values ) );
	}

	/**
	 * Deletes a row in the table.
	 *
	 * Examples:
	 *
	 *     gcdb::delete( 'table', array( 'ID' => 1 ) )
	 *     gcdb::delete( 'table', array( 'ID' => 1 ), array( '%d' ) )
	 *
	 *
	 * @see gcdb::prepare()
	 * @see gcdb::$field_types
	 * @see gc_set_gcdb_vars()
	 *
	 * @param string       $table        Table name.
	 * @param array        $where        A named array of WHERE clauses (in column => value pairs).
	 *                                   Multiple clauses will be joined with ANDs.
	 *                                   Both $where columns and $where values should be "raw".
	 *                                   Sending a null value will create an IS NULL comparison - the corresponding
	 *                                   format will be ignored in this case.
	 * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
	 *                                   If string, that format will be used for all of the items in $where.
	 *                                   A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                   If omitted, all values in $data will be treated as strings unless otherwise
	 *                                   specified in gcdb::$field_types.
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function delete( $table, $where, $where_format = null ) {
		if ( ! is_array( $where ) ) {
			return false;
		}

		$where = $this->process_fields( $table, $where, $where_format );
		if ( false === $where ) {
			return false;
		}

		$conditions = array();
		$values     = array();
		foreach ( $where as $field => $value ) {
			if ( is_null( $value['value'] ) ) {
				$conditions[] = "`$field` IS NULL";
				continue;
			}

			$conditions[] = "`$field` = " . $value['format'];
			$values[]     = $value['value'];
		}

		$conditions = implode( ' AND ', $conditions );

		$sql = "DELETE FROM `$table` WHERE $conditions";

		$this->check_current_query = false;
		return $this->query( $this->prepare( $sql, $values ) );
	}

	/**
	 * Processes arrays of field/value pairs and field formats.
	 *
	 * This is a helper method for gcdb's CRUD methods, which take field/value pairs
	 * for inserts, updates, and where clauses. This method first pairs each value
	 * with a format. Then it determines the charset of that field, using that
	 * to determine if any invalid text would be stripped. If text is stripped,
	 * then field processing is rejected and the query fails.
	 *
	 *
	 * @param string $table  Table name.
	 * @param array  $data   Field/value pair.
	 * @param mixed  $format Format for each field.
	 * @return array|false An array of fields that contain paired value and formats.
	 *                     False for invalid values.
	 */
	protected function process_fields( $table, $data, $format ) {
		$data = $this->process_field_formats( $data, $format );
		if ( false === $data ) {
			return false;
		}

		$data = $this->process_field_charsets( $data, $table );
		if ( false === $data ) {
			return false;
		}

		$data = $this->process_field_lengths( $data, $table );
		if ( false === $data ) {
			return false;
		}

		$converted_data = $this->strip_invalid_text( $data );

		if ( $data !== $converted_data ) {

			$problem_fields = array();
			foreach ( $data as $field => $value ) {
				if ( $value !== $converted_data[ $field ] ) {
					$problem_fields[] = $field;
				}
			}

			gc_load_translations_early();

			if ( 1 === count( $problem_fields ) ) {
				$this->last_error = sprintf(
					/* translators: %s: Database field where the error occurred. */
					__( 'GeChiUI 数据库错误：处理以下字段的值失败：%s。提供的值可能太长或包含无效数据。' ),
					reset( $problem_fields )
				);
			} else {
				$this->last_error = sprintf(
					/* translators: %s: Database fields where the error occurred. */
					__( 'GeChiUI 数据库错误：处理以下字段的值失败：%s。提供的值可能太长或包含无效数据。' ),
					implode( ', ', $problem_fields )
				);
			}

			return false;
		}

		return $data;
	}

	/**
	 * Prepares arrays of value/format pairs as passed to gcdb CRUD methods.
	 *
	 *
	 * @param array $data   Array of fields to values.
	 * @param mixed $format Formats to be mapped to the values in $data.
	 * @return array Array, keyed by field names with values being an array
	 *               of 'value' and 'format' keys.
	 */
	protected function process_field_formats( $data, $format ) {
		$formats          = (array) $format;
		$original_formats = $formats;

		foreach ( $data as $field => $value ) {
			$value = array(
				'value'  => $value,
				'format' => '%s',
			);

			if ( ! empty( $format ) ) {
				$value['format'] = array_shift( $formats );
				if ( ! $value['format'] ) {
					$value['format'] = reset( $original_formats );
				}
			} elseif ( isset( $this->field_types[ $field ] ) ) {
				$value['format'] = $this->field_types[ $field ];
			}

			$data[ $field ] = $value;
		}

		return $data;
	}

	/**
	 * Adds field charsets to field/value/format arrays generated by gcdb::process_field_formats().
	 *
	 *
	 * @param array  $data  As it comes from the gcdb::process_field_formats() method.
	 * @param string $table Table name.
	 * @return array|false The same array as $data with additional 'charset' keys.
	 *                     False on failure.
	 */
	protected function process_field_charsets( $data, $table ) {
		foreach ( $data as $field => $value ) {
			if ( '%d' === $value['format'] || '%f' === $value['format'] ) {
				/*
				 * We can skip this field if we know it isn't a string.
				 * This checks %d/%f versus ! %s because its sprintf() could take more.
				 */
				$value['charset'] = false;
			} else {
				$value['charset'] = $this->get_col_charset( $table, $field );
				if ( is_gc_error( $value['charset'] ) ) {
					return false;
				}
			}

			$data[ $field ] = $value;
		}

		return $data;
	}

	/**
	 * For string fields, records the maximum string length that field can safely save.
	 *
	 *
	 * @param array  $data  As it comes from the gcdb::process_field_charsets() method.
	 * @param string $table Table name.
	 * @return array|false The same array as $data with additional 'length' keys, or false if
	 *                     any of the values were too long for their corresponding field.
	 */
	protected function process_field_lengths( $data, $table ) {
		foreach ( $data as $field => $value ) {
			if ( '%d' === $value['format'] || '%f' === $value['format'] ) {
				/*
				 * We can skip this field if we know it isn't a string.
				 * This checks %d/%f versus ! %s because its sprintf() could take more.
				 */
				$value['length'] = false;
			} else {
				$value['length'] = $this->get_col_length( $table, $field );
				if ( is_gc_error( $value['length'] ) ) {
					return false;
				}
			}

			$data[ $field ] = $value;
		}

		return $data;
	}

	/**
	 * Retrieves one variable from the database.
	 *
	 * Executes a SQL query and returns the value from the SQL result.
	 * If the SQL result contains more than one column and/or more than one row,
	 * the value in the column and row specified is returned. If $query is null,
	 * the value in the specified column and row from the previous SQL result is returned.
	 *
	 *
	 * @param string|null $query Optional. SQL query. Defaults to null, use the result from the previous query.
	 * @param int         $x     Optional. Column of value to return. Indexed from 0.
	 * @param int         $y     Optional. Row of value to return. Indexed from 0.
	 * @return string|null Database query result (as string), or null on failure.
	 */
	public function get_var( $query = null, $x = 0, $y = 0 ) {
		$this->func_call = "\$db->get_var(\"$query\", $x, $y)";

		if ( $query ) {
			if ( $this->check_current_query && $this->check_safe_collation( $query ) ) {
				$this->check_current_query = false;
			}

			$this->query( $query );
		}

		// Extract var out of cached results based on x,y vals.
		if ( ! empty( $this->last_result[ $y ] ) ) {
			$values = array_values( get_object_vars( $this->last_result[ $y ] ) );
		}

		// If there is a value return it, else return null.
		return ( isset( $values[ $x ] ) && '' !== $values[ $x ] ) ? $values[ $x ] : null;
	}

	/**
	 * Retrieves one row from the database.
	 *
	 * Executes a SQL query and returns the row from the SQL result.
	 *
	 *
	 * @param string|null $query  SQL query.
	 * @param string      $output Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
	 *                            correspond to an stdClass object, an associative array, or a numeric array,
	 *                            respectively. Default OBJECT.
	 * @param int         $y      Optional. Row to return. Indexed from 0.
	 * @return array|object|null|void Database query result in format specified by $output or null on failure.
	 */
	public function get_row( $query = null, $output = OBJECT, $y = 0 ) {
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";

		if ( $query ) {
			if ( $this->check_current_query && $this->check_safe_collation( $query ) ) {
				$this->check_current_query = false;
			}

			$this->query( $query );
		} else {
			return null;
		}

		if ( ! isset( $this->last_result[ $y ] ) ) {
			return null;
		}

		if ( OBJECT === $output ) {
			return $this->last_result[ $y ] ? $this->last_result[ $y ] : null;
		} elseif ( ARRAY_A === $output ) {
			return $this->last_result[ $y ] ? get_object_vars( $this->last_result[ $y ] ) : null;
		} elseif ( ARRAY_N === $output ) {
			return $this->last_result[ $y ] ? array_values( get_object_vars( $this->last_result[ $y ] ) ) : null;
		} elseif ( OBJECT === strtoupper( $output ) ) {
			// Back compat for OBJECT being previously case-insensitive.
			return $this->last_result[ $y ] ? $this->last_result[ $y ] : null;
		} else {
			$this->print_error( ' $db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N' );
		}
	}

	/**
	 * Retrieves one column from the database.
	 *
	 * Executes a SQL query and returns the column from the SQL result.
	 * If the SQL result contains more than one column, the column specified is returned.
	 * If $query is null, the specified column from the previous SQL result is returned.
	 *
	 *
	 * @param string|null $query Optional. SQL query. Defaults to previous query.
	 * @param int         $x     Optional. Column to return. Indexed from 0.
	 * @return array Database query result. Array indexed from 0 by SQL result row number.
	 */
	public function get_col( $query = null, $x = 0 ) {
		if ( $query ) {
			if ( $this->check_current_query && $this->check_safe_collation( $query ) ) {
				$this->check_current_query = false;
			}

			$this->query( $query );
		}

		$new_array = array();
		// Extract the column values.
		if ( $this->last_result ) {
			for ( $i = 0, $j = count( $this->last_result ); $i < $j; $i++ ) {
				$new_array[ $i ] = $this->get_var( null, $x, $i );
			}
		}
		return $new_array;
	}

	/**
	 * Retrieves an entire SQL result set from the database (i.e., many rows).
	 *
	 * Executes a SQL query and returns the entire SQL result.
	 *
	 *
	 * @param string $query  SQL query.
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 *                       With one of the first three, return an array of rows indexed
	 *                       from 0 by SQL result row number. Each row is an associative array
	 *                       (column => value, ...), a numerically indexed array (0 => value, ...),
	 *                       or an object ( ->column = value ), respectively. With OBJECT_K,
	 *                       return an associative array of row objects keyed by the value
	 *                       of each row's first column's value. Duplicate keys are discarded.
	 * @return array|object|null Database query results.
	 */
	public function get_results( $query = null, $output = OBJECT ) {
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		if ( $query ) {
			if ( $this->check_current_query && $this->check_safe_collation( $query ) ) {
				$this->check_current_query = false;
			}

			$this->query( $query );
		} else {
			return null;
		}

		$new_array = array();
		if ( OBJECT === $output ) {
			// Return an integer-keyed array of row objects.
			return $this->last_result;
		} elseif ( OBJECT_K === $output ) {
			// Return an array of row objects with keys from column 1.
			// (Duplicates are discarded.)
			if ( $this->last_result ) {
				foreach ( $this->last_result as $row ) {
					$var_by_ref = get_object_vars( $row );
					$key        = array_shift( $var_by_ref );
					if ( ! isset( $new_array[ $key ] ) ) {
						$new_array[ $key ] = $row;
					}
				}
			}
			return $new_array;
		} elseif ( ARRAY_A === $output || ARRAY_N === $output ) {
			// Return an integer-keyed array of...
			if ( $this->last_result ) {
				foreach ( (array) $this->last_result as $row ) {
					if ( ARRAY_N === $output ) {
						// ...integer-keyed row arrays.
						$new_array[] = array_values( get_object_vars( $row ) );
					} else {
						// ...column name-keyed row arrays.
						$new_array[] = get_object_vars( $row );
					}
				}
			}
			return $new_array;
		} elseif ( strtoupper( $output ) === OBJECT ) {
			// Back compat for OBJECT being previously case-insensitive.
			return $this->last_result;
		}
		return null;
	}

	/**
	 * Retrieves the character set for the given table.
	 *
	 *
	 * @param string $table Table name.
	 * @return string|GC_Error Table character set, GC_Error object if it couldn't be found.
	 */
	protected function get_table_charset( $table ) {
		$tablekey = strtolower( $table );

		/**
		 * Filters the table charset value before the DB is checked.
		 *
		 * Returning a non-null value from the filter will effectively short-circuit
		 * checking the DB for the charset, returning that value instead.
		 *
		 *
		 * @param string|GC_Error|null $charset The character set to use, GC_Error object
		 *                                      if it couldn't be found. Default null.
		 * @param string               $table   The name of the table being checked.
		 */
		$charset = apply_filters( 'pre_get_table_charset', null, $table );
		if ( null !== $charset ) {
			return $charset;
		}

		if ( isset( $this->table_charset[ $tablekey ] ) ) {
			return $this->table_charset[ $tablekey ];
		}

		$charsets = array();
		$columns  = array();

		$table_parts = explode( '.', $table );
		$table       = '`' . implode( '`.`', $table_parts ) . '`';
		$results     = $this->get_results( "SHOW FULL COLUMNS FROM $table" );
		if ( ! $results ) {
			return new GC_Error( 'gcdb_get_table_charset_failure', __( '无法检索表字符集。' ) );
		}

		foreach ( $results as $column ) {
			$columns[ strtolower( $column->Field ) ] = $column;
		}

		$this->col_meta[ $tablekey ] = $columns;

		foreach ( $columns as $column ) {
			if ( ! empty( $column->Collation ) ) {
				list( $charset ) = explode( '_', $column->Collation );

				// If the current connection can't support utf8mb4 characters, let's only send 3-byte utf8 characters.
				if ( 'utf8mb4' === $charset && ! $this->has_cap( 'utf8mb4' ) ) {
					$charset = 'utf8';
				}

				$charsets[ strtolower( $charset ) ] = true;
			}

			list( $type ) = explode( '(', $column->Type );

			// A binary/blob means the whole query gets treated like this.
			if ( in_array( strtoupper( $type ), array( 'BINARY', 'VARBINARY', 'TINYBLOB', 'MEDIUMBLOB', 'BLOB', 'LONGBLOB' ), true ) ) {
				$this->table_charset[ $tablekey ] = 'binary';
				return 'binary';
			}
		}

		// utf8mb3 is an alias for utf8.
		if ( isset( $charsets['utf8mb3'] ) ) {
			$charsets['utf8'] = true;
			unset( $charsets['utf8mb3'] );
		}

		// Check if we have more than one charset in play.
		$count = count( $charsets );
		if ( 1 === $count ) {
			$charset = key( $charsets );
		} elseif ( 0 === $count ) {
			// No charsets, assume this table can store whatever.
			$charset = false;
		} else {
			// More than one charset. Remove latin1 if present and recalculate.
			unset( $charsets['latin1'] );
			$count = count( $charsets );
			if ( 1 === $count ) {
				// Only one charset (besides latin1).
				$charset = key( $charsets );
			} elseif ( 2 === $count && isset( $charsets['utf8'], $charsets['utf8mb4'] ) ) {
				// Two charsets, but they're utf8 and utf8mb4, use utf8.
				$charset = 'utf8';
			} else {
				// Two mixed character sets. ascii.
				$charset = 'ascii';
			}
		}

		$this->table_charset[ $tablekey ] = $charset;
		return $charset;
	}

	/**
	 * Retrieves the character set for the given column.
	 *
	 *
	 * @param string $table  Table name.
	 * @param string $column Column name.
	 * @return string|false|GC_Error Column character set as a string. False if the column has
	 *                               no character set. GC_Error object if there was an error.
	 */
	public function get_col_charset( $table, $column ) {
		$tablekey  = strtolower( $table );
		$columnkey = strtolower( $column );

		/**
		 * Filters the column charset value before the DB is checked.
		 *
		 * Passing a non-null value to the filter will short-circuit
		 * checking the DB for the charset, returning that value instead.
		 *
		 *
		 * @param string|null $charset The character set to use. Default null.
		 * @param string      $table   The name of the table being checked.
		 * @param string      $column  The name of the column being checked.
		 */
		$charset = apply_filters( 'pre_get_col_charset', null, $table, $column );
		if ( null !== $charset ) {
			return $charset;
		}

		// Skip this entirely if this isn't a MySQL database.
		if ( empty( $this->is_mysql ) ) {
			return false;
		}

		if ( empty( $this->table_charset[ $tablekey ] ) ) {
			// This primes column information for us.
			$table_charset = $this->get_table_charset( $table );
			if ( is_gc_error( $table_charset ) ) {
				return $table_charset;
			}
		}

		// If still no column information, return the table charset.
		if ( empty( $this->col_meta[ $tablekey ] ) ) {
			return $this->table_charset[ $tablekey ];
		}

		// If this column doesn't exist, return the table charset.
		if ( empty( $this->col_meta[ $tablekey ][ $columnkey ] ) ) {
			return $this->table_charset[ $tablekey ];
		}

		// Return false when it's not a string column.
		if ( empty( $this->col_meta[ $tablekey ][ $columnkey ]->Collation ) ) {
			return false;
		}

		list( $charset ) = explode( '_', $this->col_meta[ $tablekey ][ $columnkey ]->Collation );
		return $charset;
	}

	/**
	 * Retrieves the maximum string length allowed in a given column.
	 *
	 * The length may either be specified as a byte length or a character length.
	 *
	 *
	 * @param string $table  Table name.
	 * @param string $column Column name.
	 * @return array|false|GC_Error array( 'length' => (int), 'type' => 'byte' | 'char' ).
	 *                              False if the column has no length (for example, numeric column).
	 *                              GC_Error object if there was an error.
	 */
	public function get_col_length( $table, $column ) {
		$tablekey  = strtolower( $table );
		$columnkey = strtolower( $column );

		// Skip this entirely if this isn't a MySQL database.
		if ( empty( $this->is_mysql ) ) {
			return false;
		}

		if ( empty( $this->col_meta[ $tablekey ] ) ) {
			// This primes column information for us.
			$table_charset = $this->get_table_charset( $table );
			if ( is_gc_error( $table_charset ) ) {
				return $table_charset;
			}
		}

		if ( empty( $this->col_meta[ $tablekey ][ $columnkey ] ) ) {
			return false;
		}

		$typeinfo = explode( '(', $this->col_meta[ $tablekey ][ $columnkey ]->Type );

		$type = strtolower( $typeinfo[0] );
		if ( ! empty( $typeinfo[1] ) ) {
			$length = trim( $typeinfo[1], ')' );
		} else {
			$length = false;
		}

		switch ( $type ) {
			case 'char':
			case 'varchar':
				return array(
					'type'   => 'char',
					'length' => (int) $length,
				);

			case 'binary':
			case 'varbinary':
				return array(
					'type'   => 'byte',
					'length' => (int) $length,
				);

			case 'tinyblob':
			case 'tinytext':
				return array(
					'type'   => 'byte',
					'length' => 255,        // 2^8 - 1
				);

			case 'blob':
			case 'text':
				return array(
					'type'   => 'byte',
					'length' => 65535,      // 2^16 - 1
				);

			case 'mediumblob':
			case 'mediumtext':
				return array(
					'type'   => 'byte',
					'length' => 16777215,   // 2^24 - 1
				);

			case 'longblob':
			case 'longtext':
				return array(
					'type'   => 'byte',
					'length' => 4294967295, // 2^32 - 1
				);

			default:
				return false;
		}
	}

	/**
	 * Checks if a string is ASCII.
	 *
	 * The negative regex is faster for non-ASCII strings, as it allows
	 * the search to finish as soon as it encounters a non-ASCII character.
	 *
	 *
	 * @param string $string String to check.
	 * @return bool True if ASCII, false if not.
	 */
	protected function check_ascii( $string ) {
		if ( function_exists( 'mb_check_encoding' ) ) {
			if ( mb_check_encoding( $string, 'ASCII' ) ) {
				return true;
			}
		} elseif ( ! preg_match( '/[^\x00-\x7F]/', $string ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if the query is accessing a collation considered safe on the current version of MySQL.
	 *
	 *
	 * @param string $query The query to check.
	 * @return bool True if the collation is safe, false if it isn't.
	 */
	protected function check_safe_collation( $query ) {
		if ( $this->checking_collation ) {
			return true;
		}

		// We don't need to check the collation for queries that don't read data.
		$query = ltrim( $query, "\r\n\t (" );
		if ( preg_match( '/^(?:SHOW|DESCRIBE|DESC|EXPLAIN|CREATE)\s/i', $query ) ) {
			return true;
		}

		// All-ASCII queries don't need extra checking.
		if ( $this->check_ascii( $query ) ) {
			return true;
		}

		$table = $this->get_table_from_query( $query );
		if ( ! $table ) {
			return false;
		}

		$this->checking_collation = true;
		$collation                = $this->get_table_charset( $table );
		$this->checking_collation = false;

		// Tables with no collation, or latin1 only, don't need extra checking.
		if ( false === $collation || 'latin1' === $collation ) {
			return true;
		}

		$table = strtolower( $table );
		if ( empty( $this->col_meta[ $table ] ) ) {
			return false;
		}

		// If any of the columns don't have one of these collations, it needs more sanity checking.
		foreach ( $this->col_meta[ $table ] as $col ) {
			if ( empty( $col->Collation ) ) {
				continue;
			}

			if ( ! in_array( $col->Collation, array( 'utf8_general_ci', 'utf8_bin', 'utf8mb4_general_ci', 'utf8mb4_bin' ), true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Strips any invalid characters based on value/charset pairs.
	 *
	 *
	 * @param array $data Array of value arrays. Each value array has the keys 'value' and 'charset'.
	 *                    An optional 'ascii' key can be set to false to avoid redundant ASCII checks.
	 * @return array|GC_Error The $data parameter, with invalid characters removed from each value.
	 *                        This works as a passthrough: any additional keys such as 'field' are
	 *                        retained in each value array. If we cannot remove invalid characters,
	 *                        a GC_Error object is returned.
	 */
	protected function strip_invalid_text( $data ) {
		$db_check_string = false;

		foreach ( $data as &$value ) {
			$charset = $value['charset'];

			if ( is_array( $value['length'] ) ) {
				$length                  = $value['length']['length'];
				$truncate_by_byte_length = 'byte' === $value['length']['type'];
			} else {
				$length = false;
				// Since we have no length, we'll never truncate. Initialize the variable to false.
				// True would take us through an unnecessary (for this case) codepath below.
				$truncate_by_byte_length = false;
			}

			// There's no charset to work with.
			if ( false === $charset ) {
				continue;
			}

			// Column isn't a string.
			if ( ! is_string( $value['value'] ) ) {
				continue;
			}

			$needs_validation = true;
			if (
				// latin1 can store any byte sequence.
				'latin1' === $charset
			||
				// ASCII is always OK.
				( ! isset( $value['ascii'] ) && $this->check_ascii( $value['value'] ) )
			) {
				$truncate_by_byte_length = true;
				$needs_validation        = false;
			}

			if ( $truncate_by_byte_length ) {
				mbstring_binary_safe_encoding();
				if ( false !== $length && strlen( $value['value'] ) > $length ) {
					$value['value'] = substr( $value['value'], 0, $length );
				}
				reset_mbstring_encoding();

				if ( ! $needs_validation ) {
					continue;
				}
			}

			// utf8 can be handled by regex, which is a bunch faster than a DB lookup.
			if ( ( 'utf8' === $charset || 'utf8mb3' === $charset || 'utf8mb4' === $charset ) && function_exists( 'mb_strlen' ) ) {
				$regex = '/
					(
						(?: [\x00-\x7F]                  # single-byte sequences   0xxxxxxx
						|   [\xC2-\xDF][\x80-\xBF]       # double-byte sequences   110xxxxx 10xxxxxx
						|   \xE0[\xA0-\xBF][\x80-\xBF]   # triple-byte sequences   1110xxxx 10xxxxxx * 2
						|   [\xE1-\xEC][\x80-\xBF]{2}
						|   \xED[\x80-\x9F][\x80-\xBF]
						|   [\xEE-\xEF][\x80-\xBF]{2}';

				if ( 'utf8mb4' === $charset ) {
					$regex .= '
						|    \xF0[\x90-\xBF][\x80-\xBF]{2} # four-byte sequences   11110xxx 10xxxxxx * 3
						|    [\xF1-\xF3][\x80-\xBF]{3}
						|    \xF4[\x80-\x8F][\x80-\xBF]{2}
					';
				}

				$regex         .= '){1,40}                          # ...one or more times
					)
					| .                                  # anything else
					/x';
				$value['value'] = preg_replace( $regex, '$1', $value['value'] );

				if ( false !== $length && mb_strlen( $value['value'], 'UTF-8' ) > $length ) {
					$value['value'] = mb_substr( $value['value'], 0, $length, 'UTF-8' );
				}
				continue;
			}

			// We couldn't use any local conversions, send it to the DB.
			$value['db']     = true;
			$db_check_string = true;
		}
		unset( $value ); // Remove by reference.

		if ( $db_check_string ) {
			$queries = array();
			foreach ( $data as $col => $value ) {
				if ( ! empty( $value['db'] ) ) {
					// We're going to need to truncate by characters or bytes, depending on the length value we have.
					if ( isset( $value['length']['type'] ) && 'byte' === $value['length']['type'] ) {
						// Using binary causes LEFT() to truncate by bytes.
						$charset = 'binary';
					} else {
						$charset = $value['charset'];
					}

					if ( $this->charset ) {
						$connection_charset = $this->charset;
					} else {
						if ( $this->use_mysqli ) {
							$connection_charset = mysqli_character_set_name( $this->dbh );
						} else {
							$connection_charset = mysql_client_encoding();
						}
					}

					if ( is_array( $value['length'] ) ) {
						$length          = sprintf( '%.0f', $value['length']['length'] );
						$queries[ $col ] = $this->prepare( "CONVERT( LEFT( CONVERT( %s USING $charset ), $length ) USING $connection_charset )", $value['value'] );
					} elseif ( 'binary' !== $charset ) {
						// If we don't have a length, there's no need to convert binary - it will always return the same result.
						$queries[ $col ] = $this->prepare( "CONVERT( CONVERT( %s USING $charset ) USING $connection_charset )", $value['value'] );
					}

					unset( $data[ $col ]['db'] );
				}
			}

			$sql = array();
			foreach ( $queries as $column => $query ) {
				if ( ! $query ) {
					continue;
				}

				$sql[] = $query . " AS x_$column";
			}

			$this->check_current_query = false;
			$row                       = $this->get_row( 'SELECT ' . implode( ', ', $sql ), ARRAY_A );
			if ( ! $row ) {
				return new GC_Error( 'gcdb_strip_invalid_text_failure', __( '无法去除无效文本。' ) );
			}

			foreach ( array_keys( $data ) as $column ) {
				if ( isset( $row[ "x_$column" ] ) ) {
					$data[ $column ]['value'] = $row[ "x_$column" ];
				}
			}
		}

		return $data;
	}

	/**
	 * Strips any invalid characters from the query.
	 *
	 *
	 * @param string $query Query to convert.
	 * @return string|GC_Error The converted query, or a GC_Error object if the conversion fails.
	 */
	protected function strip_invalid_text_from_query( $query ) {
		// We don't need to check the collation for queries that don't read data.
		$trimmed_query = ltrim( $query, "\r\n\t (" );
		if ( preg_match( '/^(?:SHOW|DESCRIBE|DESC|EXPLAIN|CREATE)\s/i', $trimmed_query ) ) {
			return $query;
		}

		$table = $this->get_table_from_query( $query );
		if ( $table ) {
			$charset = $this->get_table_charset( $table );
			if ( is_gc_error( $charset ) ) {
				return $charset;
			}

			// We can't reliably strip text from tables containing binary/blob columns.
			if ( 'binary' === $charset ) {
				return $query;
			}
		} else {
			$charset = $this->charset;
		}

		$data = array(
			'value'   => $query,
			'charset' => $charset,
			'ascii'   => false,
			'length'  => false,
		);

		$data = $this->strip_invalid_text( array( $data ) );
		if ( is_gc_error( $data ) ) {
			return $data;
		}

		return $data[0]['value'];
	}

	/**
	 * Strips any invalid characters from the string for a given table and column.
	 *
	 *
	 * @param string $table  Table name.
	 * @param string $column Column name.
	 * @param string $value  The text to check.
	 * @return string|GC_Error The converted string, or a GC_Error object if the conversion fails.
	 */
	public function strip_invalid_text_for_column( $table, $column, $value ) {
		if ( ! is_string( $value ) ) {
			return $value;
		}

		$charset = $this->get_col_charset( $table, $column );
		if ( ! $charset ) {
			// Not a string column.
			return $value;
		} elseif ( is_gc_error( $charset ) ) {
			// Bail on real errors.
			return $charset;
		}

		$data = array(
			$column => array(
				'value'   => $value,
				'charset' => $charset,
				'length'  => $this->get_col_length( $table, $column ),
			),
		);

		$data = $this->strip_invalid_text( $data );
		if ( is_gc_error( $data ) ) {
			return $data;
		}

		return $data[ $column ]['value'];
	}

	/**
	 * Finds the first table name referenced in a query.
	 *
	 *
	 * @param string $query The query to search.
	 * @return string|false The table name found, or false if a table couldn't be found.
	 */
	protected function get_table_from_query( $query ) {
		// Remove characters that can legally trail the table name.
		$query = rtrim( $query, ';/-#' );

		// Allow (select...) union [...] style queries. Use the first query's table name.
		$query = ltrim( $query, "\r\n\t (" );

		// Strip everything between parentheses except nested selects.
		$query = preg_replace( '/\((?!\s*select)[^(]*?\)/is', '()', $query );

		// Quickly match most common queries.
		if ( preg_match(
			'/^\s*(?:'
				. 'SELECT.*?\s+FROM'
				. '|INSERT(?:\s+LOW_PRIORITY|\s+DELAYED|\s+HIGH_PRIORITY)?(?:\s+IGNORE)?(?:\s+INTO)?'
				. '|REPLACE(?:\s+LOW_PRIORITY|\s+DELAYED)?(?:\s+INTO)?'
				. '|UPDATE(?:\s+LOW_PRIORITY)?(?:\s+IGNORE)?'
				. '|DELETE(?:\s+LOW_PRIORITY|\s+QUICK|\s+IGNORE)*(?:.+?FROM)?'
			. ')\s+((?:[0-9a-zA-Z$_.`-]|[\xC2-\xDF][\x80-\xBF])+)/is',
			$query,
			$maybe
		) ) {
			return str_replace( '`', '', $maybe[1] );
		}

		// SHOW TABLE STATUS and SHOW TABLES WHERE Name = 'gc_posts'
		if ( preg_match( '/^\s*SHOW\s+(?:TABLE\s+STATUS|(?:FULL\s+)?TABLES).+WHERE\s+Name\s*=\s*("|\')((?:[0-9a-zA-Z$_.-]|[\xC2-\xDF][\x80-\xBF])+)\\1/is', $query, $maybe ) ) {
			return $maybe[2];
		}

		/*
		 * SHOW TABLE STATUS LIKE and SHOW TABLES LIKE 'gc\_123\_%'
		 * This quoted LIKE operand seldom holds a full table name.
		 * It is usually a pattern for matching a prefix so we just
		 * strip the trailing % and unescape the _ to get 'gc_123_'
		 * which drop-ins can use for routing these SQL statements.
		 */
		if ( preg_match( '/^\s*SHOW\s+(?:TABLE\s+STATUS|(?:FULL\s+)?TABLES)\s+(?:WHERE\s+Name\s+)?LIKE\s*("|\')((?:[\\\\0-9a-zA-Z$_.-]|[\xC2-\xDF][\x80-\xBF])+)%?\\1/is', $query, $maybe ) ) {
			return str_replace( '\\_', '_', $maybe[2] );
		}

		// Big pattern for the rest of the table-related queries.
		if ( preg_match(
			'/^\s*(?:'
				. '(?:EXPLAIN\s+(?:EXTENDED\s+)?)?SELECT.*?\s+FROM'
				. '|DESCRIBE|DESC|EXPLAIN|HANDLER'
				. '|(?:LOCK|UNLOCK)\s+TABLE(?:S)?'
				. '|(?:RENAME|OPTIMIZE|BACKUP|RESTORE|CHECK|CHECKSUM|ANALYZE|REPAIR).*\s+TABLE'
				. '|TRUNCATE(?:\s+TABLE)?'
				. '|CREATE(?:\s+TEMPORARY)?\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?'
				. '|ALTER(?:\s+IGNORE)?\s+TABLE'
				. '|DROP\s+TABLE(?:\s+IF\s+EXISTS)?'
				. '|CREATE(?:\s+\w+)?\s+INDEX.*\s+ON'
				. '|DROP\s+INDEX.*\s+ON'
				. '|LOAD\s+DATA.*INFILE.*INTO\s+TABLE'
				. '|(?:GRANT|REVOKE).*ON\s+TABLE'
				. '|SHOW\s+(?:.*FROM|.*TABLE)'
			. ')\s+\(*\s*((?:[0-9a-zA-Z$_.`-]|[\xC2-\xDF][\x80-\xBF])+)\s*\)*/is',
			$query,
			$maybe
		) ) {
			return str_replace( '`', '', $maybe[1] );
		}

		return false;
	}

	/**
	 * Loads the column metadata from the last query.
	 *
	 */
	protected function load_col_info() {
		if ( $this->col_info ) {
			return;
		}

		if ( $this->use_mysqli ) {
			$num_fields = mysqli_num_fields( $this->result );
			for ( $i = 0; $i < $num_fields; $i++ ) {
				$this->col_info[ $i ] = mysqli_fetch_field( $this->result );
			}
		} else {
			$num_fields = mysql_num_fields( $this->result );
			for ( $i = 0; $i < $num_fields; $i++ ) {
				$this->col_info[ $i ] = mysql_fetch_field( $this->result, $i );
			}
		}
	}

	/**
	 * Retrieves column metadata from the last query.
	 *
	 *
	 * @param string $info_type  Optional. Possible values include 'name', 'table', 'def', 'max_length',
	 *                           'not_null', 'primary_key', 'multiple_key', 'unique_key', 'numeric',
	 *                           'blob', 'type', 'unsigned', 'zerofill'. Default 'name'.
	 * @param int    $col_offset Optional. 0: col name. 1: which table the col's in. 2: col's max length.
	 *                           3: if the col is numeric. 4: col's type. Default -1.
	 * @return mixed Column results.
	 */
	public function get_col_info( $info_type = 'name', $col_offset = -1 ) {
		$this->load_col_info();

		if ( $this->col_info ) {
			if ( -1 === $col_offset ) {
				$i         = 0;
				$new_array = array();
				foreach ( (array) $this->col_info as $col ) {
					$new_array[ $i ] = $col->{$info_type};
					$i++;
				}
				return $new_array;
			} else {
				return $this->col_info[ $col_offset ]->{$info_type};
			}
		}
	}

	/**
	 * Starts the timer, for debugging purposes.
	 *
	 *
	 * @return true
	 */
	public function timer_start() {
		$this->time_start = microtime( true );
		return true;
	}

	/**
	 * Stops the debugging timer.
	 *
	 *
	 * @return float Total time spent on the query, in seconds.
	 */
	public function timer_stop() {
		return ( microtime( true ) - $this->time_start );
	}

	/**
	 * Wraps errors in a nice header and footer and dies.
	 *
	 * Will not die if gcdb::$show_errors is false.
	 *
	 *
	 * @param string $message    The error message.
	 * @param string $error_code Optional. A computer-readable string to identify the error.
	 *                           Default '500'.
	 * @return void|false Void if the showing of errors is enabled, false if disabled.
	 */
	public function bail( $message, $error_code = '500' ) {
		if ( $this->show_errors ) {
			$error = '';

			if ( $this->use_mysqli ) {
				if ( $this->dbh instanceof mysqli ) {
					$error = mysqli_error( $this->dbh );
				} elseif ( mysqli_connect_errno() ) {
					$error = mysqli_connect_error();
				}
			} else {
				if ( is_resource( $this->dbh ) ) {
					$error = mysql_error( $this->dbh );
				} else {
					$error = mysql_error();
				}
			}

			if ( $error ) {
				$message = '<p><code>' . $error . "</code></p>\n" . $message;
			}

			gc_die( $message );
		} else {
			if ( class_exists( 'GC_Error', false ) ) {
				$this->error = new GC_Error( $error_code, $message );
			} else {
				$this->error = $message;
			}

			return false;
		}
	}

	/**
	 * Closes the current database connection.
	 *
	 *
	 * @return bool True if the connection was successfully closed,
	 *              false if it wasn't, or if the connection doesn't exist.
	 */
	public function close() {
		if ( ! $this->dbh ) {
			return false;
		}

		if ( $this->use_mysqli ) {
			$closed = mysqli_close( $this->dbh );
		} else {
			$closed = mysql_close( $this->dbh );
		}

		if ( $closed ) {
			$this->dbh           = null;
			$this->ready         = false;
			$this->has_connected = false;
		}

		return $closed;
	}

	/**
	 * Determines whether MySQL database is at least the required minimum version.
	 *
	 *
	 * @global string $gc_version             The GeChiUI version string.
	 * @global string $required_mysql_version The required MySQL version string.
	 * @return void|GC_Error
	 */
	public function check_database_version() {
		global $gc_version, $required_mysql_version;
		// Make sure the server has the required MySQL version.
		if ( version_compare( $this->db_version(), $required_mysql_version, '<' ) ) {
			/* translators: 1: GeChiUI version number, 2: Minimum required MySQL version number. */
			return new GC_Error( 'database_version', sprintf( __( '<strong>错误</strong>：GeChiUI %1$s需要MySQL %2$s以上版本' ), $gc_version, $required_mysql_version ) );
		}
	}

	/**
	 * Determines whether the database supports collation.
	 *
	 * Called when GeChiUI is generating the table scheme.
	 *
	 * Use `gcdb::has_cap( 'collation' )`.
	 *
	 * @deprecated 3.5.0 Use gcdb::has_cap()
	 *
	 * @return bool True if collation is supported, false if not.
	 */
	public function supports_collation() {
		_deprecated_function( __FUNCTION__, '3.5.0', 'gcdb::has_cap( \'collation\' )' );
		return $this->has_cap( 'collation' );
	}

	/**
	 * Retrieves the database character collate.
	 *
	 *
	 * @return string The database character collate.
	 */
	public function get_charset_collate() {
		$charset_collate = '';

		if ( ! empty( $this->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $this->charset";
		}
		if ( ! empty( $this->collate ) ) {
			$charset_collate .= " COLLATE $this->collate";
		}

		return $charset_collate;
	}

	/**
	 * Determines if a database supports a particular feature.
	 *
	 *
	 * @see gcdb::db_version()
	 *
	 * @param string $db_cap The feature to check for. Accepts 'collation', 'group_concat',
	 *                       'subqueries', 'set_charset', 'utf8mb4', or 'utf8mb4_520'.
	 * @return int|false Whether the database feature is supported, false otherwise.
	 */
	public function has_cap( $db_cap ) {
		$version = $this->db_version();

		switch ( strtolower( $db_cap ) ) {
			case 'collation': 
			case 'group_concat':
			case 'subqueries': 
				return version_compare( $version, '4.1', '>=' );
			case 'set_charset':
				return version_compare( $version, '5.0.7', '>=' );
			case 'utf8mb4': 
				if ( version_compare( $version, '5.5.3', '<' ) ) {
					return false;
				}
				if ( $this->use_mysqli ) {
					$client_version = mysqli_get_client_info();
				} else {
					$client_version = mysql_get_client_info();
				}

				/*
				 * libmysql has supported utf8mb4 since 5.5.3, same as the MySQL server.
				 * mysqlnd has supported utf8mb4 since 5.0.9.
				 */
				if ( false !== strpos( $client_version, 'mysqlnd' ) ) {
					$client_version = preg_replace( '/^\D+([\d.]+).*/', '$1', $client_version );
					return version_compare( $client_version, '5.0.9', '>=' );
				} else {
					return version_compare( $client_version, '5.5.3', '>=' );
				}
			case 'utf8mb4_520':
				return version_compare( $version, '5.6', '>=' );
		}

		return false;
	}

	/**
	 * Retrieves a comma-separated list of the names of the functions that called gcdb.
	 *
	 *
	 * @return string Comma-separated list of the calling functions.
	 */
	public function get_caller() {
		return gc_debug_backtrace_summary( __CLASS__ );
	}

	/**
	 * Retrieves the database server version.
	 *
	 *
	 * @return string|null Version number on success, null on failure.
	 */
	public function db_version() {
		return preg_replace( '/[^0-9.].*/', '', $this->db_server_info() );
	}

	/**
	 * Retrieves full database server information.
	 *
	 *
	 * @return string|false Server info on success, false on failure.
	 */
	public function db_server_info() {
		if ( $this->use_mysqli ) {
			$server_info = mysqli_get_server_info( $this->dbh );
		} else {
			$server_info = mysql_get_server_info( $this->dbh );
		}

		return $server_info;
	}
}
