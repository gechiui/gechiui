/**
 * @file Contains all dynamic functionality needed on post and term pages.
 *
 * @output gc-admin/js/post.js
 */

 /* global ajaxurl, gcAjax, postboxes, pagenow, tinymce, alert, deleteUserSetting, ClipboardJS */
 /* global theList:true, theExtraList:true, getUserSetting, setUserSetting, commentReply, commentsBox */
 /* global GCSetThumbnailHTML, gctitlehint */

// Backward compatibility: prevent fatal errors.
window.makeSlugeditClickable = window.editPermalink = function(){};

// Make sure the gc object exists.
window.gc = window.gc || {};

( function( $ ) {
	var titleHasFocus = false,
		__ = gc.i18n.__;

	/**
	 * Control loading of comments on the post and term edit pages.
	 *
	 * @type {{st: number, get: commentsBox.get, load: commentsBox.load}}
	 *
	 * @namespace commentsBox
	 */
	window.commentsBox = {
		// Comment offset to use when fetching new comments.
		st : 0,

		/**
		 * Fetch comments using Ajax and display them in the box.
		 *
		 * @memberof commentsBox
		 *
		 * @param {number} total Total number of comments for this post.
		 * @param {number} num   Optional. Number of comments to fetch, defaults to 20.
		 * @return {boolean} Always returns false.
		 */
		get : function(total, num) {
			var st = this.st, data;
			if ( ! num )
				num = 20;

			this.st += num;
			this.total = total;
			$( '#commentsdiv .spinner' ).addClass( 'is-active' );

			data = {
				'action' : 'get-comments',
				'mode' : 'single',
				'_ajax_nonce' : $('#add_comment_nonce').val(),
				'p' : $('#post_ID').val(),
				'start' : st,
				'number' : num
			};

			$.post(
				ajaxurl,
				data,
				function(r) {
					r = gcAjax.parseAjaxResponse(r);
					$('#commentsdiv .widefat').show();
					$( '#commentsdiv .spinner' ).removeClass( 'is-active' );

					if ( 'object' == typeof r && r.responses[0] ) {
						$('#the-comment-list').append( r.responses[0].data );

						theList = theExtraList = null;
						$( 'a[className*=\':\']' ).off();

						// If the offset is over the total number of comments we cannot fetch any more, so hide the button.
						if ( commentsBox.st > commentsBox.total )
							$('#show-comments').hide();
						else
							$('#show-comments').show().children('a').text( __( '显示更多评论' ) );

						return;
					} else if ( 1 == r ) {
						$('#show-comments').text( __( '已无更多评论。' ) );
						return;
					}

					$('#the-comment-list').append('<tr><td colspan="2">'+gcAjax.broken+'</td></tr>');
				}
			);

			return false;
		},

		/**
		 * Load the next batch of comments.
		 *
		 * @memberof commentsBox
		 *
		 * @param {number} total Total number of comments to load.
		 */
		load: function(total){
			this.st = jQuery('#the-comment-list tr.comment:visible').length;
			this.get(total);
		}
	};

	/**
	 * Overwrite the content of the Featured Image postbox
	 *
	 * @param {string} html New HTML to be displayed in the content area of the postbox.
	 *
	 * @global
	 */
	window.GCSetThumbnailHTML = function(html){
		$('.inside', '#postimagediv').html(html);
	};

	/**
	 * Set the Image ID of the Featured Image
	 *
	 * @param {number} id The post_id of the image to use as Featured Image.
	 *
	 * @global
	 */
	window.GCSetThumbnailID = function(id){
		var field = $('input[value="_thumbnail_id"]', '#list-table');
		if ( field.length > 0 ) {
			$('#meta\\[' + field.attr('id').match(/[0-9]+/) + '\\]\\[value\\]').text(id);
		}
	};

	/**
	 * Remove the Featured Image
	 *
	 * @param {string} nonce Nonce to use in the request.
	 *
	 * @global
	 */
	window.GCRemoveThumbnail = function(nonce){
		$.post(
			ajaxurl, {
				action: 'set-post-thumbnail',
				post_id: $( '#post_ID' ).val(),
				thumbnail_id: -1,
				_ajax_nonce: nonce,
				cookie: encodeURIComponent( document.cookie )
			},
			/**
			 * Handle server response
			 *
			 * @param {string} str Response, will be '0' when an error occurred otherwise contains link to add Featured Image.
			 */
			function(str){
				if ( str == '0' ) {
					alert( __( '无法设置为缩略图。请尝试其他附件。' ) );
				} else {
					GCSetThumbnailHTML(str);
				}
			}
		);
	};

	/**
	 * Heartbeat locks.
	 *
	 * Used to lock editing of an object by only one user at a time.
	 *
	 * When the user does not send a heartbeat in a heartbeat-time
	 * the user is no longer editing and another user can start editing.
	 */
	$(document).on( 'heartbeat-send.refresh-lock', function( e, data ) {
		var lock = $('#active_post_lock').val(),
			post_id = $('#post_ID').val(),
			send = {};

		if ( ! post_id || ! $('#post-lock-dialog').length )
			return;

		send.post_id = post_id;

		if ( lock )
			send.lock = lock;

		data['gc-refresh-post-lock'] = send;

	}).on( 'heartbeat-tick.refresh-lock', function( e, data ) {
		// Post locks: update the lock string or show the dialog if somebody has taken over editing.
		var received, wrap, avatar;

		if ( data['gc-refresh-post-lock'] ) {
			received = data['gc-refresh-post-lock'];

			if ( received.lock_error ) {
				// Show "editing taken over" message.
				wrap = $('#post-lock-dialog');

				if ( wrap.length && ! wrap.is(':visible') ) {
					if ( gc.autosave ) {
						// Save the latest changes and disable.
						$(document).one( 'heartbeat-tick', function() {
							gc.autosave.server.suspend();
							wrap.removeClass('saving').addClass('saved');
							$(window).off( 'beforeunload.edit-post' );
						});

						wrap.addClass('saving');
						gc.autosave.server.triggerSave();
					}

					if ( received.lock_error.avatar_src ) {
						avatar = $( '<img />', {
							'class': 'avatar avatar-64 photo',
							width: 64,
							height: 64,
							alt: '',
							src: received.lock_error.avatar_src,
							srcset: received.lock_error.avatar_src_2x ?
								received.lock_error.avatar_src_2x + ' 2x' :
								undefined
						} );
						wrap.find('div.post-locked-avatar').empty().append( avatar );
					}

					wrap.show().find('.currently-editing').text( received.lock_error.text );
					wrap.find('.gc-tab-first').trigger( 'focus' );
				}
			} else if ( received.new_lock ) {
				$('#active_post_lock').val( received.new_lock );
			}
		}
	}).on( 'before-autosave.update-post-slug', function() {
		titleHasFocus = document.activeElement && document.activeElement.id === 'title';
	}).on( 'after-autosave.update-post-slug', function() {

		/*
		 * Create slug area only if not already there
		 * and the title field was not focused (user was not typing a title) when autosave ran.
		 */
		if ( ! $('#edit-slug-box > *').length && ! titleHasFocus ) {
			$.post( ajaxurl, {
					action: 'sample-permalink',
					post_id: $('#post_ID').val(),
					new_title: $('#title').val(),
					samplepermalinknonce: $('#samplepermalinknonce').val()
				},
				function( data ) {
					if ( data != '-1' ) {
						$('#edit-slug-box').html(data);
					}
				}
			);
		}
	});

}(jQuery));

/**
 * Heartbeat refresh nonces.
 */
(function($) {
	var check, timeout;

	/**
	 * Only allow to check for nonce refresh every 30 seconds.
	 */
	function schedule() {
		check = false;
		window.clearTimeout( timeout );
		timeout = window.setTimeout( function(){ check = true; }, 300000 );
	}

	$( function() {
		schedule();
	}).on( 'heartbeat-send.gc-refresh-nonces', function( e, data ) {
		var post_id,
			$authCheck = $('#gc-auth-check-wrap');

		if ( check || ( $authCheck.length && ! $authCheck.hasClass( 'hidden' ) ) ) {
			if ( ( post_id = $('#post_ID').val() ) && $('#_gcnonce').val() ) {
				data['gc-refresh-post-nonces'] = {
					post_id: post_id
				};
			}
		}
	}).on( 'heartbeat-tick.gc-refresh-nonces', function( e, data ) {
		var nonces = data['gc-refresh-post-nonces'];

		if ( nonces ) {
			schedule();

			if ( nonces.replace ) {
				$.each( nonces.replace, function( selector, value ) {
					$( '#' + selector ).val( value );
				});
			}

			if ( nonces.heartbeatNonce )
				window.heartbeatSettings.nonce = nonces.heartbeatNonce;
		}
	});
}(jQuery));

/**
 * All post and postbox controls and functionality.
 */
jQuery( function($) {
	var stamp, visibility, $submitButtons, updateVisibility, updateText,
		$textarea = $('#content'),
		$document = $(document),
		postId = $('#post_ID').val() || 0,
		$submitpost = $('#submitpost'),
		releaseLock = true,
		$postVisibilitySelect = $('#post-visibility-select'),
		$timestampdiv = $('#timestampdiv'),
		$postStatusSelect = $('#post-status-select'),
		isMac = window.navigator.platform ? window.navigator.platform.indexOf( 'Mac' ) !== -1 : false,
		copyAttachmentURLClipboard = new ClipboardJS( '.copy-attachment-url.edit-media' ),
		copyAttachmentURLSuccessTimeout,
		__ = gc.i18n.__, _x = gc.i18n._x;

	postboxes.add_postbox_toggles(pagenow);

	/*
	 * Clear the window name. Otherwise if this is a former preview window where the user navigated to edit another post,
	 * and the first post is still being edited, clicking Preview there will use this window to show the preview.
	 */
	window.name = '';

	// Post locks: contain focus inside the dialog. If the dialog is shown, focus the first item.
	$('#post-lock-dialog .notification-dialog').on( 'keydown', function(e) {
		// Don't do anything when [Tab] is pressed.
		if ( e.which != 9 )
			return;

		var target = $(e.target);

		// [Shift] + [Tab] on first tab cycles back to last tab.
		if ( target.hasClass('gc-tab-first') && e.shiftKey ) {
			$(this).find('.gc-tab-last').trigger( 'focus' );
			e.preventDefault();
		// [Tab] on last tab cycles back to first tab.
		} else if ( target.hasClass('gc-tab-last') && ! e.shiftKey ) {
			$(this).find('.gc-tab-first').trigger( 'focus' );
			e.preventDefault();
		}
	}).filter(':visible').find('.gc-tab-first').trigger( 'focus' );

	// Set the heartbeat interval to 15 seconds if post lock dialogs are enabled.
	if ( gc.heartbeat && $('#post-lock-dialog').length ) {
		gc.heartbeat.interval( 15 );
	}

	// The form is being submitted by the user.
	$submitButtons = $submitpost.find( ':submit, a.submitdelete, #post-preview' ).on( 'click.edit-post', function( event ) {
		var $button = $(this);

		if ( $button.hasClass('disabled') ) {
			event.preventDefault();
			return;
		}

		if ( $button.hasClass('submitdelete') || $button.is( '#post-preview' ) ) {
			return;
		}

		// The form submission can be blocked from JS or by using HTML 5.0 validation on some fields.
		// Run this only on an actual 'submit'.
		$('form#post').off( 'submit.edit-post' ).on( 'submit.edit-post', function( event ) {
			if ( event.isDefaultPrevented() ) {
				return;
			}

			// Stop auto save.
			if ( gc.autosave ) {
				gc.autosave.server.suspend();
			}

			if ( typeof commentReply !== 'undefined' ) {
				/*
				 * Warn the user they have an unsaved comment before submitting
				 * the post data for update.
				 */
				if ( ! commentReply.discardCommentChanges() ) {
					return false;
				}

				/*
				 * Close the comment edit/reply form if open to stop the form
				 * action from interfering with the post's form action.
				 */
				commentReply.close();
			}

			releaseLock = false;
			$(window).off( 'beforeunload.edit-post' );

			$submitButtons.addClass( 'disabled' );

			if ( $button.attr('id') === 'publish' ) {
				$submitpost.find( '#major-publishing-actions .spinner' ).addClass( 'is-active' );
			} else {
				$submitpost.find( '#minor-publishing .spinner' ).addClass( 'is-active' );
			}
		});
	});

	// Submit the form saving a draft or an autosave, and show a preview in a new tab.
	$('#post-preview').on( 'click.post-preview', function( event ) {
		var $this = $(this),
			$form = $('form#post'),
			$previewField = $('input#gc-preview'),
			target = $this.attr('target') || 'gc-preview',
			ua = navigator.userAgent.toLowerCase();

		event.preventDefault();

		if ( $this.hasClass('disabled') ) {
			return;
		}

		if ( gc.autosave ) {
			gc.autosave.server.tempBlockSave();
		}

		$previewField.val('dopreview');
		$form.attr( 'target', target ).trigger( 'submit' ).attr( 'target', '' );

		// Workaround for WebKit bug preventing a form submitting twice to the same action.
		// https://bugs.webkit.org/show_bug.cgi?id=28633
		if ( ua.indexOf('safari') !== -1 && ua.indexOf('chrome') === -1 ) {
			$form.attr( 'action', function( index, value ) {
				return value + '?t=' + ( new Date() ).getTime();
			});
		}

		$previewField.val('');
	});

	// This code is meant to allow tabbing from Title to Post content.
	$('#title').on( 'keydown.editor-focus', function( event ) {
		var editor;

		if ( event.keyCode === 9 && ! event.ctrlKey && ! event.altKey && ! event.shiftKey ) {
			editor = typeof tinymce != 'undefined' && tinymce.get('content');

			if ( editor && ! editor.isHidden() ) {
				editor.focus();
			} else if ( $textarea.length ) {
				$textarea.trigger( 'focus' );
			} else {
				return;
			}

			event.preventDefault();
		}
	});

	// Auto save new posts after a title is typed.
	if ( $( '#auto_draft' ).val() ) {
		$( '#title' ).on( 'blur', function() {
			var cancel;

			if ( ! this.value || $('#edit-slug-box > *').length ) {
				return;
			}

			// Cancel the auto save when the blur was triggered by the user submitting the form.
			$('form#post').one( 'submit', function() {
				cancel = true;
			});

			window.setTimeout( function() {
				if ( ! cancel && gc.autosave ) {
					gc.autosave.server.triggerSave();
				}
			}, 200 );
		});
	}

	$document.on( 'autosave-disable-buttons.edit-post', function() {
		$submitButtons.addClass( 'disabled' );
	}).on( 'autosave-enable-buttons.edit-post', function() {
		if ( ! gc.heartbeat || ! gc.heartbeat.hasConnectionError() ) {
			$submitButtons.removeClass( 'disabled' );
		}
	}).on( 'before-autosave.edit-post', function() {
		$( '.autosave-message' ).text( __( '草稿保存中…' ) );
	}).on( 'after-autosave.edit-post', function( event, data ) {
		$( '.autosave-message' ).text( data.message );

		if ( $( document.body ).hasClass( 'post-new-php' ) ) {
			$( '.submitbox .submitdelete' ).show();
		}
	});

	/*
	 * When the user is trying to load another page, or reloads current page
	 * show a confirmation dialog when there are unsaved changes.
	 */
	$( window ).on( 'beforeunload.edit-post', function( event ) {
		var editor  = window.tinymce && window.tinymce.get( 'content' );
		var changed = false;

		if ( gc.autosave ) {
			changed = gc.autosave.server.postChanged();
		} else if ( editor ) {
			changed = ( ! editor.isHidden() && editor.isDirty() );
		}

		if ( changed ) {
			event.preventDefault();
			// The return string is needed for browser compat.
			// See https://developer.mozilla.org/en-US/docs/Web/API/Window/beforeunload_event.
			return __( '离开这个页面，您所做的更改将丢失。' );
		}
	}).on( 'unload.edit-post', function( event ) {
		if ( ! releaseLock ) {
			return;
		}

		/*
		 * Unload is triggered (by hand) on removing the Thickbox iframe.
		 * Make sure we process only the main document unload.
		 */
		if ( event.target && event.target.nodeName != '#document' ) {
			return;
		}

		var postID = $('#post_ID').val();
		var postLock = $('#active_post_lock').val();

		if ( ! postID || ! postLock ) {
			return;
		}

		var data = {
			action: 'gc-remove-post-lock',
			_gcnonce: $('#_gcnonce').val(),
			post_ID: postID,
			active_post_lock: postLock
		};

		if ( window.FormData && window.navigator.sendBeacon ) {
			var formData = new window.FormData();

			$.each( data, function( key, value ) {
				formData.append( key, value );
			});

			if ( window.navigator.sendBeacon( ajaxurl, formData ) ) {
				return;
			}
		}

		// Fall back to a synchronous POST request.
		// See https://developer.mozilla.org/en-US/docs/Web/API/Navigator/sendBeacon
		$.post({
			async: false,
			data: data,
			url: ajaxurl
		});
	});

	// Multiple taxonomies.
	if ( $('#tagsdiv-post_tag').length ) {
		window.tagBox && window.tagBox.init();
	} else {
		$('.meta-box-sortables').children('div.postbox').each(function(){
			if ( this.id.indexOf('tagsdiv-') === 0 ) {
				window.tagBox && window.tagBox.init();
				return false;
			}
		});
	}

	// Handle categories.
	$('.categorydiv').each( function(){
		var this_id = $(this).attr('id'), catAddBefore, catAddAfter, taxonomyParts, taxonomy, settingName;

		taxonomyParts = this_id.split('-');
		taxonomyParts.shift();
		taxonomy = taxonomyParts.join('-');
		settingName = taxonomy + '_tab';

		if ( taxonomy == 'category' ) {
			settingName = 'cats';
		}

		// @todo Move to jQuery 1.3+, support for multiple hierarchical taxonomies, see gc-lists.js.
		$('a', '#' + taxonomy + '-tabs').on( 'click', function( e ) {
			e.preventDefault();
			var t = $(this).attr('href');
			$(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
			$('#' + taxonomy + '-tabs').siblings('.tabs-panel').hide();
			$(t).show();
			if ( '#' + taxonomy + '-all' == t ) {
				deleteUserSetting( settingName );
			} else {
				setUserSetting( settingName, 'pop' );
			}
		});

		if ( getUserSetting( settingName ) )
			$('a[href="#' + taxonomy + '-pop"]', '#' + taxonomy + '-tabs').trigger( 'click' );

		// Add category button controls.
		$('#new' + taxonomy).one( 'focus', function() {
			$( this ).val( '' ).removeClass( 'form-input-tip' );
		});

		// On [Enter] submit the taxonomy.
		$('#new' + taxonomy).on( 'keypress', function(event){
			if( 13 === event.keyCode ) {
				event.preventDefault();
				$('#' + taxonomy + '-add-submit').trigger( 'click' );
			}
		});

		// After submitting a new taxonomy, re-focus the input field.
		$('#' + taxonomy + '-add-submit').on( 'click', function() {
			$('#new' + taxonomy).trigger( 'focus' );
		});

		/**
		 * Before adding a new taxonomy, disable submit button.
		 *
		 * @param {Object} s Taxonomy object which will be added.
		 *
		 * @return {Object}
		 */
		catAddBefore = function( s ) {
			if ( !$('#new'+taxonomy).val() ) {
				return false;
			}

			s.data += '&' + $( ':checked', '#'+taxonomy+'checklist' ).serialize();
			$( '#' + taxonomy + '-add-submit' ).prop( 'disabled', true );
			return s;
		};

		/**
		 * Re-enable submit button after a taxonomy has been added.
		 *
		 * Re-enable submit button.
		 * If the taxonomy has a parent place the taxonomy underneath the parent.
		 *
		 * @param {Object} r Response.
		 * @param {Object} s Taxonomy data.
		 *
		 * @return {void}
		 */
		catAddAfter = function( r, s ) {
			var sup, drop = $('#new'+taxonomy+'_parent');

			$( '#' + taxonomy + '-add-submit' ).prop( 'disabled', false );
			if ( 'undefined' != s.parsed.responses[0] && (sup = s.parsed.responses[0].supplemental.newcat_parent) ) {
				drop.before(sup);
				drop.remove();
			}
		};

		$('#' + taxonomy + 'checklist').gcList({
			alt: '',
			response: taxonomy + '-ajax-response',
			addBefore: catAddBefore,
			addAfter: catAddAfter
		});

		// Add new taxonomy button toggles input form visibility.
		$('#' + taxonomy + '-add-toggle').on( 'click', function( e ) {
			e.preventDefault();
			$('#' + taxonomy + '-adder').toggleClass( 'gc-hidden-children' );
			$('a[href="#' + taxonomy + '-all"]', '#' + taxonomy + '-tabs').trigger( 'click' );
			$('#new'+taxonomy).trigger( 'focus' );
		});

		// Sync checked items between "All {taxonomy}" and "Most used" lists.
		$('#' + taxonomy + 'checklist, #' + taxonomy + 'checklist-pop').on(
			'click',
			'li.popular-category > label input[type="checkbox"]',
			function() {
				var t = $(this), c = t.is(':checked'), id = t.val();
				if ( id && t.parents('#taxonomy-'+taxonomy).length )
					$('#in-' + taxonomy + '-' + id + ', #in-popular-' + taxonomy + '-' + id).prop( 'checked', c );
			}
		);

	}); // End cats.

	// Custom Fields postbox.
	if ( $('#postcustom').length ) {
		$( '#the-list' ).gcList( {
			/**
			 * Add current post_ID to request to fetch custom fields
			 *
			 * @ignore
			 *
			 * @param {Object} s Request object.
			 *
			 * @return {Object} Data modified with post_ID attached.
			 */
			addBefore: function( s ) {
				s.data += '&post_id=' + $('#post_ID').val();
				return s;
			},
			/**
			 * Show the listing of custom fields after fetching.
			 *
			 * @ignore
			 */
			addAfter: function() {
				$('table#list-table').show();
			}
		});
	}

	/*
	 * Publish Post box (#submitdiv)
	 */
	if ( $('#submitdiv').length ) {
		stamp = $('#timestamp').html();
		visibility = $('#post-visibility-display').html();

		/**
		 * When the visibility of a post changes sub-options should be shown or hidden.
		 *
		 * @ignore
		 *
		 * @return {void}
		 */
		updateVisibility = function() {
			// Show sticky for public posts.
			if ( $postVisibilitySelect.find('input:radio:checked').val() != 'public' ) {
				$('#sticky').prop('checked', false);
				$('#sticky-span').hide();
			} else {
				$('#sticky-span').show();
			}

			// Show password input field for password protected post.
			if ( $postVisibilitySelect.find('input:radio:checked').val() != 'password' ) {
				$('#password-span').hide();
			} else {
				$('#password-span').show();
			}
		};

		/**
		 * Make sure all labels represent the current settings.
		 *
		 * @ignore
		 *
		 * @return {boolean} False when an invalid timestamp has been selected, otherwise True.
		 */
		updateText = function() {

			if ( ! $timestampdiv.length )
				return true;

			var attemptedDate, originalDate, currentDate, publishOn, postStatus = $('#post_status'),
				optPublish = $('option[value="publish"]', postStatus), aa = $('#aa').val(),
				mm = $('#mm').val(), jj = $('#jj').val(), hh = $('#hh').val(), mn = $('#mn').val();

			attemptedDate = new Date( aa, mm - 1, jj, hh, mn );
			originalDate = new Date(
				$('#hidden_aa').val(),
				$('#hidden_mm').val() -1,
				$('#hidden_jj').val(),
				$('#hidden_hh').val(),
				$('#hidden_mn').val()
			);
			currentDate = new Date(
				$('#cur_aa').val(),
				$('#cur_mm').val() -1,
				$('#cur_jj').val(),
				$('#cur_hh').val(),
				$('#cur_mn').val()
			);

			// Catch unexpected date problems.
			if (
				attemptedDate.getFullYear() != aa ||
				(1 + attemptedDate.getMonth()) != mm ||
				attemptedDate.getDate() != jj ||
				attemptedDate.getMinutes() != mn
			) {
				$timestampdiv.find('.timestamp-wrap').addClass('form-invalid');
				return false;
			} else {
				$timestampdiv.find('.timestamp-wrap').removeClass('form-invalid');
			}

			// Determine what the publish should be depending on the date and post status.
			if ( attemptedDate > currentDate && $('#original_post_status').val() != 'future' ) {
				publishOn = __( '预定发布于：' );
				$('#publish').val( _x( '时间表', 'post action/button label' ) );
			} else if ( attemptedDate <= currentDate && $('#original_post_status').val() != 'publish' ) {
				publishOn = __( '发布于：' );
				$('#publish').val( __( '发布' ) );
			} else {
				publishOn = __( '发布于：' );
				$('#publish').val( __( '更新' ) );
			}

			// If the date is the same, set it to trigger update events.
			if ( originalDate.toUTCString() == attemptedDate.toUTCString() ) {
				// Re-set to the current value.
				$('#timestamp').html(stamp);
			} else {
				$('#timestamp').html(
					'\n' + publishOn + ' <b>' +
					// translators: 1: Month, 2: Day, 3: Year, 4: Hour, 5: Minute.
					__( '%3$s年%1$s%2$s日%4$s:%5$s' )
						.replace( '%1$s', $( 'option[value="' + mm + '"]', '#mm' ).attr( 'data-text' ) )
						.replace( '%2$s', parseInt( jj, 10 ) )
						.replace( '%3$s', aa )
						.replace( '%4$s', ( '00' + hh ).slice( -2 ) )
						.replace( '%5$s', ( '00' + mn ).slice( -2 ) ) +
						'</b> '
				);
			}

			// Add "privately published" to post status when applies.
			if ( $postVisibilitySelect.find('input:radio:checked').val() == 'private' ) {
				$('#publish').val( __( '更新' ) );
				if ( 0 === optPublish.length ) {
					postStatus.append('<option value="publish">' + __( '已私密发布' ) + '</option>');
				} else {
					optPublish.html( __( '已私密发布' ) );
				}
				$('option[value="publish"]', postStatus).prop('selected', true);
				$('#misc-publishing-actions .edit-post-status').hide();
			} else {
				if ( $('#original_post_status').val() == 'future' || $('#original_post_status').val() == 'draft' ) {
					if ( optPublish.length ) {
						optPublish.remove();
						postStatus.val($('#hidden_post_status').val());
					}
				} else {
					optPublish.html( __( '已发布' ) );
				}
				if ( postStatus.is(':hidden') )
					$('#misc-publishing-actions .edit-post-status').show();
			}

			// Update "Status:" to currently selected status.
			$('#post-status-display').text(
				// Remove any potential tags from post status text.
				gc.sanitize.stripTagsAndEncodeText( $('option:selected', postStatus).text() )
			);

			// Show or hide the "Save Draft" button.
			if (
				$('option:selected', postStatus).val() == 'private' ||
				$('option:selected', postStatus).val() == 'publish'
			) {
				$('#save-post').hide();
			} else {
				$('#save-post').show();
				if ( $('option:selected', postStatus).val() == 'pending' ) {
					$('#save-post').show().val( __( '保存为待审' ) );
				} else {
					$('#save-post').show().val( __( '保存草稿' ) );
				}
			}
			return true;
		};

		// Show the visibility options and hide the toggle button when opened.
		$( '#visibility .edit-visibility').on( 'click', function( e ) {
			e.preventDefault();
			if ( $postVisibilitySelect.is(':hidden') ) {
				updateVisibility();
				$postVisibilitySelect.slideDown( 'fast', function() {
					$postVisibilitySelect.find( 'input[type="radio"]' ).first().trigger( 'focus' );
				} );
				$(this).hide();
			}
		});

		// Cancel visibility selection area and hide it from view.
		$postVisibilitySelect.find('.cancel-post-visibility').on( 'click', function( event ) {
			$postVisibilitySelect.slideUp('fast');
			$('#visibility-radio-' + $('#hidden-post-visibility').val()).prop('checked', true);
			$('#post_password').val($('#hidden-post-password').val());
			$('#sticky').prop('checked', $('#hidden-post-sticky').prop('checked'));
			$('#post-visibility-display').html(visibility);
			$('#visibility .edit-visibility').show().trigger( 'focus' );
			updateText();
			event.preventDefault();
		});

		// Set the selected visibility as current.
		$postVisibilitySelect.find('.save-post-visibility').on( 'click', function( event ) { // Crazyhorse - multiple OK cancels.
			var visibilityLabel = '', selectedVisibility = $postVisibilitySelect.find('input:radio:checked').val();

			$postVisibilitySelect.slideUp('fast');
			$('#visibility .edit-visibility').show().trigger( 'focus' );
			updateText();

			if ( 'public' !== selectedVisibility ) {
				$('#sticky').prop('checked', false);
			}

			switch ( selectedVisibility ) {
				case 'public':
					visibilityLabel = $( '#sticky' ).prop( 'checked' ) ? __( '公开并置顶' ) : __( '公开' );
					break;
				case 'private':
					visibilityLabel = __( '私密' );
					break;
				case 'password':
					visibilityLabel = __( '密码保护' );
					break;
			}

			$('#post-visibility-display').text( visibilityLabel );
			event.preventDefault();
		});

		// When the selection changes, update labels.
		$postVisibilitySelect.find('input:radio').on( 'change', function() {
			updateVisibility();
		});

		// Edit publish time click.
		$timestampdiv.siblings('a.edit-timestamp').on( 'click', function( event ) {
			if ( $timestampdiv.is( ':hidden' ) ) {
				$timestampdiv.slideDown( 'fast', function() {
					$( 'input, select', $timestampdiv.find( '.timestamp-wrap' ) ).first().trigger( 'focus' );
				} );
				$(this).hide();
			}
			event.preventDefault();
		});

		// Cancel editing the publish time and hide the settings.
		$timestampdiv.find('.cancel-timestamp').on( 'click', function( event ) {
			$timestampdiv.slideUp('fast').siblings('a.edit-timestamp').show().trigger( 'focus' );
			$('#mm').val($('#hidden_mm').val());
			$('#jj').val($('#hidden_jj').val());
			$('#aa').val($('#hidden_aa').val());
			$('#hh').val($('#hidden_hh').val());
			$('#mn').val($('#hidden_mn').val());
			updateText();
			event.preventDefault();
		});

		// Save the changed timestamp.
		$timestampdiv.find('.save-timestamp').on( 'click', function( event ) { // Crazyhorse - multiple OK cancels.
			if ( updateText() ) {
				$timestampdiv.slideUp('fast');
				$timestampdiv.siblings('a.edit-timestamp').show().trigger( 'focus' );
			}
			event.preventDefault();
		});

		// Cancel submit when an invalid timestamp has been selected.
		$('#post').on( 'submit', function( event ) {
			if ( ! updateText() ) {
				event.preventDefault();
				$timestampdiv.show();

				if ( gc.autosave ) {
					gc.autosave.enableButtons();
				}

				$( '#publishing-action .spinner' ).removeClass( 'is-active' );
			}
		});

		// Post Status edit click.
		$postStatusSelect.siblings('a.edit-post-status').on( 'click', function( event ) {
			if ( $postStatusSelect.is( ':hidden' ) ) {
				$postStatusSelect.slideDown( 'fast', function() {
					$postStatusSelect.find('select').trigger( 'focus' );
				} );
				$(this).hide();
			}
			event.preventDefault();
		});

		// Save the Post Status changes and hide the options.
		$postStatusSelect.find('.save-post-status').on( 'click', function( event ) {
			$postStatusSelect.slideUp( 'fast' ).siblings( 'a.edit-post-status' ).show().trigger( 'focus' );
			updateText();
			event.preventDefault();
		});

		// Cancel Post Status editing and hide the options.
		$postStatusSelect.find('.cancel-post-status').on( 'click', function( event ) {
			$postStatusSelect.slideUp( 'fast' ).siblings( 'a.edit-post-status' ).show().trigger( 'focus' );
			$('#post_status').val( $('#hidden_post_status').val() );
			updateText();
			event.preventDefault();
		});
	}

	/**
	 * Handle the editing of the post_name. Create the required HTML elements and
	 * update the changes via Ajax.
	 *
	 * @global
	 *
	 * @return {void}
	 */
	function editPermalink() {
		var i, slug_value, slug_label,
			$el, revert_e,
			c = 0,
			real_slug = $('#post_name'),
			revert_slug = real_slug.val(),
			permalink = $( '#sample-permalink' ),
			permalinkOrig = permalink.html(),
			permalinkInner = $( '#sample-permalink a' ).html(),
			buttons = $('#edit-slug-buttons'),
			buttonsOrig = buttons.html(),
			full = $('#editable-post-name-full');

		// Deal with Twemoji in the post-name.
		full.find( 'img' ).replaceWith( function() { return this.alt; } );
		full = full.html();

		permalink.html( permalinkInner );

		// Save current content to revert to when cancelling.
		$el = $( '#editable-post-name' );
		revert_e = $el.html();

		buttons.html(
			'<button type="button" class="save button button-small">' + __( '确定' ) + '</button> ' +
			'<button type="button" class="cancel button-link">' + __( '取消' ) + '</button>'
		);

		// Save permalink changes.
		buttons.children( '.save' ).on( 'click', function() {
			var new_slug = $el.children( 'input' ).val();

			if ( new_slug == $('#editable-post-name-full').text() ) {
				buttons.children('.cancel').trigger( 'click' );
				return;
			}

			$.post(
				ajaxurl,
				{
					action: 'sample-permalink',
					post_id: postId,
					new_slug: new_slug,
					new_title: $('#title').val(),
					samplepermalinknonce: $('#samplepermalinknonce').val()
				},
				function(data) {
					var box = $('#edit-slug-box');
					box.html(data);
					if (box.hasClass('hidden')) {
						box.fadeIn('fast', function () {
							box.removeClass('hidden');
						});
					}

					buttons.html(buttonsOrig);
					permalink.html(permalinkOrig);
					real_slug.val(new_slug);
					$( '.edit-slug' ).trigger( 'focus' );
					gc.a11y.speak( __( '固定链接已保存' ) );
				}
			);
		});

		// Cancel editing of permalink.
		buttons.children( '.cancel' ).on( 'click', function() {
			$('#view-post-btn').show();
			$el.html(revert_e);
			buttons.html(buttonsOrig);
			permalink.html(permalinkOrig);
			real_slug.val(revert_slug);
			$( '.edit-slug' ).trigger( 'focus' );
		});

		// If more than 1/4th of 'full' is '%', make it empty.
		for ( i = 0; i < full.length; ++i ) {
			if ( '%' == full.charAt(i) )
				c++;
		}
		slug_value = ( c > full.length / 4 ) ? '' : full;
		slug_label = __( 'URL别名' );

		$el.html(
			'<label for="new-post-slug" class="screen-reader-text">' + slug_label + '</label>' +
			'<input type="text" id="new-post-slug" value="' + slug_value + '" autocomplete="off" spellcheck="false" />'
		).children( 'input' ).on( 'keydown', function( e ) {
			var key = e.which;
			// On [Enter], just save the new slug, don't save the post.
			if ( 13 === key ) {
				e.preventDefault();
				buttons.children( '.save' ).trigger( 'click' );
			}
			// On [Esc] cancel the editing.
			if ( 27 === key ) {
				buttons.children( '.cancel' ).trigger( 'click' );
			}
		} ).on( 'keyup', function() {
			real_slug.val( this.value );
		}).trigger( 'focus' );
	}

	$( '#titlediv' ).on( 'click', '.edit-slug', function() {
		editPermalink();
	});

	/**
	 * Adds screen reader text to the title label when needed.
	 *
	 * Use the 'screen-reader-text' class to emulate a placeholder attribute
	 * and hide the label when entering a value.
	 *
	 * @param {string} id Optional. HTML ID to add the screen reader helper text to.
	 *
	 * @global
	 *
	 * @return {void}
	 */
	window.gctitlehint = function( id ) {
		id = id || 'title';

		var title = $( '#' + id ), titleprompt = $( '#' + id + '-prompt-text' );

		if ( '' === title.val() ) {
			titleprompt.removeClass( 'screen-reader-text' );
		}

		title.on( 'input', function() {
			if ( '' === this.value ) {
				titleprompt.removeClass( 'screen-reader-text' );
				return;
			}

			titleprompt.addClass( 'screen-reader-text' );
		} );
	};

	gctitlehint();

	// Resize the WYSIWYG and plain text editors.
	( function() {
		var editor, offset, mce,
			$handle = $('#post-status-info'),
			$postdivrich = $('#postdivrich');

		// If there are no textareas or we are on a touch device, we can't do anything.
		if ( ! $textarea.length || 'ontouchstart' in window ) {
			// Hide the resize handle.
			$('#content-resize-handle').hide();
			return;
		}

		/**
		 * Handle drag event.
		 *
		 * @param {Object} event Event containing details about the drag.
		 */
		function dragging( event ) {
			if ( $postdivrich.hasClass( 'gc-editor-expand' ) ) {
				return;
			}

			if ( mce ) {
				editor.theme.resizeTo( null, offset + event.pageY );
			} else {
				$textarea.height( Math.max( 50, offset + event.pageY ) );
			}

			event.preventDefault();
		}

		/**
		 * When the dragging stopped make sure we return focus and do a sanity check on the height.
		 */
		function endDrag() {
			var height, toolbarHeight;

			if ( $postdivrich.hasClass( 'gc-editor-expand' ) ) {
				return;
			}

			if ( mce ) {
				editor.focus();
				toolbarHeight = parseInt( $( '#gc-content-editor-container .mce-toolbar-grp' ).height(), 10 );

				if ( toolbarHeight < 10 || toolbarHeight > 200 ) {
					toolbarHeight = 30;
				}

				height = parseInt( $('#content_ifr').css('height'), 10 ) + toolbarHeight - 28;
			} else {
				$textarea.trigger( 'focus' );
				height = parseInt( $textarea.css('height'), 10 );
			}

			$document.off( '.gc-editor-resize' );

			// Sanity check: normalize height to stay within acceptable ranges.
			if ( height && height > 50 && height < 5000 ) {
				setUserSetting( 'ed_size', height );
			}
		}

		$handle.on( 'mousedown.gc-editor-resize', function( event ) {
			if ( typeof tinymce !== 'undefined' ) {
				editor = tinymce.get('content');
			}

			if ( editor && ! editor.isHidden() ) {
				mce = true;
				offset = $('#content_ifr').height() - event.pageY;
			} else {
				mce = false;
				offset = $textarea.height() - event.pageY;
				$textarea.trigger( 'blur' );
			}

			$document.on( 'mousemove.gc-editor-resize', dragging )
				.on( 'mouseup.gc-editor-resize mouseleave.gc-editor-resize', endDrag );

			event.preventDefault();
		}).on( 'mouseup.gc-editor-resize', endDrag );
	})();

	// TinyMCE specific handling of Post Format changes to reflect in the editor.
	if ( typeof tinymce !== 'undefined' ) {
		// When changing post formats, change the editor body class.
		$( '#post-formats-select input.post-format' ).on( 'change.set-editor-class', function() {
			var editor, body, format = this.id;

			if ( format && $( this ).prop( 'checked' ) && ( editor = tinymce.get( 'content' ) ) ) {
				body = editor.getBody();
				body.className = body.className.replace( /\bpost-format-[^ ]+/, '' );
				editor.dom.addClass( body, format == 'post-format-0' ? 'post-format-standard' : format );
				$( document ).trigger( 'editor-classchange' );
			}
		});

		// When changing page template, change the editor body class.
		$( '#page_template' ).on( 'change.set-editor-class', function() {
			var editor, body, pageTemplate = $( this ).val() || '';

			pageTemplate = pageTemplate.substr( pageTemplate.lastIndexOf( '/' ) + 1, pageTemplate.length )
				.replace( /\.php$/, '' )
				.replace( /\./g, '-' );

			if ( pageTemplate && ( editor = tinymce.get( 'content' ) ) ) {
				body = editor.getBody();
				body.className = body.className.replace( /\bpage-template-[^ ]+/, '' );
				editor.dom.addClass( body, 'page-template-' + pageTemplate );
				$( document ).trigger( 'editor-classchange' );
			}
		});

	}

	// Save on pressing [Ctrl]/[Command] + [S] in the Text editor.
	$textarea.on( 'keydown.gc-autosave', function( event ) {
		// Key [S] has code 83.
		if ( event.which === 83 ) {
			if (
				event.shiftKey ||
				event.altKey ||
				( isMac && ( ! event.metaKey || event.ctrlKey ) ) ||
				( ! isMac && ! event.ctrlKey )
			) {
				return;
			}

			gc.autosave && gc.autosave.server.triggerSave();
			event.preventDefault();
		}
	});

	// If the last status was auto-draft and the save is triggered, edit the current URL.
	if ( $( '#original_post_status' ).val() === 'auto-draft' && window.history.replaceState ) {
		var location;

		$( '#publish' ).on( 'click', function() {
			location = window.location.href;
			location += ( location.indexOf( '?' ) !== -1 ) ? '&' : '?';
			location += 'gc-post-new-reload=true';

			window.history.replaceState( null, null, location );
		});
	}

	/**
	 * Copies the attachment URL in the Edit Media page to the clipboard.
	 *
	 *
	 * @param {MouseEvent} event A click event.
	 *
	 * @return {void}
	 */
	copyAttachmentURLClipboard.on( 'success', function( event ) {
		var triggerElement = $( event.trigger ),
			successElement = $( '.success', triggerElement.closest( '.copy-to-clipboard-container' ) );

		// Clear the selection and move focus back to the trigger.
		event.clearSelection();
		// Handle ClipboardJS focus bug, see https://github.com/zenorocha/clipboard.js/issues/680
		triggerElement.trigger( 'focus' );

		// Show success visual feedback.
		clearTimeout( copyAttachmentURLSuccessTimeout );
		successElement.removeClass( 'hidden' );

		// Hide success visual feedback after 3 seconds since last success.
		copyAttachmentURLSuccessTimeout = setTimeout( function() {
			successElement.addClass( 'hidden' );
		}, 3000 );

		// Handle success audible feedback.
		gc.a11y.speak( __( '文件URL已复制至剪贴板' ) );
	} );
} );

/**
 * TinyMCE word count display
 */
( function( $, counter ) {
	$( function() {
		var $content = $( '#content' ),
			$count = $( '#gc-word-count' ).find( '.word-count' ),
			prevCount = 0,
			contentEditor;

		/**
		 * Get the word count from TinyMCE and display it
		 */
		function update() {
			var text, count;

			if ( ! contentEditor || contentEditor.isHidden() ) {
				text = $content.val();
			} else {
				text = contentEditor.getContent( { format: 'raw' } );
			}

			count = counter.count( text );

			if ( count !== prevCount ) {
				$count.text( count );
			}

			prevCount = count;
		}

		/**
		 * Bind the word count update triggers.
		 *
		 * When a node change in the main TinyMCE editor has been triggered.
		 * When a key has been released in the plain text content editor.
		 */
		$( document ).on( 'tinymce-editor-init', function( event, editor ) {
			if ( editor.id !== 'content' ) {
				return;
			}

			contentEditor = editor;

			editor.on( 'nodechange keyup', _.debounce( update, 1000 ) );
		} );

		$content.on( 'input keyup', _.debounce( update, 1000 ) );

		update();
	} );

} )( jQuery, new gc.utils.WordCounter() );
