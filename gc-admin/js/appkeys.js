/**
 * @output gc-admin/js/appkeys.js
 */

( function( $ ) {
	var $appPassSection = $( '#appkeys-section' ),
		$newAppPassForm = $appPassSection.find( '.create-appkey' ),
		$newAppPassField = $newAppPassForm.find( '.input' ),
		$newAppPassButton = $newAppPassForm.find( '.button' ),
		$appPassTwrapper = $appPassSection.find( '.appkeys-list-table-wrapper' ),
		$appPassTbody = $appPassSection.find( 'tbody' ),
		$appPassTrNoItems = $appPassTbody.find( '.no-items' ),
		$removeAllBtn = $( '#revoke-all-appkeys' ),
		tmplNewAppPass = gc.template( 'new-appkey' ),
		tmplAppPassRow = gc.template( 'appkey-row' ),
		userId = $( '#user_id' ).val();

	$newAppPassButton.on( 'click', function( e ) {
		e.preventDefault();

		if ( $newAppPassButton.prop( 'aria-disabled' ) ) {
			return;
		}

		var name = $newAppPassField.val();

		if ( 0 === name.length ) {
			$newAppPassField.trigger( 'focus' );
			return;
		}

		clearNotices();
		$newAppPassButton.prop( 'aria-disabled', true ).addClass( 'disabled' );

		var request = {
			name: name
		};

		/**
		 * Filters the request data used to create a new AppKey.
		 *
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
			$newAppPassButton.removeProp( 'aria-disabled' ).removeClass( 'disabled' );
		} ).done( function( response ) {
			$newAppPassField.val( '' );
			$newAppPassButton.prop( 'disabled', false );

			$newAppPassForm.after( tmplNewAppPass( {
				name: response.name,
				password: response.password
			} ) );
			$( '.new-appkey-notice' ).trigger( 'focus' );

			$appPassTbody.prepend( tmplAppPassRow( response ) );

			$appPassTwrapper.show();
			$appPassTrNoItems.remove();

			/**
			 * Fires after an appkey has been successfully created.
			 *
		
			 *
			 * @param {Object} response The response data from the REST API.
			 * @param {Object} request  The request data used to create the password.
			 */
			gc.hooks.doAction( 'gc_appkeys_created_password', response, request );
		} ).fail( handleErrorResponse );
	} );

	$appPassTbody.on( 'click', '.delete', function( e ) {
		e.preventDefault();

		if ( ! window.confirm( gc.i18n.__( '您确定要撤销这个密码吗？此操作无法撤消。' ) ) ) {
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
					$appPassTwrapper.hide();
				}
				$tr.remove();

				addNotice( gc.i18n.__( 'Appkey已撤消。' ), 'success' ).trigger( 'focus' );
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
				$appPassTbody.children().remove();
				$appPassSection.children( '.new-appkey' ).remove();
				$appPassTwrapper.hide();

				addNotice( gc.i18n.__( '所有Appkey已撤消。' ), 'success' ).trigger( 'focus' );
			}
		} ).fail( handleErrorResponse );
	} );

	$appPassSection.on( 'click', '.notice-dismiss', function( e ) {
		e.preventDefault();
		var $el = $( this ).parent();
		$el.removeAttr( 'role' );
		$el.fadeTo( 100, 0, function () {
			$el.slideUp( 100, function () {
				$el.remove();
				$newAppPassField.trigger( 'focus' );
			} );
		} );
	} );

	$newAppPassField.on( 'keypress', function ( e ) {
		if ( 13 === e.which ) {
			e.preventDefault();
			$newAppPassButton.trigger( 'click' );
		}
	} );

	// If there are no items, don't display the table yet.  If there are, show it.
	if ( 0 === $appPassTbody.children( 'tr' ).not( $appPassTrNoItems ).length ) {
		$appPassTwrapper.hide();
	}

	/**
	 * Handles an error response from the REST API.
	 *
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
	 * Displays a message in the AppKeys section.
	 *
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

		$newAppPassForm.after( $notice );

		return $notice;
	}

	/**
	 * Clears notice messages from the AppKeys section.
	 *
	 */
	function clearNotices() {
		$( '.notice', $appPassSection ).remove();
	}
}( jQuery ) );
