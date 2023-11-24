<?php
/**
 * Server-side rendering of the `core/archives` block.
 *
 * @package GeChiUI
 */

/**
 * Renders the `core/archives` block on server.
 *
 * @see GC_Widget_Archives
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the post content with archives added.
 */
function render_block_core_archives( $attributes ) {
	$show_post_count = ! empty( $attributes['showPostCounts'] );
	$type            = isset( $attributes['type'] ) ? $attributes['type'] : 'monthly';

	$class = 'gc-block-archives-list';

	if ( ! empty( $attributes['displayAsDropdown'] ) ) {

		$class = 'gc-block-archives-dropdown';

		$dropdown_id = gc_unique_id( 'gc-block-archives-' );
		$title       = __( '归档' );

		/** This filter is documented in gc-includes/widgets/class-gc-widget-archives.php */
		$dropdown_args = apply_filters(
			'widget_archives_dropdown_args',
			array(
				'type'            => $type,
				'format'          => 'option',
				'show_post_count' => $show_post_count,
			)
		);

		$dropdown_args['echo'] = 0;

		$archives = gc_get_archives( $dropdown_args );

		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $class ) );

		switch ( $dropdown_args['type'] ) {
			case 'yearly':
				$label = __( '选择年份' );
				break;
			case 'monthly':
				$label = __( '选择月份' );
				break;
			case 'daily':
				$label = __( '选择日期' );
				break;
			case 'weekly':
				$label = __( '选择周次' );
				break;
			default:
				$label = __( '选择文章' );
				break;
		}

		$show_label = empty( $attributes['showLabel'] ) ? ' screen-reader-text' : '';

		$block_content = '<label for="' . $dropdown_id . '" class="gc-block-archives__label' . $show_label . '">' . esc_html( $title ) . '</label>
		<select id="' . $dropdown_id . '" name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">
		<option value="">' . esc_html( $label ) . '</option>' . $archives . '</select>';

		return sprintf(
			'<div %1$s>%2$s</div>',
			$wrapper_attributes,
			$block_content
		);
	}

	/** This filter is documented in gc-includes/widgets/class-gc-widget-archives.php */
	$archives_args = apply_filters(
		'widget_archives_args',
		array(
			'type'            => $type,
			'show_post_count' => $show_post_count,
		)
	);

	$archives_args['echo'] = 0;

	$archives = gc_get_archives( $archives_args );

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $class ) );

	if ( empty( $archives ) ) {
		return sprintf(
			'<div %1$s>%2$s</div>',
			$wrapper_attributes,
			__( '没有归档可显示。' )
		);
	}

	return sprintf(
		'<ul %1$s>%2$s</ul>',
		$wrapper_attributes,
		$archives
	);
}

/**
 * Register archives block.
 */
function register_block_core_archives() {
	register_block_type_from_metadata(
		__DIR__ . '/archives',
		array(
			'render_callback' => 'render_block_core_archives',
		)
	);
}
add_action( 'init', 'register_block_core_archives' );
