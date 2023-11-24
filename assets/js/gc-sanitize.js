/**
 * @output assets/js/gc-sanitize.js
 */

( function () {

	window.gc = window.gc || {};

	/**
	 * gc.sanitize
	 *
	 * Helper functions to sanitize strings.
	 */
	gc.sanitize = {

		/**
		 * Strip HTML tags.
		 *
		 * @param {string} text Text to have the HTML tags striped out of.
		 *
		 * @return  Stripped text.
		 */
		stripTags: function( text ) {
			text = text || '';

			// Do the replacement.
			var _text = text
					.replace( /<!--[\s\S]*?(-->|$)/g, '' )
					.replace( /<(script|style)[^>]*>[\s\S]*?(<\/\1>|$)/ig, '' )
					.replace( /<\/?[a-z][\s\S]*?(>|$)/ig, '' );

			// If the initial text is not equal to the modified text,
			// do the search-replace again, until there is nothing to be replaced.
			if ( _text !== text ) {
				return gc.sanitize.stripTags( _text );
			}

			// Return the text with stripped tags.
			return _text;
		},

		/**
		 * Strip HTML tags and convert HTML entities.
		 *
		 * @param {string} text Text to strip tags and convert HTML entities.
		 *
		 * @return Sanitized text. False on failure.
		 */
		stripTagsAndEncodeText: function( text ) {
			var _text = gc.sanitize.stripTags( text ),
				textarea = document.createElement( 'textarea' );

			try {
				textarea.textContent = _text;
				_text = gc.sanitize.stripTags( textarea.value );
			} catch ( er ) {}

			return _text;
		}
	};
}() );
