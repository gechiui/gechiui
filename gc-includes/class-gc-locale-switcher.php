<?php
/**
 * Locale API: GC_Locale_Switcher class
 *
 * @package GeChiUI
 * @subpackage i18n
 *
 */

/**
 * Core class used for switching locales.
 *
 *
 */
class GC_Locale_Switcher {
	/**
	 * Locale stack.
	 *
	 * @var string[]
	 */
	private $locales = array();

	/**
	 * Original locale.
	 *
	 * @var string
	 */
	private $original_locale;

	/**
	 * Holds all available languages.
	 *
	 * @var array An array of language codes (file names without the .mo extension).
	 */
	private $available_languages = array();

	/**
	 * Constructor.
	 *
	 * Stores the original locale as well as a list of all available languages.
	 *
	 */
	public function __construct() {
		$this->original_locale     = determine_locale();
		$this->available_languages = array_merge( array( 'zh_CN' ), get_available_languages() );
	}

	/**
	 * Initializes the locale switcher.
	 *
	 * Hooks into the {@see 'locale'} filter to change the locale on the fly.
	 *
	 */
	public function init() {
		add_filter( 'locale', array( $this, 'filter_locale' ) );
	}

	/**
	 * Switches the translations according to the given locale.
	 *
	 *
	 * @param string $locale The locale to switch to.
	 * @return bool True on success, false on failure.
	 */
	public function switch_to_locale( $locale ) {
		$current_locale = determine_locale();
		if ( $current_locale === $locale ) {
			return false;
		}

		if ( ! in_array( $locale, $this->available_languages, true ) ) {
			return false;
		}

		$this->locales[] = $locale;

		$this->change_locale( $locale );

		/**
		 * Fires when the locale is switched.
		 *
		 *
		 * @param string $locale The new locale.
		 */
		do_action( 'switch_locale', $locale );

		return true;
	}

	/**
	 * Restores the translations according to the previous locale.
	 *
	 *
	 * @return string|false Locale on success, false on failure.
	 */
	public function restore_previous_locale() {
		$previous_locale = array_pop( $this->locales );

		if ( null === $previous_locale ) {
			// The stack is empty, bail.
			return false;
		}

		$locale = end( $this->locales );

		if ( ! $locale ) {
			// There's nothing left in the stack: go back to the original locale.
			$locale = $this->original_locale;
		}

		$this->change_locale( $locale );

		/**
		 * Fires when the locale is restored to the previous one.
		 *
		 *
		 * @param string $locale          The new locale.
		 * @param string $previous_locale The previous locale.
		 */
		do_action( 'restore_previous_locale', $locale, $previous_locale );

		return $locale;
	}

	/**
	 * Restores the translations according to the original locale.
	 *
	 *
	 * @return string|false Locale on success, false on failure.
	 */
	public function restore_current_locale() {
		if ( empty( $this->locales ) ) {
			return false;
		}

		$this->locales = array( $this->original_locale );

		return $this->restore_previous_locale();
	}

	/**
	 * Whether switch_to_locale() is in effect.
	 *
	 *
	 * @return bool True if the locale has been switched, false otherwise.
	 */
	public function is_switched() {
		return ! empty( $this->locales );
	}

	/**
	 * Filters the locale of the GeChiUI installation.
	 *
	 *
	 * @param string $locale The locale of the GeChiUI installation.
	 * @return string The locale currently being switched to.
	 */
	public function filter_locale( $locale ) {
		$switched_locale = end( $this->locales );

		if ( $switched_locale ) {
			return $switched_locale;
		}

		return $locale;
	}

	/**
	 * Load translations for a given locale.
	 *
	 * When switching to a locale, translations for this locale must be loaded from scratch.
	 *
	 *
	 * @global Mo[] $l10n An array of all currently loaded text domains.
	 *
	 * @param string $locale The locale to load translations for.
	 */
	private function load_translations( $locale ) {
		global $l10n;

		$domains = $l10n ? array_keys( $l10n ) : array();

		load_default_textdomain( $locale );

		foreach ( $domains as $domain ) {
			if ( 'default' === $domain ) {
				continue;
			}

			unload_textdomain( $domain );
			get_translations_for_domain( $domain );
		}
	}

	/**
	 * Changes the site's locale to the given one.
	 *
	 * Loads the translations, changes the global `$gc_locale` object and updates
	 * all post type labels.
	 *
	 *
	 * @global GC_Locale $gc_locale GeChiUI date and time locale object.
	 *
	 * @param string $locale The locale to change to.
	 */
	private function change_locale( $locale ) {
		// Reset translation availability information.
		_get_path_to_translation( null, true );

		$this->load_translations( $locale );

		$GLOBALS['gc_locale'] = new GC_Locale();

		/**
		 * Fires when the locale is switched to or restored.
		 *
		 *
		 * @param string $locale The new locale.
		 */
		do_action( 'change_locale', $locale );
	}
}
