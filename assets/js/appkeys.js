/**
 * @output assets/js/appkeys.js
 */

( function( $ ) {
	var $appKeySection = $( '#appkeys-section' ),
		$newAppKeyForm = $appKeySection.find( '.create-appkey' ),
		$newAppKeyField = $newAppKeyForm.find( '#new_appname' ),
		$newAppKeyButton = $newAppKeyForm.find( '#do_new_appkey' ),
		$appKeyTwrapper = $appKeySection.find( '.appkeys-list-table-wrapper' ),
		$appKeyTbody = $appKeySection.find( 'tbody' ),
		$appKeyTrNoItems = $appKeyTbody.find( '.no-items' ),
		$removeAllBtn = $( '#revoke-all-appkeys' ),
		tmplNewAppKey = gc.template( 'new-appkey' ),
		tmplAppKeyRow = gc.template( 'appkey-row' ),
		userId = $( '#user_id' ).val();

	$newAppKeyButton.on( 'click', function( e ) {
		e.preventDefault();

		if ( $newAppKeyButton.prop( 'aria-disabled' ) ) {
			return;
		}

		var name = $newAppKeyField.val();

		if ( 0 === name.length ) {
			$newAppKeyField.trigger( 'focus' );
			return;
		}

		clearNotices();
		$newAppKeyButton.prop( 'aria-disabled', true ).addClass( 'disabled' );

		var request = {
			name: name
		};

		/**
		 * Filters the request data used to create a new Application Password.
		 *
		 * @since 5.6.0
		 *
		 * @param {Object} request The request data.
		 * @param {number} userId  The id of the user the password is added for.
		 */
		request = gc.hooks.applyFilters( 'gc_appkeys_new_password_request', request, userId );

		gc.apiRequest( {
			path: '/gc/v2/users/' + userId + '/appkeys?_locale=user',
			method: 'POST',
			data: request
		} ).always( function() {
			$newAppKeyButton.removeProp( 'aria-disabled' ).removeClass( 'disabled' );
		} ).done( function( response ) {
			$newAppKeyField.val( '' );
			$newAppKeyButton.prop( 'disabled', false );

			$newAppKeyForm.after( tmplNewAppKey( {
				name: response.name,
				password: response.password
			} ) );
			$( '.new-appkey-notice' ).trigger( 'focus' );

			$appKeyTbody.prepend( tmplAppKeyRow( response ) );

			$appKeyTwrapper.show();
			$appKeyTrNoItems.remove();

			/**
			 * Fires after an application password has been successfully created.
			 *
			 * @since 5.6.0
			 *
			 * @param {Object} response The response data from the REST API.
			 * @param {Object} request  The request data used to create the password.
			 */
			gc.hooks.doAction( 'gc_appkeys_created_password', response, request );
		} ).fail( handleErrorResponse );
	} );

	$appKeyTbody.on( 'click', '.delete', function( e ) {
		e.preventDefault();

		if ( ! window.confirm( gc.i18n.__( '是否确定废除此密码？此操作无法撤消。' ) ) ) {
			return;
		}

		var $submitButton = $( this ),
			$tr = $submitButton.closest( 'tr' ),
			uuid = $tr.data( 'uuid' );

		clearNotices();
		$submitButton.prop( 'disabled', true );

		gc.apiRequest( {
			path: '/gc/v2/users/' + userId + '/appkeys/' + uuid + '?_locale=user',
			method: 'DELETE'
		} ).always( function() {
			$submitButton.prop( 'disabled', false );
		} ).done( function( response ) {
			if ( response.deleted ) {
				if ( 0 === $tr.siblings().length ) {
					$appKeyTwrapper.hide();
				}
				$tr.remove();

				addNotice( gc.i18n.__( '应用程序密码已废除。' ), 'success' ).trigger( 'focus' );
			}
		} ).fail( handleErrorResponse );
	} );

	$removeAllBtn.on( 'click', function( e ) {
		e.preventDefault();

		if ( ! window.confirm( gc.i18n.__( '您确定要撤销所有密码吗？此操作无法撤消。' ) ) ) {
			return;
		}

		var $submitButton = $( this );

		clearNotices();
		$submitButton.prop( 'disabled', true );

		gc.apiRequest( {
			path: '/gc/v2/users/' + userId + '/appkeys?_locale=user',
			method: 'DELETE'
		} ).always( function() {
			$submitButton.prop( 'disabled', false );
		} ).done( function( response ) {
			if ( response.deleted ) {
				$appKeyTbody.children().remove();
				$appKeySection.children( '.new-appkey' ).remove();
				$appKeyTwrapper.hide();

				addNotice( gc.i18n.__( '已撤销所有应用程序密码。' ), 'success' ).trigger( 'focus' );
			}
		} ).fail( handleErrorResponse );
	} );

	$appKeySection.on( 'click', '.notice-dismiss', function( e ) {
		e.preventDefault();
		var $el = $( this ).parent();
		$el.removeAttr( 'role' );
		$el.fadeTo( 100, 0, function () {
			$el.slideUp( 100, function () {
				$el.remove();
				$newAppKeyField.trigger( 'focus' );
			} );
		} );
	} );

	$newAppKeyField.on( 'keypress', function ( e ) {
		if ( 13 === e.which ) {
			e.preventDefault();
			$newAppKeyButton.trigger( 'click' );
		}
	} );

	// If there are no items, don't display the table yet.  If there are, show it.
	if ( 0 === $appKeyTbody.children( 'tr' ).not( $appKeyTrNoItems ).length ) {
		$appKeyTwrapper.hide();
	}

	/**
	 * Handles an error response from the REST API.
	 *
	 * @since 5.6.0
	 *
	 * @param {jqXHR} xhr The XHR object from the ajax call.
	 * @param {string} textStatus The string categorizing the ajax request's status.
	 * @param {string} errorThrown The HTTP status error text.
	 */
	function handleErrorResponse( xhr, textStatus, errorThrown ) {
		var errorMessage = errorThrown;

		if ( xhr.responseJSON && xhr.responseJSON.message ) {
			errorMessage = xhr.responseJSON.message;
		}

		addNotice( errorMessage, 'error' );
	}

	/**
	 * Displays a message in the Application Passwords section.
	 *
	 * @since 5.6.0
	 *
	 * @param {string} message The message to display.
	 * @param {string} type    The notice type. Either 'success' or 'error'.
	 * @returns {jQuery} The notice element.
	 */
	function addNotice( message, type ) {
		var $notice = $( '<div></div>' )
			.attr( 'role', 'alert' )
			.attr( 'tabindex', '-1' )
			.addClass( 'is-dismissible notice notice-' + type )
			.append( $( '<p></p>' ).text( message ) )
			.append(
				$( '<button></button>' )
					.attr( 'type', 'button' )
					.addClass( 'notice-dismiss' )
					.append( $( '<span></span>' ).addClass( 'screen-reader-text' ).text( gc.i18n.__( '忽略此通知。' ) ) )
			);

		$newAppKeyForm.after( $notice );

		return $notice;
	}

	/**
	 * Clears notice messages from the Application Passwords section.
	 *
	 * @since 5.6.0
	 */
	function clearNotices() {
		$( '.notice', $appKeySection ).remove();
	}
}( jQuery ) );
