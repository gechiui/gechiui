<?php
/**
 * GeChiUI Options Administration API.
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/**
 * Output JavaScript to toggle display of additional settings if avatars are disabled.
 *
 *
 */
function options_discussion_add_js() {
	?>
	<script>
	(function($){
		var parent = $( '#show_avatars' ),
			children = $( '.avatar-settings' );
		parent.on( 'change', function(){
			children.toggleClass( 'hide-if-js', ! this.checked );
		});
	})(jQuery);
	</script>
	<?php
}

/**
 * Display JavaScript on the page.
 *
 *
 */
function options_general_add_js() {
	?>
<script type="text/javascript">
	jQuery( function($) {
		var $siteName = $( '#gc-admin-bar-site-name' ).children( 'a' ).first(),
			homeURL = ( <?php echo gc_json_encode( get_home_url() ); ?> || '' ).replace( /^(https?:\/\/)?(www\.)?/, '' );

		$( '#blogname' ).on( 'input', function() {
			var title = $.trim( $( this ).val() ) || homeURL;

			// Truncate to 40 characters.
			if ( 40 < title.length ) {
				title = title.substring( 0, 40 ) + '\u2026';
			}

			$siteName.text( title );
		});

		$( 'input[name="date_format"]' ).on( 'click', function() {
			if ( 'date_format_custom_radio' !== $(this).attr( 'id' ) )
				$( 'input[name="date_format_custom"]' ).val( $( this ).val() ).closest( 'fieldset' ).find( '.example' ).text( $( this ).parent( 'label' ).children( '.format-i18n' ).text() );
		});

		$( 'input[name="date_format_custom"]' ).on( 'click input', function() {
			$( '#date_format_custom_radio' ).prop( 'checked', true );
		});

		$( 'input[name="time_format"]' ).on( 'click', function() {
			if ( 'time_format_custom_radio' !== $(this).attr( 'id' ) )
				$( 'input[name="time_format_custom"]' ).val( $( this ).val() ).closest( 'fieldset' ).find( '.example' ).text( $( this ).parent( 'label' ).children( '.format-i18n' ).text() );
		});

		$( 'input[name="time_format_custom"]' ).on( 'click input', function() {
			$( '#time_format_custom_radio' ).prop( 'checked', true );
		});

		$( 'input[name="date_format_custom"], input[name="time_format_custom"]' ).on( 'input', function() {
			var format = $( this ),
				fieldset = format.closest( 'fieldset' ),
				example = fieldset.find( '.example' ),
				spinner = fieldset.find( '.spinner' );

			// Debounce the event callback while users are typing.
			clearTimeout( $.data( this, 'timer' ) );
			$( this ).data( 'timer', setTimeout( function() {
				// If custom date is not empty.
				if ( format.val() ) {
					spinner.addClass( 'is-active' );

					$.post( ajaxurl, {
						action: 'date_format_custom' === format.attr( 'name' ) ? 'date_format' : 'time_format',
						date 	: format.val()
					}, function( d ) { spinner.removeClass( 'is-active' ); example.text( d ); } );
				}
			}, 500 ) );
		} );

		var languageSelect = $( '#GCLANG' );
		$( 'form' ).on( 'submit', function() {
			// Don't show a spinner for English and installed languages,
			// as there is nothing to download.
			if ( ! languageSelect.find( 'option:selected' ).data( 'installed' ) ) {
				$( '#submit', this ).after( '<span class="spinner language-install-spinner is-active" />' );
			}
		});
	} );
</script>
	<?php
}

/**
 * Display JavaScript on the page.
 *
 *
 */
function options_reading_add_js() {
	?>
<script type="text/javascript">
	jQuery( function($) {
		var section = $('#front-static-pages'),
			staticPage = section.find('input:radio[value="page"]'),
			selects = section.find('select'),
			check_disabled = function(){
				selects.prop( 'disabled', ! staticPage.prop('checked') );
			};
		check_disabled();
		section.find( 'input:radio' ).on( 'change', check_disabled );
	} );
</script>
	<?php
}

/**
 * Render the site charset setting.
 *
 *
 */
function options_reading_blog_charset() {
	echo '<input name="blog_charset" type="text" id="blog_charset" value="' . esc_attr( get_option( 'blog_charset' ) ) . '" class="regular-text" />';
	echo '<p class="描述">' . __( '您的站点的<a href="https://www.gechiui.com/support/glossary/#character-set">字符编码</a>（推荐使用UTF-8）' ) . '</p>';
}
