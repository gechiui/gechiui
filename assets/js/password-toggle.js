/**
 * Adds functionality for password visibility buttons to toggle between text and password input types.
 *
 * @since 6.3.0
 * @output assets/js/password-toggle.js
 */

( function () {
	var toggleElements, status, input, icon, label, __ = gc.i18n.__;

	toggleElements = document.querySelectorAll( '.pwd-toggle' );

	toggleElements.forEach( function (toggle) {
		toggle.classList.remove( 'hide-if-no-js' );
		toggle.addEventListener( 'click', togglePassword );
	} );

	function togglePassword() {
		status = this.getAttribute( 'data-toggle' );
		input = this.parentElement.children.namedItem( 'pwd' );
		icon = this.getElementsByClassName( 'dashicons' )[ 0 ];
		label = this.getElementsByClassName( 'text' )[ 0 ];

		if ( 0 === parseInt( status, 10 ) ) {
			this.setAttribute( 'data-toggle', 1 );
			this.setAttribute( 'aria-label', __( '隐藏密码' ) );
			input.setAttribute( 'type', 'text' );
			label.innerHTML = __( '隐藏' );
			icon.classList.remove( 'dashicons-visibility' );
			icon.classList.add( 'dashicons-hidden' );
		} else {
			this.setAttribute( 'data-toggle', 0 );
			this.setAttribute( 'aria-label', __( '显示密码' ) );
			input.setAttribute( 'type', 'password' );
			label.innerHTML = __( '显示' );
			icon.classList.remove( 'dashicons-hidden' );
			icon.classList.add( 'dashicons-visibility' );
		}
	}
} )();
