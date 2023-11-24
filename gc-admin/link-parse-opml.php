<?php
/**
 * Parse OPML XML files and store in globals.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * @global string $opml
 */
global $opml;

/**
 * XML callback function for the start of a new XML tag.
 *
 * @access private
 *
 * @global array $names
 * @global array $urls
 * @global array $targets
 * @global array $descriptions
 * @global array $feeds
 *
 * @param resource $parser   XML Parser resource.
 * @param string   $tag_name XML element name.
 * @param array    $attrs    XML element attributes.
 */
function startElement( $parser, $tag_name, $attrs ) { // phpcs:ignore GeChiUI.NamingConventions.ValidFunctionName.FunctionNameInvalid
	global $names, $urls, $targets, $descriptions, $feeds;

	if ( 'OUTLINE' === $tag_name ) {
		$name = '';
		if ( isset( $attrs['TEXT'] ) ) {
			$name = $attrs['TEXT'];
		}
		if ( isset( $attrs['TITLE'] ) ) {
			$name = $attrs['TITLE'];
		}
		$url = '';
		if ( isset( $attrs['URL'] ) ) {
			$url = $attrs['URL'];
		}
		if ( isset( $attrs['HTMLURL'] ) ) {
			$url = $attrs['HTMLURL'];
		}

		// Save the data away.
		$names[]        = $name;
		$urls[]         = $url;
		$targets[]      = isset( $attrs['TARGET'] ) ? $attrs['TARGET'] : '';
		$feeds[]        = isset( $attrs['XMLURL'] ) ? $attrs['XMLURL'] : '';
		$descriptions[] = isset( $attrs['DESCRIPTION'] ) ? $attrs['DESCRIPTION'] : '';
	} // End if outline.
}

/**
 * XML callback function that is called at the end of a XML tag.
 *
 * @access private
 *
 * @param resource $parser   XML Parser resource.
 * @param string   $tag_name XML tag name.
 */
function endElement( $parser, $tag_name ) { // phpcs:ignore GeChiUI.NamingConventions.ValidFunctionName.FunctionNameInvalid
	// Nothing to do.
}

// Create an XML parser.
if ( ! function_exists( 'xml_parser_create' ) ) {
	trigger_error( __( "PHP的XML扩展不可用。请联系您的主机提供商来启用PHP的XML扩展。" ) );
	gc_die( __( "PHP的XML扩展不可用。请联系您的主机提供商来启用PHP的XML扩展。" ) );
}

$xml_parser = xml_parser_create();

// Set the functions to handle opening and closing tags.
xml_set_element_handler( $xml_parser, 'startElement', 'endElement' );

if ( ! xml_parse( $xml_parser, $opml, true ) ) {
	printf(
		/* translators: 1: Error message, 2: Line number. */
		__( 'XML错误：%1$s于行%2$s' ),
		xml_error_string( xml_get_error_code( $xml_parser ) ),
		xml_get_current_line_number( $xml_parser )
	);
}

// Free up memory used by the XML parser.
xml_parser_free( $xml_parser );
unset( $xml_parser );
