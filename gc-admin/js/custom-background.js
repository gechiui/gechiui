/**
 * @output gc-admin/js/custom-background.js
 */

/* global ajaxurl */

/**
 * Registers all events for customizing the background.
 *
 *
 *
 * @requires jQuery
 */
(function($) {
	$( function() {
		var frame,
			bgImage = $( '#custom-background-image' );

		/**
		 * Instantiates the GeChiUI color picker and binds the change and clear events.
		 *
		 *
		 * @return {void}
		 */
		$('#background-color').gcColorPicker({
			change: function( event, ui ) {
				bgImage.css('background-color', ui.color.toString());
			},
			clear: function() {
				bgImage.css('background-color', '');
			}
		});

		/**
		 * Alters the background size CSS property whenever the background size input has changed.
		 *
		 *
		 * @return {void}
		 */
		$( 'select[name="background-size"]' ).on( 'change', function() {
			bgImage.css( 'background-size', $( this ).val() );
		});

		/**
		 * Alters the background position CSS property whenever the background position input has changed.
		 *
		 *
		 * @return {void}
		 */
		$( 'input[name="background-position"]' ).on( 'change', function() {
			bgImage.css( 'background-position', $( this ).val() );
		});

		/**
		 * Alters the background repeat CSS property whenever the background repeat input has changed.
		 *
		 *
		 * @return {void}
		 */
		$( 'input[name="background-repeat"]' ).on( 'change',  function() {
			bgImage.css( 'background-repeat', $( this ).is( ':checked' ) ? 'repeat' : 'no-repeat' );
		});

		/**
		 * Alters the background attachment CSS property whenever the background attachment input has changed.
		 *
		 *
		 * @return {void}
		 */
		$( 'input[name="background-attachment"]' ).on( 'change', function() {
			bgImage.css( 'background-attachment', $( this ).is( ':checked' ) ? 'scroll' : 'fixed' );
		});

		/**
		 * Binds the event for opening the GC Media dialog.
		 *
		 *
		 * @return {void}
		 */
		$('#choose-from-library-link').on( 'click', function( event ) {
			var $el = $(this);

			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}

			// Create the media frame.
			frame = gc.media.frames.customBackground = gc.media({
				// Set the title of the modal.
				title: $el.data('choose'),

				// Tell the modal to show only images.
				library: {
					type: 'image'
				},

				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: $el.data('update'),
					/*
					 * Tell the button not to close the modal, since we're
					 * going to refresh the page when the image is selected.
					 */
					close: false
				}
			});

			/**
			 * When an image is selected, run a callback.
			 *
		
			 *
			 * @return {void}
 			 */
			frame.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = frame.state().get('selection').first();
				var nonceValue = $( '#_gcnonce' ).val() || '';

				// Run an Ajax request to set the background image.
				$.post( ajaxurl, {
					action: 'set-background-image',
					attachment_id: attachment.id,
					_ajax_nonce: nonceValue,
					size: 'full'
				}).done( function() {
					// When the request completes, reload the window.
					window.location.reload();
				});
			});

			// Finally, open the modal.
			frame.open();
		});
	});
})(jQuery);
