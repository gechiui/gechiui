<?php
/**
 * Locale API: GC_Locale class
 *
 * @package GeChiUI
 * @subpackage i18n
 *
 */

/**
 * Core class used to store translated data for a locale.
 *
 *
 *
 */
class GC_Locale {
	/**
	 * Stores the translated strings for the full weekday names.
	 *
	 * @var string[]
	 */
	public $weekday;

	/**
	 * Stores the translated strings for the one character weekday names.
	 *
	 * There is a hack to make sure that Tuesday and Thursday, as well
	 * as Sunday and Saturday, don't conflict. See init() method for more.
	 *
	 * @see GC_Locale::init() for how to handle the hack.
	 *
	 * @var string[]
	 */
	public $weekday_initial;

	/**
	 * Stores the translated strings for the abbreviated weekday names.
	 *
	 * @var string[]
	 */
	public $weekday_abbrev;

	/**
	 * Stores the translated strings for the full month names.
	 *
	 * @var string[]
	 */
	public $month;

	/**
	 * Stores the translated strings for the month names in genitive case, if the locale specifies.
	 *
	 * @var string[]
	 */
	public $month_genitive;

	/**
	 * Stores the translated strings for the abbreviated month names.
	 *
	 * @var string[]
	 */
	public $month_abbrev;

	/**
	 * Stores the translated strings for 'am' and 'pm'.
	 *
	 * Also the capitalized versions.
	 *
	 * @var string[]
	 */
	public $meridiem;

	/**
	 * The text direction of the locale language.
	 *
	 * Default is left to right 'ltr'.
	 *
	 * @var string
	 */
	public $text_direction = 'ltr';

	/**
	 * The thousands separator and decimal point values used for localizing numbers.
	 *
	 * @var array
	 */
	public $number_format;

	/**
	 * Constructor which calls helper methods to set up object variables.
	 *
	 */
	public function __construct() {
		$this->init();
		$this->register_globals();
	}

	/**
	 * Sets up the translated strings and object properties.
	 *
	 * The method creates the translatable strings for various
	 * calendar elements. Which allows for specifying locale
	 * specific calendar names and text direction.
	 *
	 *
	 * @global string $text_direction
	 * @global string $gc_version     The GeChiUI version string.
	 */
	public function init() {
		// The weekdays.
		$this->weekday[0] = /* translators: Weekday. */ __( '星期日' );
		$this->weekday[1] = /* translators: Weekday. */ __( '星期一' );
		$this->weekday[2] = /* translators: Weekday. */ __( '星期二' );
		$this->weekday[3] = /* translators: Weekday. */ __( '星期三' );
		$this->weekday[4] = /* translators: Weekday. */ __( '星期四' );
		$this->weekday[5] = /* translators: Weekday. */ __( '星期五' );
		$this->weekday[6] = /* translators: Weekday. */ __( '星期六' );

		// The first letter of each day.
		$this->weekday_initial[ __( '星期日' ) ]    = /* translators: One-letter abbreviation of the weekday. */ _x( '日', 'Sunday initial' );
		$this->weekday_initial[ __( '星期一' ) ]    = /* translators: One-letter abbreviation of the weekday. */ _x( '一', 'Monday initial' );
		$this->weekday_initial[ __( '星期二' ) ]   = /* translators: One-letter abbreviation of the weekday. */ _x( '二', 'Tuesday initial' );
		$this->weekday_initial[ __( '星期三' ) ] = /* translators: One-letter abbreviation of the weekday. */ _x( '三', 'Wednesday initial' );
		$this->weekday_initial[ __( '星期四' ) ]  = /* translators: One-letter abbreviation of the weekday. */ _x( '四', 'Thursday initial' );
		$this->weekday_initial[ __( '星期五' ) ]    = /* translators: One-letter abbreviation of the weekday. */ _x( '五', 'Friday initial' );
		$this->weekday_initial[ __( '星期六' ) ]  = /* translators: One-letter abbreviation of the weekday. */ _x( '六', 'Saturday initial' );

		// Abbreviations for each day.
		$this->weekday_abbrev[ __( '星期日' ) ]    = /* translators: Three-letter abbreviation of the weekday. */ __( '周日' );
		$this->weekday_abbrev[ __( '星期一' ) ]    = /* translators: Three-letter abbreviation of the weekday. */ __( '周一' );
		$this->weekday_abbrev[ __( '星期二' ) ]   = /* translators: Three-letter abbreviation of the weekday. */ __( '周二' );
		$this->weekday_abbrev[ __( '星期三' ) ] = /* translators: Three-letter abbreviation of the weekday. */ __( '周三' );
		$this->weekday_abbrev[ __( '星期四' ) ]  = /* translators: Three-letter abbreviation of the weekday. */ __( '周四' );
		$this->weekday_abbrev[ __( '星期五' ) ]    = /* translators: Three-letter abbreviation of the weekday. */ __( '周五' );
		$this->weekday_abbrev[ __( '星期六' ) ]  = /* translators: Three-letter abbreviation of the weekday. */ __( '周六' );

		// The months.
		$this->month['01'] = /* translators: Month name. */ __( '1月' );
		$this->month['02'] = /* translators: Month name. */ __( '2月' );
		$this->month['03'] = /* translators: Month name. */ __( '3月' );
		$this->month['04'] = /* translators: Month name. */ __( '4月' );
		$this->month['05'] = /* translators: Month name. */ __( '5月' );
		$this->month['06'] = /* translators: Month name. */ __( '6月' );
		$this->month['07'] = /* translators: Month name. */ __( '7月' );
		$this->month['08'] = /* translators: Month name. */ __( '8月' );
		$this->month['09'] = /* translators: Month name. */ __( '9月' );
		$this->month['10'] = /* translators: Month name. */ __( '10月' );
		$this->month['11'] = /* translators: Month name. */ __( '11月' );
		$this->month['12'] = /* translators: Month name. */ __( '12月' );

		// The months, genitive.
		$this->month_genitive['01'] = /* translators: Month name, genitive. */ _x( '1月', 'genitive' );
		$this->month_genitive['02'] = /* translators: Month name, genitive. */ _x( '2月', 'genitive' );
		$this->month_genitive['03'] = /* translators: Month name, genitive. */ _x( '3月', 'genitive' );
		$this->month_genitive['04'] = /* translators: Month name, genitive. */ _x( '4月', 'genitive' );
		$this->month_genitive['05'] = /* translators: Month name, genitive. */ _x( '5月', 'genitive' );
		$this->month_genitive['06'] = /* translators: Month name, genitive. */ _x( '6月', 'genitive' );
		$this->month_genitive['07'] = /* translators: Month name, genitive. */ _x( '7月', 'genitive' );
		$this->month_genitive['08'] = /* translators: Month name, genitive. */ _x( '8月', 'genitive' );
		$this->month_genitive['09'] = /* translators: Month name, genitive. */ _x( '9月', 'genitive' );
		$this->month_genitive['10'] = /* translators: Month name, genitive. */ _x( '10月', 'genitive' );
		$this->month_genitive['11'] = /* translators: Month name, genitive. */ _x( '11月', 'genitive' );
		$this->month_genitive['12'] = /* translators: Month name, genitive. */ _x( '12月', 'genitive' );

		// Abbreviations for each month.
		$this->month_abbrev[ __( '1月' ) ]   = /* translators: Three-letter abbreviation of the month. */ _x( '1月', 'January abbreviation' );
		$this->month_abbrev[ __( '2月' ) ]  = /* translators: Three-letter abbreviation of the month. */ _x( '2月', 'February abbreviation' );
		$this->month_abbrev[ __( '3月' ) ]     = /* translators: Three-letter abbreviation of the month. */ _x( '3月', 'March abbreviation' );
		$this->month_abbrev[ __( '4月' ) ]     = /* translators: Three-letter abbreviation of the month. */ _x( '4月', 'April abbreviation' );
		$this->month_abbrev[ __( '5月' ) ]       = /* translators: Three-letter abbreviation of the month. */ _x( '5月', 'May abbreviation' );
		$this->month_abbrev[ __( '6月' ) ]      = /* translators: Three-letter abbreviation of the month. */ _x( '6月', 'June abbreviation' );
		$this->month_abbrev[ __( '7月' ) ]      = /* translators: Three-letter abbreviation of the month. */ _x( '7月', 'July abbreviation' );
		$this->month_abbrev[ __( '8月' ) ]    = /* translators: Three-letter abbreviation of the month. */ _x( '8月', 'August abbreviation' );
		$this->month_abbrev[ __( '9月' ) ] = /* translators: Three-letter abbreviation of the month. */ _x( '9月', 'September abbreviation' );
		$this->month_abbrev[ __( '10月' ) ]   = /* translators: Three-letter abbreviation of the month. */ _x( '10月', 'October abbreviation' );
		$this->month_abbrev[ __( '11月' ) ]  = /* translators: Three-letter abbreviation of the month. */ _x( '11月', 'November abbreviation' );
		$this->month_abbrev[ __( '12月' ) ]  = /* translators: Three-letter abbreviation of the month. */ _x( '12月', 'December abbreviation' );

		// The meridiems.
		$this->meridiem['am'] = __( '上午' );
		$this->meridiem['pm'] = __( '下午' );
		$this->meridiem['AM'] = __( '上午' );
		$this->meridiem['PM'] = __( '下午' );

		// Numbers formatting.
		// See https://www.php.net/number_format

		/* translators: $thousands_sep argument for https://www.php.net/number_format, default is ',' */
		$thousands_sep = __( ',' );

		// Replace space with a non-breaking space to avoid wrapping.
		$thousands_sep = str_replace( ' ', '&nbsp;', $thousands_sep );

		$this->number_format['thousands_sep'] = ( ',' === $thousands_sep ) ? ',' : $thousands_sep;

		/* translators: $dec_point argument for https://www.php.net/number_format, default is '.' */
		$decimal_point = __( '.' );

		$this->number_format['decimal_point'] = ( '.' === $decimal_point ) ? '.' : $decimal_point;

		// Set text direction.
		if ( isset( $GLOBALS['text_direction'] ) ) {
			$this->text_direction = $GLOBALS['text_direction'];

			/* translators: 'rtl' or 'ltr'. This sets the text direction for GeChiUI. */
		} elseif ( 'rtl' === _x( 'ltr', '文本方向' ) ) {
			$this->text_direction = 'rtl';
		}
	}

	/**
	 * Retrieve the full translated weekday word.
	 *
	 * Week starts on translated Sunday and can be fetched
	 * by using 0 (zero). So the week starts with 0 (zero)
	 * and ends on Saturday with is fetched by using 6 (six).
	 *
	 *
	 * @param int $weekday_number 0 for Sunday through 6 Saturday.
	 * @return string Full translated weekday.
	 */
	public function get_weekday( $weekday_number ) {
		return $this->weekday[ $weekday_number ];
	}

	/**
	 * Retrieve the translated weekday initial.
	 *
	 * The weekday initial is retrieved by the translated
	 * full weekday word. When translating the weekday initial
	 * pay attention to make sure that the starting letter does
	 * not conflict.
	 *
	 *
	 * @param string $weekday_name Full translated weekday word.
	 * @return string Translated weekday initial.
	 */
	public function get_weekday_initial( $weekday_name ) {
		return $this->weekday_initial[ $weekday_name ];
	}

	/**
	 * Retrieve the translated weekday abbreviation.
	 *
	 * The weekday abbreviation is retrieved by the translated
	 * full weekday word.
	 *
	 *
	 * @param string $weekday_name Full translated weekday word.
	 * @return string Translated weekday abbreviation.
	 */
	public function get_weekday_abbrev( $weekday_name ) {
		return $this->weekday_abbrev[ $weekday_name ];
	}

	/**
	 * Retrieve the full translated month by month number.
	 *
	 * The $month_number parameter has to be a string
	 * because it must have the '0' in front of any number
	 * that is less than 10. Starts from '01' and ends at
	 * '12'.
	 *
	 * You can use an integer instead and it will add the
	 * '0' before the numbers less than 10 for you.
	 *
	 *
	 * @param string|int $month_number '01' through '12'.
	 * @return string Translated full month name.
	 */
	public function get_month( $month_number ) {
		return $this->month[ zeroise( $month_number, 2 ) ];
	}

	/**
	 * Retrieve translated version of month abbreviation string.
	 *
	 * The $month_name parameter is expected to be the translated or
	 * translatable version of the month.
	 *
	 *
	 * @param string $month_name Translated month to get abbreviated version.
	 * @return string Translated abbreviated month.
	 */
	public function get_month_abbrev( $month_name ) {
		return $this->month_abbrev[ $month_name ];
	}

	/**
	 * Retrieve translated version of meridiem string.
	 *
	 * The $meridiem parameter is expected to not be translated.
	 *
	 *
	 * @param string $meridiem Either 'am', 'pm', 'AM', or 'PM'. Not translated version.
	 * @return string Translated version
	 */
	public function get_meridiem( $meridiem ) {
		return $this->meridiem[ $meridiem ];
	}

	/**
	 * Global variables are deprecated.
	 *
	 * For backward compatibility only.
	 *
	 * @deprecated For backward compatibility only.
	 *
	 * @global array $weekday
	 * @global array $weekday_initial
	 * @global array $weekday_abbrev
	 * @global array $month
	 * @global array $month_abbrev
	 *
	 */
	public function register_globals() {
		$GLOBALS['weekday']         = $this->weekday;
		$GLOBALS['weekday_initial'] = $this->weekday_initial;
		$GLOBALS['weekday_abbrev']  = $this->weekday_abbrev;
		$GLOBALS['month']           = $this->month;
		$GLOBALS['month_abbrev']    = $this->month_abbrev;
	}

	/**
	 * Checks if current locale is RTL.
	 *
	 * @return bool Whether locale is RTL.
	 */
	public function is_rtl() {
		return 'rtl' === $this->text_direction;
	}

	/**
	 * Register date/time format strings for general POT.
	 *
	 * Private, unused method to add some date/time formats translated
	 * on gc-admin/options-general.php to the general POT that would
	 * otherwise be added to the admin POT.
	 *
	 */
	public function _strings_for_pot() {
		/* translators: Localized date format, see https://www.php.net/manual/datetime.format.php */
		__( 'Y年n月j日' );
		/* translators: Localized time format, see https://www.php.net/manual/datetime.format.php */
		__( 'ag:i' );
		/* translators: Localized date and time format, see https://www.php.net/manual/datetime.format.php */
		__( 'Y年n月j日ag:i' );
	}
}
