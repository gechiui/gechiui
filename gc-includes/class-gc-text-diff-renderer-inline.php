<?php
/**
 * Diff API: GC_Text_Diff_Renderer_inline class
 *
 * @package GeChiUI
 * @subpackage Diff
 */

/**
 * Better word splitting than the PEAR package provides.
 *
 * @uses Text_Diff_Renderer_inline Extends
 */
#[AllowDynamicProperties]
class GC_Text_Diff_Renderer_inline extends Text_Diff_Renderer_inline {

	/**
	 * @ignore
	 * @since 2.6.0
	 *
	 * @param string $string
	 * @param string $newlineEscape
	 * @return string
	 */
	public function _splitOnWords( $string, $newlineEscape = "\n" ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.stringFound
		$string = str_replace( "\0", '', $string );
		$words  = preg_split( '/([^\w])/u', $string, -1, PREG_SPLIT_DELIM_CAPTURE );
		$words  = str_replace( "\n", $newlineEscape, $words );
		return $words;
	}

}
