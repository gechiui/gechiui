<?php
/**
 * Block Serialization Parser
 *
 * @package GeChiUI
 */

/**
 * Class GC_Block_Parser_Block
 *
 * Holds the block structure in memory
 *
 * @since 5.0.0
 */
class GC_Block_Parser_Block {
	/**
	 * Name of block
	 *
	 * @example "core/paragraph"
	 *
	 * @since 5.0.0
	 * @var string
	 */
	public $blockName; // phpcs:ignore GeChiUI.NamingConventions.ValidVariableName

	/**
	 * Optional set of attributes from block comment delimiters
	 *
	 * @example null
	 * @example array( 'columns' => 3 )
	 *
	 * @since 5.0.0
	 * @var array|null
	 */
	public $attrs;

	/**
	 * List of inner blocks (of this same class)
	 *
	 * @since 5.0.0
	 * @var GC_Block_Parser_Block[]
	 */
	public $innerBlocks; // phpcs:ignore GeChiUI.NamingConventions.ValidVariableName

	/**
	 * Resultant HTML from inside block comment delimiters
	 * after removing inner blocks
	 *
	 * @example "...Just <!-- gc:test /--> testing..." -> "Just testing..."
	 *
	 * @since 5.0.0
	 * @var string
	 */
	public $innerHTML; // phpcs:ignore GeChiUI.NamingConventions.ValidVariableName

	/**
	 * List of string fragments and null markers where inner blocks were found
	 *
	 * @example array(
	 *   'innerHTML'    => 'BeforeInnerAfter',
	 *   'innerBlocks'  => array( block, block ),
	 *   'innerContent' => array( 'Before', null, 'Inner', null, 'After' ),
	 * )
	 *
	 * @since 4.2.0
	 * @var array
	 */
	public $innerContent; // phpcs:ignore GeChiUI.NamingConventions.ValidVariableName

	/**
	 * Constructor.
	 *
	 * Will populate object properties from the provided arguments.
	 *
	 * @since 5.0.0
	 *
	 * @param string $name          Name of block.
	 * @param array  $attrs         Optional set of attributes from block comment delimiters.
	 * @param array  $inner_blocks  List of inner blocks (of this same class).
	 * @param string $inner_html    Resultant HTML from inside block comment delimiters after removing inner blocks.
	 * @param array  $inner_content List of string fragments and null markers where inner blocks were found.
	 */
	public function __construct( $name, $attrs, $inner_blocks, $inner_html, $inner_content ) {
		$this->blockName    = $name;          // phpcs:ignore GeChiUI.NamingConventions.ValidVariableName
		$this->attrs        = $attrs;
		$this->innerBlocks  = $inner_blocks;  // phpcs:ignore GeChiUI.NamingConventions.ValidVariableName
		$this->innerHTML    = $inner_html;    // phpcs:ignore GeChiUI.NamingConventions.ValidVariableName
		$this->innerContent = $inner_content; // phpcs:ignore GeChiUI.NamingConventions.ValidVariableName
	}
}
