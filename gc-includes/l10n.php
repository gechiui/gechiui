<?php
/**
 * Core Translation API
 *
 * @package GeChiUI
 * @subpackage i18n
 *
 */

/**
 * Retrieves the current locale.
 *
 * If the locale is set, then it will filter the locale in the {@see 'locale'}
 * filter hook and return the value.
 *
 * If the locale is not set already, then the GCLANG constant is used if it is
 * defined. Then it is filtered through the {@see 'locale'} filter hook and
 * the value for the locale global set and the locale is returned.
 *
 * The process to get the locale should only be done once, but the locale will
 * always be filtered using the {@see 'locale'} hook.
 *
 *
 *
 * @global string $locale           The current locale.
 * @global string $gc_local_package Locale code of the package.
 *
 * @return string The locale of the blog or from the {@see 'locale'} hook.
 */
function get_locale() {
	global $locale, $gc_local_package;

	if ( isset( $locale ) ) {
		/** This filter is documented in gc-includes/l10n.php */
		return apply_filters( 'locale', $locale );
	}

	if ( isset( $gc_local_package ) ) {
		$locale = $gc_local_package;
	}

	// GCLANG was defined in gc-config.
	if ( defined( 'GCLANG' ) ) {
		$locale = GCLANG;
	}

	// If multisite, check options.
	if ( is_multisite() ) {
		// Don't check blog option when installing.
		if ( gc_installing() ) {
			$ms_locale = get_site_option( 'GCLANG' );
		} else {
			$ms_locale = get_option( 'GCLANG' );
			if ( false === $ms_locale ) {
				$ms_locale = get_site_option( 'GCLANG' );
			}
		}

		if ( false !== $ms_locale ) {
			$locale = $ms_locale;
		}
	} else {
		$db_locale = get_option( 'GCLANG' );
		if ( false !== $db_locale ) {
			$locale = $db_locale;
		}
	}

	if ( empty( $locale ) ) {
		$locale = 'zh_CN';
	}

	/**
	 * Filters the locale ID of the GeChiUI installation.
	 *
	 *
	 * @param string $locale The locale ID.
	 */
	return apply_filters( 'locale', $locale );
}

/**
 * Retrieves the locale of a user.
 *
 * If the user has a locale set to a non-empty string then it will be
 * returned. Otherwise it returns the locale of get_locale().
 *
 *
 *
 * @param int|GC_User $user_id User's ID or a GC_User object. Defaults to current user.
 * @return string The locale of the user.
 */
function get_user_locale( $user_id = 0 ) {
	$user = false;
	if ( 0 === $user_id && function_exists( 'gc_get_current_user' ) ) {
		$user = gc_get_current_user();
	} elseif ( $user_id instanceof GC_User ) {
		$user = $user_id;
	} elseif ( $user_id && is_numeric( $user_id ) ) {
		$user = get_user_by( 'id', $user_id );
	}

	if ( ! $user ) {
		return get_locale();
	}

	$locale = $user->locale;
	return $locale ? $locale : get_locale();
}

/**
 * Determine the current locale desired for the request.
 *
 *
 *
 * @global string $pagenow
 *
 * @return string The determined locale.
 */
function determine_locale() {
	/**
	 * Filters the locale for the current request prior to the default determination process.
	 *
	 * Using this filter allows to override the default logic, effectively short-circuiting the function.
	 *
	 *
	 * @param string|null $locale The locale to return and short-circuit. Default null.
	 */
	$determined_locale = apply_filters( 'pre_determine_locale', null );

	if ( ! empty( $determined_locale ) && is_string( $determined_locale ) ) {
		return $determined_locale;
	}

	$determined_locale = get_locale();

	if ( is_admin() ) {
		$determined_locale = get_user_locale();
	}

	if ( isset( $_GET['_locale'] ) && 'user' === $_GET['_locale'] && gc_is_json_request() ) {
		$determined_locale = get_user_locale();
	}

	$gc_lang = '';

	if ( ! empty( $_GET['gc_lang'] ) ) {
		$gc_lang = sanitize_text_field( $_GET['gc_lang'] );
	} elseif ( ! empty( $_COOKIE['gc_lang'] ) ) {
		$gc_lang = sanitize_text_field( $_COOKIE['gc_lang'] );
	}

	if ( ! empty( $gc_lang ) && ! empty( $GLOBALS['pagenow'] ) && 'gc-login.php' === $GLOBALS['pagenow'] ) {
		$determined_locale = $gc_lang;
	}

	/**
	 * Filters the locale for the current request.
	 *
	 *
	 * @param string $locale The locale.
	 */
	return apply_filters( 'determine_locale', $determined_locale );
}

/**
 * Retrieve the translation of $text.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 * *Note:* Don't use translate() directly, use __() or related functions.
 *
 *
 *
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string Translated text.
 */
function translate( $text, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translation  = $translations->translate( $text );

	/**
	 * Filters text with its translation.
	 *
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Text to translate.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( 'gettext', $translation, $text, $domain );

	/**
	 * Filters text with its translation for a domain.
	 *
	 * The dynamic portion of the hook name, `$domain`, refers to the text domain.
	 *
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Text to translate.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( "gettext_{$domain}", $translation, $text, $domain );

	return $translation;
}

/**
 * Remove last item on a pipe-delimited string.
 *
 * Meant for removing the last item in a string, such as 'Role name|User role'. The original
 * string will be returned if no pipe '|' characters are found in the string.
 *
 *
 *
 * @param string $string A pipe-delimited string.
 * @return string Either $string or everything before the last pipe.
 */
function before_last_bar( $string ) {
	$last_bar = strrpos( $string, '|' );
	if ( false === $last_bar ) {
		return $string;
	} else {
		return substr( $string, 0, $last_bar );
	}
}

/**
 * Retrieve the translation of $text in the context defined in $context.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 * *Note:* Don't use translate_with_gettext_context() directly, use _x() or related functions.
 *
 *
 *
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 * @return string Translated text on success, original text on failure.
 */
function translate_with_gettext_context( $text, $context, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translation  = $translations->translate( $text, $context );

	/**
	 * Filters text with its translation based on context information.
	 *
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Text to translate.
	 * @param string $context     Context information for the translators.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( 'gettext_with_context', $translation, $text, $context, $domain );

	/**
	 * Filters text with its translation based on context information for a domain.
	 *
	 * The dynamic portion of the hook name, `$domain`, refers to the text domain.
	 *
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Text to translate.
	 * @param string $context     Context information for the translators.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( "gettext_with_context_{$domain}", $translation, $text, $context, $domain );

	return $translation;
}

/**
 * Retrieve the translation of $text.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 *
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string Translated text.
 */
function __( $text, $domain = 'default' ) {
	return translate( $text, $domain );
}

/**
 * Retrieve the translation of $text and escapes it for safe use in an attribute.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 *
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string Translated text on success, original text on failure.
 */
function esc_attr__( $text, $domain = 'default' ) {
	return esc_attr( translate( $text, $domain ) );
}

/**
 * Retrieve the translation of $text and escapes it for safe use in HTML output.
 *
 * If there is no translation, or the text domain isn't loaded, the original text
 * is escaped and returned.
 *
 *
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string Translated text.
 */
function esc_html__( $text, $domain = 'default' ) {
	return esc_html( translate( $text, $domain ) );
}

/**
 * Display translated text.
 *
 *
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 */
function _e( $text, $domain = 'default' ) {
	echo translate( $text, $domain );
}

/**
 * Display translated text that has been escaped for safe use in an attribute.
 *
 * Encodes `< > & " '` (less than, greater than, ampersand, double quote, single quote).
 * Will never double encode entities.
 *
 * If you need the value for use in PHP, use esc_attr__().
 *
 *
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 */
function esc_attr_e( $text, $domain = 'default' ) {
	echo esc_attr( translate( $text, $domain ) );
}

/**
 * Display translated text that has been escaped for safe use in HTML output.
 *
 * If there is no translation, or the text domain isn't loaded, the original text
 * is escaped and displayed.
 *
 * If you need the value for use in PHP, use esc_html__().
 *
 *
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 */
function esc_html_e( $text, $domain = 'default' ) {
	echo esc_html( translate( $text, $domain ) );
}

/**
 * Retrieve translated string with gettext context.
 *
 * Quite a few times, there will be collisions with similar translatable text
 * found in more than two places, but with different translated context.
 *
 * By including the context in the pot file, translators can translate the two
 * strings differently.
 *
 *
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 * @return string Translated context string without pipe.
 */
function _x( $text, $context, $domain = 'default' ) {
	return translate_with_gettext_context( $text, $context, $domain );
}

/**
 * Display translated string with gettext context.
 *
 *
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 */
function _ex( $text, $context, $domain = 'default' ) {
	echo _x( $text, $context, $domain );
}

/**
 * Translate string with gettext context, and escapes it for safe use in an attribute.
 *
 * If there is no translation, or the text domain isn't loaded, the original text
 * is escaped and returned.
 *
 *
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 * @return string Translated text.
 */
function esc_attr_x( $text, $context, $domain = 'default' ) {
	return esc_attr( translate_with_gettext_context( $text, $context, $domain ) );
}

/**
 * Translate string with gettext context, and escapes it for safe use in HTML output.
 *
 * If there is no translation, or the text domain isn't loaded, the original text
 * is escaped and returned.
 *
 *
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 * @return string Translated text.
 */
function esc_html_x( $text, $context, $domain = 'default' ) {
	return esc_html( translate_with_gettext_context( $text, $context, $domain ) );
}

/**
 * Translates and retrieves the singular or plural form based on the supplied number.
 *
 * Used when you want to use the appropriate form of a string based on whether a
 * number is singular or plural.
 *
 * Example:
 *
 *     printf( _n( '%s person', '%s people', $count, 'text-domain' ), number_format_i18n( $count ) );
 *
 *
 *
 *
 * @param string $single The text to be used if the number is singular.
 * @param string $plural The text to be used if the number is plural.
 * @param int    $number The number to compare against to use either the singular or plural form.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string The translated singular or plural form.
 */
function _n( $single, $plural, $number, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translation  = $translations->translate_plural( $single, $plural, $number );

	/**
	 * Filters the singular or plural form of a string.
	 *
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text to be used if the number is singular.
	 * @param string $plural      The text to be used if the number is plural.
	 * @param string $number      The number to compare against to use either the singular or plural form.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( 'ngettext', $translation, $single, $plural, $number, $domain );

	/**
	 * Filters the singular or plural form of a string for a domain.
	 *
	 * The dynamic portion of the hook name, `$domain`, refers to the text domain.
	 *
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text to be used if the number is singular.
	 * @param string $plural      The text to be used if the number is plural.
	 * @param string $number      The number to compare against to use either the singular or plural form.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( "ngettext_{$domain}", $translation, $single, $plural, $number, $domain );

	return $translation;
}

/**
 * Translates and retrieves the singular or plural form based on the supplied number, with gettext context.
 *
 * This is a hybrid of _n() and _x(). It supports context and plurals.
 *
 * Used when you want to use the appropriate form of a string with context based on whether a
 * number is singular or plural.
 *
 * Example of a generic phrase which is disambiguated via the context parameter:
 *
 *     printf( _nx( '%s group', '%s groups', $people, 'group of people', 'text-domain' ), number_format_i18n( $people ) );
 *     printf( _nx( '%s group', '%s groups', $animals, 'group of animals', 'text-domain' ), number_format_i18n( $animals ) );
 *
 *
 *
 *
 * @param string $single  The text to be used if the number is singular.
 * @param string $plural  The text to be used if the number is plural.
 * @param int    $number  The number to compare against to use either the singular or plural form.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 * @return string The translated singular or plural form.
 */
function _nx( $single, $plural, $number, $context, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translation  = $translations->translate_plural( $single, $plural, $number, $context );

	/**
	 * Filters the singular or plural form of a string with gettext context.
	 *
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text to be used if the number is singular.
	 * @param string $plural      The text to be used if the number is plural.
	 * @param string $number      The number to compare against to use either the singular or plural form.
	 * @param string $context     Context information for the translators.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( 'ngettext_with_context', $translation, $single, $plural, $number, $context, $domain );

	/**
	 * Filters the singular or plural form of a string with gettext context for a domain.
	 *
	 * The dynamic portion of the hook name, `$domain`, refers to the text domain.
	 *
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text to be used if the number is singular.
	 * @param string $plural      The text to be used if the number is plural.
	 * @param string $number      The number to compare against to use either the singular or plural form.
	 * @param string $context     Context information for the translators.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( "ngettext_with_context_{$domain}", $translation, $single, $plural, $number, $context, $domain );

	return $translation;
}

/**
 * Registers plural strings in POT file, but does not translate them.
 *
 * Used when you want to keep structures with translatable plural
 * strings and use them later when the number is known.
 *
 * Example:
 *
 *     $message = _n_noop( '%s post', '%s posts', 'text-domain' );
 *     ...
 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
 *
 *
 *
 * @param string $singular Singular form to be localized.
 * @param string $plural   Plural form to be localized.
 * @param string $domain   Optional. Text domain. Unique identifier for retrieving translated strings.
 *                         Default null.
 * @return array {
 *     Array of translation information for the strings.
 *
 *     @type string $0        Singular form to be localized. No longer used.
 *     @type string $1        Plural form to be localized. No longer used.
 *     @type string $singular Singular form to be localized.
 *     @type string $plural   Plural form to be localized.
 *     @type null   $context  Context information for the translators.
 *     @type string $domain   Text domain.
 * }
 */
function _n_noop( $singular, $plural, $domain = null ) {
	return array(
		0          => $singular,
		1          => $plural,
		'singular' => $singular,
		'plural'   => $plural,
		'context'  => null,
		'domain'   => $domain,
	);
}

/**
 * Registers plural strings with gettext context in POT file, but does not translate them.
 *
 * Used when you want to keep structures with translatable plural
 * strings and use them later when the number is known.
 *
 * Example of a generic phrase which is disambiguated via the context parameter:
 *
 *     $messages = array(
 *          'people'  => _nx_noop( '%s group', '%s groups', 'people', 'text-domain' ),
 *          'animals' => _nx_noop( '%s group', '%s groups', 'animals', 'text-domain' ),
 *     );
 *     ...
 *     $message = $messages[ $type ];
 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
 *
 *
 *
 * @param string $singular Singular form to be localized.
 * @param string $plural   Plural form to be localized.
 * @param string $context  Context information for the translators.
 * @param string $domain   Optional. Text domain. Unique identifier for retrieving translated strings.
 *                         Default null.
 * @return array {
 *     Array of translation information for the strings.
 *
 *     @type string      $0        Singular form to be localized. No longer used.
 *     @type string      $1        Plural form to be localized. No longer used.
 *     @type string      $2        Context information for the translators. No longer used.
 *     @type string      $singular Singular form to be localized.
 *     @type string      $plural   Plural form to be localized.
 *     @type string      $context  Context information for the translators.
 *     @type string|null $domain   Text domain.
 * }
 */
function _nx_noop( $singular, $plural, $context, $domain = null ) {
	return array(
		0          => $singular,
		1          => $plural,
		2          => $context,
		'singular' => $singular,
		'plural'   => $plural,
		'context'  => $context,
		'domain'   => $domain,
	);
}

/**
 * Translates and retrieves the singular or plural form of a string that's been registered
 * with _n_noop() or _nx_noop().
 *
 * Used when you want to use a translatable plural string once the number is known.
 *
 * Example:
 *
 *     $message = _n_noop( '%s post', '%s posts', 'text-domain' );
 *     ...
 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
 *
 *
 *
 * @param array  $nooped_plural Array with singular, plural, and context keys, usually the result of _n_noop() or _nx_noop().
 * @param int    $count         Number of objects.
 * @param string $domain        Optional. Text domain. Unique identifier for retrieving translated strings. If $nooped_plural contains
 *                              a text domain passed to _n_noop() or _nx_noop(), it will override this value. Default 'default'.
 * @return string Either $single or $plural translated text.
 */
function translate_nooped_plural( $nooped_plural, $count, $domain = 'default' ) {
	if ( $nooped_plural['domain'] ) {
		$domain = $nooped_plural['domain'];
	}

	if ( $nooped_plural['context'] ) {
		return _nx( $nooped_plural['singular'], $nooped_plural['plural'], $count, $nooped_plural['context'], $domain );
	} else {
		return _n( $nooped_plural['singular'], $nooped_plural['plural'], $count, $domain );
	}
}

/**
 * Load a .mo file into the text domain $domain.
 *
 * If the text domain already exists, the translations will be merged. If both
 * sets have the same string, the translation from the original value will be taken.
 *
 * On success, the .mo file will be placed in the $l10n global by $domain
 * and will be a MO object.
 *
 *
 *
 * @global MO[] $l10n          An array of all currently loaded text domains.
 * @global MO[] $l10n_unloaded An array of all text domains that have been unloaded again.
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string $mofile Path to the .mo file.
 * @return bool True on success, false on failure.
 */
function load_textdomain( $domain, $mofile ) {
	global $l10n, $l10n_unloaded;

	$l10n_unloaded = (array) $l10n_unloaded;

	/**
	 * Filters whether to override the .mo file loading.
	 *
	 *
	 * @param bool   $override Whether to override the .mo file loading. Default false.
	 * @param string $domain   Text domain. Unique identifier for retrieving translated strings.
	 * @param string $mofile   Path to the MO file.
	 */
	$plugin_override = apply_filters( 'override_load_textdomain', false, $domain, $mofile );

	if ( true === (bool) $plugin_override ) {
		unset( $l10n_unloaded[ $domain ] );

		return true;
	}

	/**
	 * Fires before the MO translation file is loaded.
	 *
	 *
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 * @param string $mofile Path to the .mo file.
	 */
	do_action( 'load_textdomain', $domain, $mofile );

	/**
	 * Filters MO file path for loading translations for a specific text domain.
	 *
	 *
	 * @param string $mofile Path to the MO file.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	$mofile = apply_filters( 'load_textdomain_mofile', $mofile, $domain );

	if ( ! is_readable( $mofile ) ) {
		return false;
	}

	$mo = new MO();
	if ( ! $mo->import_from_file( $mofile ) ) {
		return false;
	}

	if ( isset( $l10n[ $domain ] ) ) {
		$mo->merge_with( $l10n[ $domain ] );
	}

	unset( $l10n_unloaded[ $domain ] );

	$l10n[ $domain ] = &$mo;

	return true;
}

/**
 * Unload translations for a text domain.
 *
 *
 *
 * @global MO[] $l10n          An array of all currently loaded text domains.
 * @global MO[] $l10n_unloaded An array of all text domains that have been unloaded again.
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return bool Whether textdomain was unloaded.
 */
function unload_textdomain( $domain ) {
	global $l10n, $l10n_unloaded;

	$l10n_unloaded = (array) $l10n_unloaded;

	/**
	 * Filters whether to override the text domain unloading.
	 *
	 *
	 * @param bool   $override Whether to override the text domain unloading. Default false.
	 * @param string $domain   Text domain. Unique identifier for retrieving translated strings.
	 */
	$plugin_override = apply_filters( 'override_unload_textdomain', false, $domain );

	if ( $plugin_override ) {
		$l10n_unloaded[ $domain ] = true;

		return true;
	}

	/**
	 * Fires before the text domain is unloaded.
	 *
	 *
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	do_action( 'unload_textdomain', $domain );

	if ( isset( $l10n[ $domain ] ) ) {
		unset( $l10n[ $domain ] );

		$l10n_unloaded[ $domain ] = true;

		return true;
	}

	return false;
}

/**
 * Load default translated strings based on locale.
 *
 * Loads the .mo file in GC_LANG_DIR constant path from GeChiUI root.
 * The translated (.mo) file is named based on the locale.
 *
 * @see load_textdomain()
 *
 *
 *
 * @param string $locale Optional. Locale to load. Default is the value of get_locale().
 * @return bool Whether the textdomain was loaded.
 */
function load_default_textdomain( $locale = null ) {
	if ( null === $locale ) {
		$locale = determine_locale();
	}

	// Unload previously loaded strings so we can switch translations.
	unload_textdomain( 'default' );

	$return = load_textdomain( 'default', GC_LANG_DIR . "/$locale.mo" );

	if ( ( is_multisite() || ( defined( 'GC_INSTALLING_NETWORK' ) && GC_INSTALLING_NETWORK ) ) && ! file_exists( GC_LANG_DIR . "/admin-$locale.mo" ) ) {
		load_textdomain( 'default', GC_LANG_DIR . "/ms-$locale.mo" );
		return $return;
	}

	if ( is_admin() || gc_installing() || ( defined( 'GC_REPAIRING' ) && GC_REPAIRING ) ) {
		load_textdomain( 'default', GC_LANG_DIR . "/admin-$locale.mo" );
	}

	if ( is_network_admin() || ( defined( 'GC_INSTALLING_NETWORK' ) && GC_INSTALLING_NETWORK ) ) {
		load_textdomain( 'default', GC_LANG_DIR . "/admin-network-$locale.mo" );
	}

	return $return;
}

/**
 * Loads a plugin's translated strings.
 *
 * If the path is not given then it will be the root of the plugin directory.
 *
 * The .mo file should be named based on the text domain with a dash, and then the locale exactly.
 *
 *
 *
 *
 * @param string       $domain          Unique identifier for retrieving translated strings
 * @param string|false $deprecated      Optional. Deprecated. Use the $plugin_rel_path parameter instead.
 *                                      Default false.
 * @param string|false $plugin_rel_path Optional. Relative path to GC_PLUGIN_DIR where the .mo file resides.
 *                                      Default false.
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_plugin_textdomain( $domain, $deprecated = false, $plugin_rel_path = false ) {
	/**
	 * Filters a plugin's locale.
	 *
	 *
	 * @param string $locale The plugin's current locale.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	$locale = apply_filters( 'plugin_locale', determine_locale(), $domain );

	$mofile = $domain . '-' . $locale . '.mo';

	// Try to load from the languages directory first.
	if ( load_textdomain( $domain, GC_LANG_DIR . '/plugins/' . $mofile ) ) {
		return true;
	}

	if ( false !== $plugin_rel_path ) {
		$path = GC_PLUGIN_DIR . '/' . trim( $plugin_rel_path, '/' );
	} elseif ( false !== $deprecated ) {
		_deprecated_argument( __FUNCTION__, '2.7.0' );
		$path = ABSPATH . trim( $deprecated, '/' );
	} else {
		$path = GC_PLUGIN_DIR;
	}

	return load_textdomain( $domain, $path . '/' . $mofile );
}

/**
 * Load the translated strings for a plugin residing in the mu-plugins directory.
 *
 *
 *
 *
 * @param string $domain             Text domain. Unique identifier for retrieving translated strings.
 * @param string $mu_plugin_rel_path Optional. Relative to `GCMU_PLUGIN_DIR` directory in which the .mo
 *                                   file resides. Default empty string.
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_muplugin_textdomain( $domain, $mu_plugin_rel_path = '' ) {
	/** This filter is documented in gc-includes/l10n.php */
	$locale = apply_filters( 'plugin_locale', determine_locale(), $domain );

	$mofile = $domain . '-' . $locale . '.mo';

	// Try to load from the languages directory first.
	if ( load_textdomain( $domain, GC_LANG_DIR . '/plugins/' . $mofile ) ) {
		return true;
	}

	$path = GCMU_PLUGIN_DIR . '/' . ltrim( $mu_plugin_rel_path, '/' );

	return load_textdomain( $domain, $path . '/' . $mofile );
}

/**
 * Load the theme's translated strings.
 *
 * If the current locale exists as a .mo file in the theme's root directory, it
 * will be included in the translated strings by the $domain.
 *
 * The .mo files must be named based on the locale exactly.
 *
 *
 *
 *
 * @param string       $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string|false $path   Optional. Path to the directory containing the .mo file.
 *                             Default false.
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_theme_textdomain( $domain, $path = false ) {
	/**
	 * Filters a theme's locale.
	 *
	 *
	 * @param string $locale The theme's current locale.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	$locale = apply_filters( 'theme_locale', determine_locale(), $domain );

	$mofile = $domain . '-' . $locale . '.mo';

	// Try to load from the languages directory first.
	if ( load_textdomain( $domain, GC_LANG_DIR . '/themes/' . $mofile ) ) {
		return true;
	}

	if ( ! $path ) {
		$path = get_template_directory();
	}

	return load_textdomain( $domain, $path . '/' . $locale . '.mo' );
}

/**
 * Load the child themes translated strings.
 *
 * If the current locale exists as a .mo file in the child themes
 * root directory, it will be included in the translated strings by the $domain.
 *
 * The .mo files must be named based on the locale exactly.
 *
 *
 *
 * @param string       $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string|false $path   Optional. Path to the directory containing the .mo file.
 *                             Default false.
 * @return bool True when the theme textdomain is successfully loaded, false otherwise.
 */
function load_child_theme_textdomain( $domain, $path = false ) {
	if ( ! $path ) {
		$path = get_stylesheet_directory();
	}
	return load_theme_textdomain( $domain, $path );
}

/**
 * Loads the script translated strings.
 *
 *
 *
 *
 *
 * @see GC_Scripts::set_translations()
 *
 * @param string $handle Name of the script to register a translation domain to.
 * @param string $domain Optional. Text domain. Default 'default'.
 * @param string $path   Optional. The full file path to the directory containing translation files.
 * @return string|false The translated strings in JSON encoding on success,
 *                      false if the script textdomain could not be loaded.
 */
function load_script_textdomain( $handle, $domain = 'default', $path = null ) {
	$gc_scripts = gc_scripts();

	if ( ! isset( $gc_scripts->registered[ $handle ] ) ) {
		return false;
	}

	$path   = untrailingslashit( $path );
	$locale = determine_locale();

	// If a path was given and the handle file exists simply return it.
	$file_base       = 'default' === $domain ? $locale : $domain . '-' . $locale;
	$handle_filename = $file_base . '-' . $handle . '.json';

	if ( $path ) {
		$translations = load_script_translations( $path . '/' . $handle_filename, $handle, $domain );

		if ( $translations ) {
			return $translations;
		}
	}

	$src = $gc_scripts->registered[ $handle ]->src;

	if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $gc_scripts->content_url && 0 === strpos( $src, $gc_scripts->content_url ) ) ) {
		$src = $gc_scripts->base_url . $src;
	}

	$relative       = false;
	$languages_path = GC_LANG_DIR;

	$src_url     = gc_parse_url( $src );
	$content_url = gc_parse_url( content_url() );
	$plugins_url = gc_parse_url( plugins_url() );
	$site_url    = gc_parse_url( site_url() );

	// If the host is the same or it's a relative URL.
	if (
		( ! isset( $content_url['path'] ) || strpos( $src_url['path'], $content_url['path'] ) === 0 ) &&
		( ! isset( $src_url['host'] ) || ! isset( $content_url['host'] ) || $src_url['host'] === $content_url['host'] )
	) {
		// Make the src relative the specific plugin or theme.
		if ( isset( $content_url['path'] ) ) {
			$relative = substr( $src_url['path'], strlen( $content_url['path'] ) );
		} else {
			$relative = $src_url['path'];
		}
		$relative = trim( $relative, '/' );
		$relative = explode( '/', $relative );

		$languages_path = GC_LANG_DIR . '/' . $relative[0];

		$relative = array_slice( $relative, 2 ); // Remove plugins/<plugin name> or themes/<theme name>.
		$relative = implode( '/', $relative );
	} elseif (
		( ! isset( $plugins_url['path'] ) || strpos( $src_url['path'], $plugins_url['path'] ) === 0 ) &&
		( ! isset( $src_url['host'] ) || ! isset( $plugins_url['host'] ) || $src_url['host'] === $plugins_url['host'] )
	) {
		// Make the src relative the specific plugin.
		if ( isset( $plugins_url['path'] ) ) {
			$relative = substr( $src_url['path'], strlen( $plugins_url['path'] ) );
		} else {
			$relative = $src_url['path'];
		}
		$relative = trim( $relative, '/' );
		$relative = explode( '/', $relative );

		$languages_path = GC_LANG_DIR . '/plugins';

		$relative = array_slice( $relative, 1 ); // Remove <plugin name>.
		$relative = implode( '/', $relative );
	} elseif ( ! isset( $src_url['host'] ) || ! isset( $site_url['host'] ) || $src_url['host'] === $site_url['host'] ) {
		if ( ! isset( $site_url['path'] ) ) {
			$relative = trim( $src_url['path'], '/' );
		} elseif ( ( strpos( $src_url['path'], trailingslashit( $site_url['path'] ) ) === 0 ) ) {
			// Make the src relative to the GC root.
			$relative = substr( $src_url['path'], strlen( $site_url['path'] ) );
			$relative = trim( $relative, '/' );
		}
	}

	/**
	 * Filters the relative path of scripts used for finding translation files.
	 *
	 *
	 * @param string|false $relative The relative path of the script. False if it could not be determined.
	 * @param string       $src      The full source URL of the script.
	 */
	$relative = apply_filters( 'load_script_textdomain_relative_path', $relative, $src );

	// If the source is not from GC.
	if ( false === $relative ) {
		return load_script_translations( false, $handle, $domain );
	}

	// Translations are always based on the unminified filename.
	if ( substr( $relative, -7 ) === '.min.js' ) {
		$relative = substr( $relative, 0, -7 ) . '.js';
	}

	$md5_filename = $file_base . '-' . md5( $relative ) . '.json';

	if ( $path ) {
		$translations = load_script_translations( $path . '/' . $md5_filename, $handle, $domain );

		if ( $translations ) {
			return $translations;
		}
	}

	$translations = load_script_translations( $languages_path . '/' . $md5_filename, $handle, $domain );

	if ( $translations ) {
		return $translations;
	}

	return load_script_translations( false, $handle, $domain );
}

/**
 * Loads the translation data for the given script handle and text domain.
 *
 *
 *
 * @param string|false $file   Path to the translation file to load. False if there isn't one.
 * @param string       $handle Name of the script to register a translation domain to.
 * @param string       $domain The text domain.
 * @return string|false The JSON-encoded translated strings for the given script handle and text domain.
 *                      False if there are none.
 */
function load_script_translations( $file, $handle, $domain ) {
	/**
	 * Pre-filters script translations for the given file, script handle and text domain.
	 *
	 * Returning a non-null value allows to override the default logic, effectively short-circuiting the function.
	 *
	 *
	 * @param string|false|null $translations JSON-encoded translation data. Default null.
	 * @param string|false      $file         Path to the translation file to load. False if there isn't one.
	 * @param string            $handle       Name of the script to register a translation domain to.
	 * @param string            $domain       The text domain.
	 */
	$translations = apply_filters( 'pre_load_script_translations', null, $file, $handle, $domain );

	if ( null !== $translations ) {
		return $translations;
	}

	/**
	 * Filters the file path for loading script translations for the given script handle and text domain.
	 *
	 *
	 * @param string|false $file   Path to the translation file to load. False if there isn't one.
	 * @param string       $handle Name of the script to register a translation domain to.
	 * @param string       $domain The text domain.
	 */
	$file = apply_filters( 'load_script_translation_file', $file, $handle, $domain );

	if ( ! $file || ! is_readable( $file ) ) {
		return false;
	}

	$translations = file_get_contents( $file );

	/**
	 * Filters script translations for the given file, script handle and text domain.
	 *
	 *
	 * @param string $translations JSON-encoded translation data.
	 * @param string $file         Path to the translation file that was loaded.
	 * @param string $handle       Name of the script to register a translation domain to.
	 * @param string $domain       The text domain.
	 */
	return apply_filters( 'load_script_translations', $translations, $file, $handle, $domain );
}

/**
 * Loads plugin and theme textdomains just-in-time.
 *
 * When a textdomain is encountered for the first time, we try to load
 * the translation file from `gc-content/languages`, removing the need
 * to call load_plugin_texdomain() or load_theme_texdomain().
 *
 *
 * @access private
 *
 * @see get_translations_for_domain()
 * @global MO[] $l10n_unloaded An array of all text domains that have been unloaded again.
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return bool True when the textdomain is successfully loaded, false otherwise.
 */
function _load_textdomain_just_in_time( $domain ) {
	global $l10n_unloaded;

	$l10n_unloaded = (array) $l10n_unloaded;

	// Short-circuit if domain is 'default' which is reserved for core.
	if ( 'default' === $domain || isset( $l10n_unloaded[ $domain ] ) ) {
		return false;
	}

	$translation_path = _get_path_to_translation( $domain );
	if ( false === $translation_path ) {
		return false;
	}

	return load_textdomain( $domain, $translation_path );
}

/**
 * Gets the path to a translation file for loading a textdomain just in time.
 *
 * Caches the retrieved results internally.
 *
 *
 * @access private
 *
 * @see _load_textdomain_just_in_time()
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @param bool   $reset  Whether to reset the internal cache. Used by the switch to locale functionality.
 * @return string|false The path to the translation file or false if no translation file was found.
 */
function _get_path_to_translation( $domain, $reset = false ) {
	static $available_translations = array();

	if ( true === $reset ) {
		$available_translations = array();
	}

	if ( ! isset( $available_translations[ $domain ] ) ) {
		$available_translations[ $domain ] = _get_path_to_translation_from_lang_dir( $domain );
	}

	return $available_translations[ $domain ];
}

/**
 * Gets the path to a translation file in the languages directory for the current locale.
 *
 * Holds a cached list of available .mo files to improve performance.
 *
 *
 * @access private
 *
 * @see _get_path_to_translation()
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return string|false The path to the translation file or false if no translation file was found.
 */
function _get_path_to_translation_from_lang_dir( $domain ) {
	static $cached_mofiles = null;

	if ( null === $cached_mofiles ) {
		$cached_mofiles = array();

		$locations = array(
			GC_LANG_DIR . '/plugins',
			GC_LANG_DIR . '/themes',
		);

		foreach ( $locations as $location ) {
			$mofiles = glob( $location . '/*.mo' );
			if ( $mofiles ) {
				$cached_mofiles = array_merge( $cached_mofiles, $mofiles );
			}
		}
	}

	$locale = determine_locale();
	$mofile = "{$domain}-{$locale}.mo";

	$path = GC_LANG_DIR . '/plugins/' . $mofile;
	if ( in_array( $path, $cached_mofiles, true ) ) {
		return $path;
	}

	$path = GC_LANG_DIR . '/themes/' . $mofile;
	if ( in_array( $path, $cached_mofiles, true ) ) {
		return $path;
	}

	return false;
}

/**
 * Return the Translations instance for a text domain.
 *
 * If there isn't one, returns empty Translations instance.
 *
 *
 *
 * @global MO[] $l10n
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return Translations|NOOP_Translations A Translations instance.
 */
function get_translations_for_domain( $domain ) {
	global $l10n;
	if ( isset( $l10n[ $domain ] ) || ( _load_textdomain_just_in_time( $domain ) && isset( $l10n[ $domain ] ) ) ) {
		return $l10n[ $domain ];
	}

	static $noop_translations = null;
	if ( null === $noop_translations ) {
		$noop_translations = new NOOP_Translations;
	}

	return $noop_translations;
}

/**
 * Whether there are translations for the text domain.
 *
 *
 *
 * @global MO[] $l10n
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return bool Whether there are translations.
 */
function is_textdomain_loaded( $domain ) {
	global $l10n;
	return isset( $l10n[ $domain ] );
}

/**
 * Translates role name.
 *
 * Since the role names are in the database and not in the source there
 * are dummy gettext calls to get them into the POT file and this function
 * properly translates them back.
 *
 * The before_last_bar() call is needed, because older installations keep the roles
 * using the old context format: 'Role name|User role' and just skipping the
 * content after the last bar is easier than fixing them in the DB. New installations
 * won't suffer from that problem.
 *
 *
 *
 *
 * @param string $name   The role name.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string Translated role name on success, original name on failure.
 */
function translate_user_role( $name, $domain = 'default' ) {
	return translate_with_gettext_context( before_last_bar( $name ), 'User role', $domain );
}

/**
 * Get all available languages based on the presence of *.mo files in a given directory.
 *
 * The default directory is GC_LANG_DIR.
 *
 *
 *
 *
 * @param string $dir A directory to search for language files.
 *                    Default GC_LANG_DIR.
 * @return string[] An array of language codes or an empty array if no languages are present. Language codes are formed by stripping the .mo extension from the language file names.
 */
function get_available_languages( $dir = null ) {
	$languages = array();

	$lang_files = glob( ( is_null( $dir ) ? GC_LANG_DIR : $dir ) . '/*.mo' );
	if ( $lang_files ) {
		foreach ( $lang_files as $lang_file ) {
			$lang_file = basename( $lang_file, '.mo' );
			if ( 0 !== strpos( $lang_file, 'continents-cities' ) && 0 !== strpos( $lang_file, 'ms-' ) &&
				0 !== strpos( $lang_file, 'admin-' ) ) {
				$languages[] = $lang_file;
			}
		}
	}

	/**
	 * Filters the list of available language codes.
	 *
	 *
	 * @param string[] $languages An array of available language codes.
	 * @param string   $dir       The directory where the language files were found.
	 */
	return apply_filters( 'get_available_languages', $languages, $dir );
}

/**
 * Get installed translations.
 *
 * Looks in the gc-content/languages directory for translations of
 * plugins or themes.
 *
 *
 *
 * @param string $type What to search for. Accepts 'plugins', 'themes', 'core'.
 * @return array Array of language data.
 */
function gc_get_installed_translations( $type ) {
	if ( 'themes' !== $type && 'plugins' !== $type && 'core' !== $type ) {
		return array();
	}

	$dir = 'core' === $type ? '' : "/$type";

	if ( ! is_dir( GC_LANG_DIR ) ) {
		return array();
	}

	if ( $dir && ! is_dir( GC_LANG_DIR . $dir ) ) {
		return array();
	}

	$files = scandir( GC_LANG_DIR . $dir );
	if ( ! $files ) {
		return array();
	}

	$language_data = array();

	foreach ( $files as $file ) {
		if ( '.' === $file[0] || is_dir( GC_LANG_DIR . "$dir/$file" ) ) {
			continue;
		}
		if ( substr( $file, -3 ) !== '.po' ) {
			continue;
		}
		if ( ! preg_match( '/(?:(.+)-)?([a-z]{2,3}(?:_[A-Z]{2})?(?:_[a-z0-9]+)?).po/', $file, $match ) ) {
			continue;
		}
		if ( ! in_array( substr( $file, 0, -3 ) . '.mo', $files, true ) ) {
			continue;
		}

		list( , $textdomain, $language ) = $match;
		if ( '' === $textdomain ) {
			$textdomain = 'default';
		}
		$language_data[ $textdomain ][ $language ] = gc_get_pomo_file_data( GC_LANG_DIR . "$dir/$file" );
	}
	return $language_data;
}

/**
 * Extract headers from a PO file.
 *
 *
 *
 * @param string $po_file Path to PO file.
 * @return string[] Array of PO file header values keyed by header name.
 */
function gc_get_pomo_file_data( $po_file ) {
	$headers = get_file_data(
		$po_file,
		array(
			'POT-Creation-Date'  => '"POT-Creation-Date',
			'PO-Revision-Date'   => '"PO-Revision-Date',
			'Project-Id-Version' => '"Project-Id-Version',
			'X-Generator'        => '"X-Generator',
		)
	);
	foreach ( $headers as $header => $value ) {
		// Remove possible contextual '\n' and closing double quote.
		$headers[ $header ] = preg_replace( '~(\\\n)?"$~', '', $value );
	}
	return $headers;
}

/**
 * Language selector.
 *
 *
 *
 *
 *
 *
 *
 * @see get_available_languages()
 * @see gc_get_available_translations()
 *
 * @param string|array $args {
 *     Optional. Array or string of arguments for outputting the language selector.
 *
 *     @type string   $id                           ID attribute of the select element. Default 'locale'.
 *     @type string   $name                         Name attribute of the select element. Default 'locale'.
 *     @type array    $languages                    List of installed languages, contain only the locales.
 *                                                  Default empty array.
 *     @type array    $translations                 List of available translations. Default result of
 *                                                  gc_get_available_translations().
 *     @type string   $selected                     Language which should be selected. Default empty.
 *     @type bool|int $echo                         Whether to echo the generated markup. Accepts 0, 1, or their
 *                                                  boolean equivalents. Default 1.
 *     @type bool     $show_available_translations  Whether to show available translations. Default true.
 *     @type bool     $show_option_site_default     Whether to show an option to fall back to the site's locale. Default false.
 *     @type bool     $show_option_zh_cn            Whether to show an option for English (United States). Default true.
 *     @type bool     $explicit_option_zh_cn        Whether the English (United States) option uses an explicit value of zh_CN
 *                                                  instead of an empty value. Default false.
 * }
 * @return string HTML dropdown list of languages.
 */
function gc_dropdown_languages( $args = array() ) {

	$parsed_args = gc_parse_args(
		$args,
		array(
			'id'                          => 'locale',
			'name'                        => 'locale',
			'languages'                   => array(),
			'translations'                => array(),
			'selected'                    => '',
			'echo'                        => 1,
			'show_available_translations' => true,
			'show_option_site_default'    => false,
			'show_option_zh_cn'           => true,
			'explicit_option_zh_cn'       => false,
		)
	);

	// Bail if no ID or no name.
	if ( ! $parsed_args['id'] || ! $parsed_args['name'] ) {
		return;
	}

	// English (United States) uses an empty string for the value attribute.
	if ( 'zh_CN' === $parsed_args['selected'] && ! $parsed_args['explicit_option_zh_cn'] ) {
		$parsed_args['selected'] = '';
	}

	$translations = $parsed_args['translations'];
	if ( empty( $translations ) ) {
		require_once ABSPATH . 'gc-admin/includes/translation-install.php';
		$translations = gc_get_available_translations();
	}

	/*
	 * $parsed_args['languages'] should only contain the locales. Find the locale in
	 * $translations to get the native name. Fall back to locale.
	 */
	$languages = array();
	foreach ( $parsed_args['languages'] as $locale ) {
		if ( isset( $translations[ $locale ] ) ) {
			$translation = $translations[ $locale ];
			$languages[] = array(
				'language'    => $translation['language'],
				'native_name' => $translation['native_name'],
				'lang'        => current( $translation['iso'] ),
			);

			// Remove installed language from available translations.
			unset( $translations[ $locale ] );
		} else {
			$languages[] = array(
				'language'    => $locale,
				'native_name' => $locale,
				'lang'        => '',
			);
		}
	}

	$translations_available = ( ! empty( $translations ) && $parsed_args['show_available_translations'] );

	// Holds the HTML markup.
	$structure = array();

	// List installed languages.
	if ( $translations_available ) {
		$structure[] = '<optgroup label="' . esc_attr_x( '已安装', 'translations' ) . '">';
	}

	// Site default.
	if ( $parsed_args['show_option_site_default'] ) {
		$structure[] = sprintf(
			'<option value="site-default" data-installed="1"%s>%s</option>',
			selected( 'site-default', $parsed_args['selected'], false ),
			_x( '站点默认', 'default site language' )
		);
	}

	if ( $parsed_args['show_option_zh_cn'] ) {
		$value       = ( $parsed_args['explicit_option_zh_cn'] ) ? 'zh_CN' : '';
		$structure[] = sprintf(
			'<option value="%s" lang="zh" data-installed="1"%s>中文（简体）</option>',
			esc_attr( $value ),
			selected( '', $parsed_args['selected'], false )
		);
	}

	// List installed languages.
	foreach ( $languages as $language ) {
		$structure[] = sprintf(
			'<option value="%s" lang="%s"%s data-installed="1">%s</option>',
			esc_attr( $language['language'] ),
			esc_attr( $language['lang'] ),
			selected( $language['language'], $parsed_args['selected'], false ),
			esc_html( $language['native_name'] )
		);
	}
	if ( $translations_available ) {
		$structure[] = '</optgroup>';
	}

	// List available translations.
	if ( $translations_available ) {
		$structure[] = '<optgroup label="' . esc_attr_x( '可用', 'translations' ) . '">';
		foreach ( $translations as $translation ) {
			$structure[] = sprintf(
				'<option value="%s" lang="%s"%s>%s</option>',
				esc_attr( $translation['language'] ),
				esc_attr( current( $translation['iso'] ) ),
				selected( $translation['language'], $parsed_args['selected'], false ),
				esc_html( $translation['native_name'] )
			);
		}
		$structure[] = '</optgroup>';
	}

	// Combine the output string.
	$output  = sprintf( '<select name="%s" id="%s">', esc_attr( $parsed_args['name'] ), esc_attr( $parsed_args['id'] ) );
	$output .= implode( "\n", $structure );
	$output .= '</select>';

	if ( $parsed_args['echo'] ) {
		echo $output;
	}

	return $output;
}

/**
 * Determines whether the current locale is right-to-left (RTL).
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 *
 *
 * @global GC_Locale $gc_locale GeChiUI date and time locale object.
 *
 * @return bool Whether locale is RTL.
 */
function is_rtl() {
	global $gc_locale;
	if ( ! ( $gc_locale instanceof GC_Locale ) ) {
		return false;
	}
	return $gc_locale->is_rtl();
}

/**
 * Switches the translations according to the given locale.
 *
 *
 *
 * @global GC_Locale_Switcher $gc_locale_switcher GeChiUI locale switcher object.
 *
 * @param string $locale The locale.
 * @return bool True on success, false on failure.
 */
function switch_to_locale( $locale ) {
	/* @var GC_Locale_Switcher $gc_locale_switcher */
	global $gc_locale_switcher;

	return $gc_locale_switcher->switch_to_locale( $locale );
}

/**
 * Restores the translations according to the previous locale.
 *
 *
 *
 * @global GC_Locale_Switcher $gc_locale_switcher GeChiUI locale switcher object.
 *
 * @return string|false Locale on success, false on error.
 */
function restore_previous_locale() {
	/* @var GC_Locale_Switcher $gc_locale_switcher */
	global $gc_locale_switcher;

	return $gc_locale_switcher->restore_previous_locale();
}

/**
 * Restores the translations according to the original locale.
 *
 *
 *
 * @global GC_Locale_Switcher $gc_locale_switcher GeChiUI locale switcher object.
 *
 * @return string|false Locale on success, false on error.
 */
function restore_current_locale() {
	/* @var GC_Locale_Switcher $gc_locale_switcher */
	global $gc_locale_switcher;

	return $gc_locale_switcher->restore_current_locale();
}

/**
 * Whether switch_to_locale() is in effect.
 *
 *
 *
 * @global GC_Locale_Switcher $gc_locale_switcher GeChiUI locale switcher object.
 *
 * @return bool True if the locale has been switched, false otherwise.
 */
function is_locale_switched() {
	/* @var GC_Locale_Switcher $gc_locale_switcher */
	global $gc_locale_switcher;

	return $gc_locale_switcher->is_switched();
}

/**
 * Translates the provided settings value using its i18n schema.
 *
 *
 * @access private
 *
 * @param string|string[]|array[]|object $i18n_schema I18n schema for the setting.
 * @param string|string[]|array[]        $settings    Value for the settings.
 * @param string                         $textdomain  Textdomain to use with translations.
 *
 * @return string|string[]|array[] Translated settings.
 */
function translate_settings_using_i18n_schema( $i18n_schema, $settings, $textdomain ) {
	if ( empty( $i18n_schema ) || empty( $settings ) || empty( $textdomain ) ) {
		return $settings;
	}

	if ( is_string( $i18n_schema ) && is_string( $settings ) ) {
		return translate_with_gettext_context( $settings, $i18n_schema, $textdomain );
	}
	if ( is_array( $i18n_schema ) && is_array( $settings ) ) {
		$translated_settings = array();
		foreach ( $settings as $value ) {
			$translated_settings[] = translate_settings_using_i18n_schema( $i18n_schema[0], $value, $textdomain );
		}
		return $translated_settings;
	}
	if ( is_object( $i18n_schema ) && is_array( $settings ) ) {
		$group_key           = '*';
		$translated_settings = array();
		foreach ( $settings as $key => $value ) {
			if ( isset( $i18n_schema->$key ) ) {
				$translated_settings[ $key ] = translate_settings_using_i18n_schema( $i18n_schema->$key, $value, $textdomain );
			} elseif ( isset( $i18n_schema->$group_key ) ) {
				$translated_settings[ $key ] = translate_settings_using_i18n_schema( $i18n_schema->$group_key, $value, $textdomain );
			} else {
				$translated_settings[ $key ] = $value;
			}
		}
		return $translated_settings;
	}
	return $settings;
}
