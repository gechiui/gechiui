/**
 * @output gc-includes/js/gclink.js
 */

 /* global gcLink */

( function( $, gcLinkL10n, gc ) {
	var editor, searchTimer, River, Query, correctedURL,
		emailRegexp = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,63}$/i,
		urlRegexp = /^(https?|ftp):\/\/[A-Z0-9.-]+\.[A-Z]{2,63}[^ "]*$/i,
		inputs = {},
		rivers = {},
		isTouch = ( 'ontouchend' in document );

	function getLink() {
		if ( editor ) {
			return editor.$( 'a[data-gclink-edit="true"]' );
		}

		return null;
	}

	window.gcLink = {
		timeToTriggerRiver: 150,
		minRiverAJAXDuration: 200,
		riverBottomThreshold: 5,
		keySensitivity: 100,
		lastSearch: '',
		textarea: '',
		modalOpen: false,

		init: function() {
			inputs.wrap = $('#gc-link-wrap');
			inputs.dialog = $( '#gc-link' );
			inputs.backdrop = $( '#gc-link-backdrop' );
			inputs.submit = $( '#gc-link-submit' );
			inputs.close = $( '#gc-link-close' );

			// Input.
			inputs.text = $( '#gc-link-text' );
			inputs.url = $( '#gc-link-url' );
			inputs.nonce = $( '#_ajax_linking_nonce' );
			inputs.openInNewTab = $( '#gc-link-target' );
			inputs.search = $( '#gc-link-search' );

			// Build rivers.
			rivers.search = new River( $( '#search-results' ) );
			rivers.recent = new River( $( '#most-recent-results' ) );
			rivers.elements = inputs.dialog.find( '.query-results' );

			// Get search notice text.
			inputs.queryNotice = $( '#query-notice-message' );
			inputs.queryNoticeTextDefault = inputs.queryNotice.find( '.query-notice-default' );
			inputs.queryNoticeTextHint = inputs.queryNotice.find( '.query-notice-hint' );

			// Bind event handlers.
			inputs.dialog.on( 'keydown', gcLink.keydown );
			inputs.dialog.on( 'keyup', gcLink.keyup );
			inputs.submit.on( 'click', function( event ) {
				event.preventDefault();
				gcLink.update();
			});

			inputs.close.add( inputs.backdrop ).add( '#gc-link-cancel button' ).on( 'click', function( event ) {
				event.preventDefault();
				gcLink.close();
			});

			rivers.elements.on( 'river-select', gcLink.updateFields );

			// Display 'hint' message when search field or 'query-results' box are focused.
			inputs.search.on( 'focus.gclink', function() {
				inputs.queryNoticeTextDefault.hide();
				inputs.queryNoticeTextHint.removeClass( 'screen-reader-text' ).show();
			} ).on( 'blur.gclink', function() {
				inputs.queryNoticeTextDefault.show();
				inputs.queryNoticeTextHint.addClass( 'screen-reader-text' ).hide();
			} );

			inputs.search.on( 'keyup input', function() {
				window.clearTimeout( searchTimer );
				searchTimer = window.setTimeout( function() {
					gcLink.searchInternalLinks();
				}, 500 );
			});

			inputs.url.on( 'paste', function() {
				setTimeout( gcLink.correctURL, 0 );
			} );

			inputs.url.on( 'blur', gcLink.correctURL );
		},

		// If URL wasn't corrected last time and doesn't start with http:, https:, ? # or /, prepend http://.
		correctURL: function () {
			var url = inputs.url.val().trim();

			if ( url && correctedURL !== url && ! /^(?:[a-z]+:|#|\?|\.|\/)/.test( url ) ) {
				inputs.url.val( 'http://' + url );
				correctedURL = url;
			}
		},

		open: function( editorId, url, text ) {
			var ed,
				$body = $( document.body );

			$body.addClass( 'modal-open' );
			gcLink.modalOpen = true;

			gcLink.range = null;

			if ( editorId ) {
				window.gcActiveEditor = editorId;
			}

			if ( ! window.gcActiveEditor ) {
				return;
			}

			this.textarea = $( '#' + window.gcActiveEditor ).get( 0 );

			if ( typeof window.tinymce !== 'undefined' ) {
				// Make sure the link wrapper is the last element in the body,
				// or the inline editor toolbar may show above the backdrop.
				$body.append( inputs.backdrop, inputs.wrap );

				ed = window.tinymce.get( window.gcActiveEditor );

				if ( ed && ! ed.isHidden() ) {
					editor = ed;
				} else {
					editor = null;
				}
			}

			if ( ! gcLink.isMCE() && document.selection ) {
				this.textarea.focus();
				this.range = document.selection.createRange();
			}

			inputs.wrap.show();
			inputs.backdrop.show();

			gcLink.refresh( url, text );

			$( document ).trigger( 'gclink-open', inputs.wrap );
		},

		isMCE: function() {
			return editor && ! editor.isHidden();
		},

		refresh: function( url, text ) {
			var linkText = '';

			// Refresh rivers (clear links, check visibility).
			rivers.search.refresh();
			rivers.recent.refresh();

			if ( gcLink.isMCE() ) {
				gcLink.mceRefresh( url, text );
			} else {
				// For the Text editor the "Link text" field is always shown.
				if ( ! inputs.wrap.hasClass( 'has-text-field' ) ) {
					inputs.wrap.addClass( 'has-text-field' );
				}

				if ( document.selection ) {
					// Old IE.
					linkText = document.selection.createRange().text || text || '';
				} else if ( typeof this.textarea.selectionStart !== 'undefined' &&
					( this.textarea.selectionStart !== this.textarea.selectionEnd ) ) {
					// W3C.
					text = this.textarea.value.substring( this.textarea.selectionStart, this.textarea.selectionEnd ) || text || '';
				}

				inputs.text.val( text );
				gcLink.setDefaultValues();
			}

			if ( isTouch ) {
				// Close the onscreen keyboard.
				inputs.url.trigger( 'focus' ).trigger( 'blur' );
			} else {
				/*
				 * Focus the URL field and highlight its contents.
				 * If this is moved above the selection changes,
				 * IE will show a flashing cursor over the dialog.
				 */
				window.setTimeout( function() {
					inputs.url[0].select();
					inputs.url.trigger( 'focus' );
				} );
			}

			// Load the most recent results if this is the first time opening the panel.
			if ( ! rivers.recent.ul.children().length ) {
				rivers.recent.ajax();
			}

			correctedURL = inputs.url.val().replace( /^http:\/\//, '' );
		},

		hasSelectedText: function( linkNode ) {
			var node, nodes, i, html = editor.selection.getContent();

			// Partial html and not a fully selected anchor element.
			if ( /</.test( html ) && ( ! /^<a [^>]+>[^<]+<\/a>$/.test( html ) || html.indexOf('href=') === -1 ) ) {
				return false;
			}

			if ( linkNode.length ) {
				nodes = linkNode[0].childNodes;

				if ( ! nodes || ! nodes.length ) {
					return false;
				}

				for ( i = nodes.length - 1; i >= 0; i-- ) {
					node = nodes[i];

					if ( node.nodeType != 3 && ! window.tinymce.dom.BookmarkManager.isBookmarkNode( node ) ) {
						return false;
					}
				}
			}

			return true;
		},

		mceRefresh: function( searchStr, text ) {
			var linkText, href,
				linkNode = getLink(),
				onlyText = this.hasSelectedText( linkNode );

			if ( linkNode.length ) {
				linkText = linkNode.text();
				href = linkNode.attr( 'href' );

				if ( ! linkText.trim() ) {
					linkText = text || '';
				}

				if ( searchStr && ( urlRegexp.test( searchStr ) || emailRegexp.test( searchStr ) ) ) {
					href = searchStr;
				}

				if ( href !== '_gc_link_placeholder' ) {
					inputs.url.val( href );
					inputs.openInNewTab.prop( 'checked', '_blank' === linkNode.attr( 'target' ) );
					inputs.submit.val( gcLinkL10n.update );
				} else {
					this.setDefaultValues( linkText );
				}

				if ( searchStr && searchStr !== href ) {
					// The user has typed something in the inline dialog. Trigger a search with it.
					inputs.search.val( searchStr );
				} else {
					inputs.search.val( '' );
				}

				// Always reset the search.
				window.setTimeout( function() {
					gcLink.searchInternalLinks();
				} );
			} else {
				linkText = editor.selection.getContent({ format: 'text' }) || text || '';
				this.setDefaultValues( linkText );
			}

			if ( onlyText ) {
				inputs.text.val( linkText );
				inputs.wrap.addClass( 'has-text-field' );
			} else {
				inputs.text.val( '' );
				inputs.wrap.removeClass( 'has-text-field' );
			}
		},

		close: function( reset ) {
			$( document.body ).removeClass( 'modal-open' );
			gcLink.modalOpen = false;

			if ( reset !== 'noReset' ) {
				if ( ! gcLink.isMCE() ) {
					gcLink.textarea.focus();

					if ( gcLink.range ) {
						gcLink.range.moveToBookmark( gcLink.range.getBookmark() );
						gcLink.range.select();
					}
				} else {
					if ( editor.plugins.gclink ) {
						editor.plugins.gclink.close();
					}

					editor.focus();
				}
			}

			inputs.backdrop.hide();
			inputs.wrap.hide();

			correctedURL = false;

			$( document ).trigger( 'gclink-close', inputs.wrap );
		},

		getAttrs: function() {
			gcLink.correctURL();

			return {
				href: inputs.url.val().trim(),
				target: inputs.openInNewTab.prop( 'checked' ) ? '_blank' : null
			};
		},

		buildHtml: function(attrs) {
			var html = '<a href="' + attrs.href + '"';

			if ( attrs.target ) {
				html += ' rel="noopener" target="' + attrs.target + '"';
			}

			return html + '>';
		},

		update: function() {
			if ( gcLink.isMCE() ) {
				gcLink.mceUpdate();
			} else {
				gcLink.htmlUpdate();
			}
		},

		htmlUpdate: function() {
			var attrs, text, html, begin, end, cursor, selection,
				textarea = gcLink.textarea;

			if ( ! textarea ) {
				return;
			}

			attrs = gcLink.getAttrs();
			text = inputs.text.val();

			var parser = document.createElement( 'a' );
			parser.href = attrs.href;

			if ( 'javascript:' === parser.protocol || 'data:' === parser.protocol ) { // jshint ignore:line
				attrs.href = '';
			}

			// If there's no href, return.
			if ( ! attrs.href ) {
				return;
			}

			html = gcLink.buildHtml(attrs);

			// Insert HTML.
			if ( document.selection && gcLink.range ) {
				// IE.
				// Note: If no text is selected, IE will not place the cursor
				// inside the closing tag.
				textarea.focus();
				gcLink.range.text = html + ( text || gcLink.range.text ) + '</a>';
				gcLink.range.moveToBookmark( gcLink.range.getBookmark() );
				gcLink.range.select();

				gcLink.range = null;
			} else if ( typeof textarea.selectionStart !== 'undefined' ) {
				// W3C.
				begin = textarea.selectionStart;
				end = textarea.selectionEnd;
				selection = text || textarea.value.substring( begin, end );
				html = html + selection + '</a>';
				cursor = begin + html.length;

				// If no text is selected, place the cursor inside the closing tag.
				if ( begin === end && ! selection ) {
					cursor -= 4;
				}

				textarea.value = (
					textarea.value.substring( 0, begin ) +
					html +
					textarea.value.substring( end, textarea.value.length )
				);

				// Update cursor position.
				textarea.selectionStart = textarea.selectionEnd = cursor;
			}

			gcLink.close();
			textarea.focus();
			$( textarea ).trigger( 'change' );

			// Audible confirmation message when a link has been inserted in the Editor.
			gc.a11y.speak( gcLinkL10n.linkInserted );
		},

		mceUpdate: function() {
			var attrs = gcLink.getAttrs(),
				$link, text, hasText;

			var parser = document.createElement( 'a' );
			parser.href = attrs.href;

			if ( 'javascript:' === parser.protocol || 'data:' === parser.protocol ) { // jshint ignore:line
				attrs.href = '';
			}

			if ( ! attrs.href ) {
				editor.execCommand( 'unlink' );
				gcLink.close();
				return;
			}

			$link = getLink();

			editor.undoManager.transact( function() {
				if ( ! $link.length ) {
					editor.execCommand( 'mceInsertLink', false, { href: '_gc_link_placeholder', 'data-gc-temp-link': 1 } );
					$link = editor.$( 'a[data-gc-temp-link="1"]' ).removeAttr( 'data-gc-temp-link' );
					hasText = $link.text().trim();
				}

				if ( ! $link.length ) {
					editor.execCommand( 'unlink' );
				} else {
					if ( inputs.wrap.hasClass( 'has-text-field' ) ) {
						text = inputs.text.val();

						if ( text ) {
							$link.text( text );
						} else if ( ! hasText ) {
							$link.text( attrs.href );
						}
					}

					attrs['data-gclink-edit'] = null;
					attrs['data-mce-href'] = attrs.href;
					$link.attr( attrs );
				}
			} );

			gcLink.close( 'noReset' );
			editor.focus();

			if ( $link.length ) {
				editor.selection.select( $link[0] );

				if ( editor.plugins.gclink ) {
					editor.plugins.gclink.checkLink( $link[0] );
				}
			}

			editor.nodeChanged();

			// Audible confirmation message when a link has been inserted in the Editor.
			gc.a11y.speak( gcLinkL10n.linkInserted );
		},

		updateFields: function( e, li ) {
			inputs.url.val( li.children( '.item-permalink' ).val() );

			if ( inputs.wrap.hasClass( 'has-text-field' ) && ! inputs.text.val() ) {
				inputs.text.val( li.children( '.item-title' ).text() );
			}
		},

		getUrlFromSelection: function( selection ) {
			if ( ! selection ) {
				if ( this.isMCE() ) {
					selection = editor.selection.getContent({ format: 'text' });
				} else if ( document.selection && gcLink.range ) {
					selection = gcLink.range.text;
				} else if ( typeof this.textarea.selectionStart !== 'undefined' ) {
					selection = this.textarea.value.substring( this.textarea.selectionStart, this.textarea.selectionEnd );
				}
			}

			selection = selection || '';
			selection = selection.trim();

			if ( selection && emailRegexp.test( selection ) ) {
				// Selection is email address.
				return 'mailto:' + selection;
			} else if ( selection && urlRegexp.test( selection ) ) {
				// Selection is URL.
				return selection.replace( /&amp;|&#0?38;/gi, '&' );
			}

			return '';
		},

		setDefaultValues: function( selection ) {
			inputs.url.val( this.getUrlFromSelection( selection ) );

			// Empty the search field and swap the "rivers".
			inputs.search.val('');
			gcLink.searchInternalLinks();

			// Update save prompt.
			inputs.submit.val( gcLinkL10n.save );
		},

		searchInternalLinks: function() {
			var waiting,
				search = inputs.search.val() || '',
				minInputLength = parseInt( gcLinkL10n.minInputLength, 10 ) || 3;

			if ( search.length >= minInputLength ) {
				rivers.recent.hide();
				rivers.search.show();

				// Don't search if the keypress didn't change the title.
				if ( gcLink.lastSearch == search )
					return;

				gcLink.lastSearch = search;
				waiting = inputs.search.parent().find( '.spinner' ).addClass( 'is-active' );

				rivers.search.change( search );
				rivers.search.ajax( function() {
					waiting.removeClass( 'is-active' );
				});
			} else {
				rivers.search.hide();
				rivers.recent.show();
			}
		},

		next: function() {
			rivers.search.next();
			rivers.recent.next();
		},

		prev: function() {
			rivers.search.prev();
			rivers.recent.prev();
		},

		keydown: function( event ) {
			var fn, id;

			// Escape key.
			if ( 27 === event.keyCode ) {
				gcLink.close();
				event.stopImmediatePropagation();
			// Tab key.
			} else if ( 9 === event.keyCode ) {
				id = event.target.id;

				// gc-link-submit must always be the last focusable element in the dialog.
				// Following focusable elements will be skipped on keyboard navigation.
				if ( id === 'gc-link-submit' && ! event.shiftKey ) {
					inputs.close.trigger( 'focus' );
					event.preventDefault();
				} else if ( id === 'gc-link-close' && event.shiftKey ) {
					inputs.submit.trigger( 'focus' );
					event.preventDefault();
				}
			}

			// Up Arrow and Down Arrow keys.
			if ( event.shiftKey || ( 38 !== event.keyCode && 40 !== event.keyCode ) ) {
				return;
			}

			if ( document.activeElement &&
				( document.activeElement.id === 'link-title-field' || document.activeElement.id === 'url-field' ) ) {
				return;
			}

			// Up Arrow key.
			fn = 38 === event.keyCode ? 'prev' : 'next';
			clearInterval( gcLink.keyInterval );
			gcLink[ fn ]();
			gcLink.keyInterval = setInterval( gcLink[ fn ], gcLink.keySensitivity );
			event.preventDefault();
		},

		keyup: function( event ) {
			// Up Arrow and Down Arrow keys.
			if ( 38 === event.keyCode || 40 === event.keyCode ) {
				clearInterval( gcLink.keyInterval );
				event.preventDefault();
			}
		},

		delayedCallback: function( func, delay ) {
			var timeoutTriggered, funcTriggered, funcArgs, funcContext;

			if ( ! delay )
				return func;

			setTimeout( function() {
				if ( funcTriggered )
					return func.apply( funcContext, funcArgs );
				// Otherwise, wait.
				timeoutTriggered = true;
			}, delay );

			return function() {
				if ( timeoutTriggered )
					return func.apply( this, arguments );
				// Otherwise, wait.
				funcArgs = arguments;
				funcContext = this;
				funcTriggered = true;
			};
		}
	};

	River = function( element, search ) {
		var self = this;
		this.element = element;
		this.ul = element.children( 'ul' );
		this.contentHeight = element.children( '#link-selector-height' );
		this.waiting = element.find('.river-waiting');

		this.change( search );
		this.refresh();

		$( '#gc-link .query-results, #gc-link #link-selector' ).on( 'scroll', function() {
			self.maybeLoad();
		});
		element.on( 'click', 'li', function( event ) {
			self.select( $( this ), event );
		});
	};

	$.extend( River.prototype, {
		refresh: function() {
			this.deselect();
			this.visible = this.element.is( ':visible' );
		},
		show: function() {
			if ( ! this.visible ) {
				this.deselect();
				this.element.show();
				this.visible = true;
			}
		},
		hide: function() {
			this.element.hide();
			this.visible = false;
		},
		// Selects a list item and triggers the river-select event.
		select: function( li, event ) {
			var liHeight, elHeight, liTop, elTop;

			if ( li.hasClass( 'unselectable' ) || li == this.selected )
				return;

			this.deselect();
			this.selected = li.addClass( 'selected' );
			// Make sure the element is visible.
			liHeight = li.outerHeight();
			elHeight = this.element.height();
			liTop = li.position().top;
			elTop = this.element.scrollTop();

			if ( liTop < 0 ) // Make first visible element.
				this.element.scrollTop( elTop + liTop );
			else if ( liTop + liHeight > elHeight ) // Make last visible element.
				this.element.scrollTop( elTop + liTop - elHeight + liHeight );

			// Trigger the river-select event.
			this.element.trigger( 'river-select', [ li, event, this ] );
		},
		deselect: function() {
			if ( this.selected )
				this.selected.removeClass( 'selected' );
			this.selected = false;
		},
		prev: function() {
			if ( ! this.visible )
				return;

			var to;
			if ( this.selected ) {
				to = this.selected.prev( 'li' );
				if ( to.length )
					this.select( to );
			}
		},
		next: function() {
			if ( ! this.visible )
				return;

			var to = this.selected ? this.selected.next( 'li' ) : $( 'li:not(.unselectable):first', this.element );
			if ( to.length )
				this.select( to );
		},
		ajax: function( callback ) {
			var self = this,
				delay = this.query.page == 1 ? 0 : gcLink.minRiverAJAXDuration,
				response = gcLink.delayedCallback( function( results, params ) {
					self.process( results, params );
					if ( callback )
						callback( results, params );
				}, delay );

			this.query.ajax( response );
		},
		change: function( search ) {
			if ( this.query && this._search == search )
				return;

			this._search = search;
			this.query = new Query( search );
			this.element.scrollTop( 0 );
		},
		process: function( results, params ) {
			var list = '', alt = true, classes = '',
				firstPage = params.page == 1;

			if ( ! results ) {
				if ( firstPage ) {
					list += '<li class="unselectable no-matches-found"><span class="item-title"><em>' +
						gcLinkL10n.noMatchesFound + '</em></span></li>';
				}
			} else {
				$.each( results, function() {
					classes = alt ? 'alternate' : '';
					classes += this.title ? '' : ' no-title';
					list += classes ? '<li class="' + classes + '">' : '<li>';
					list += '<input type="hidden" class="item-permalink" value="' + this.permalink + '" />';
					list += '<span class="item-title">';
					list += this.title ? this.title : gcLinkL10n.noTitle;
					list += '</span><span class="item-info">' + this.info + '</span></li>';
					alt = ! alt;
				});
			}

			this.ul[ firstPage ? 'html' : 'append' ]( list );
		},
		maybeLoad: function() {
			var self = this,
				el = this.element,
				bottom = el.scrollTop() + el.height();

			if ( ! this.query.ready() || bottom < this.contentHeight.height() - gcLink.riverBottomThreshold )
				return;

			setTimeout(function() {
				var newTop = el.scrollTop(),
					newBottom = newTop + el.height();

				if ( ! self.query.ready() || newBottom < self.contentHeight.height() - gcLink.riverBottomThreshold )
					return;

				self.waiting.addClass( 'is-active' );
				el.scrollTop( newTop + self.waiting.outerHeight() );

				self.ajax( function() {
					self.waiting.removeClass( 'is-active' );
				});
			}, gcLink.timeToTriggerRiver );
		}
	});

	Query = function( search ) {
		this.page = 1;
		this.allLoaded = false;
		this.querying = false;
		this.search = search;
	};

	$.extend( Query.prototype, {
		ready: function() {
			return ! ( this.querying || this.allLoaded );
		},
		ajax: function( callback ) {
			var self = this,
				query = {
					action : 'gc-link-ajax',
					page : this.page,
					'_ajax_linking_nonce' : inputs.nonce.val()
				};

			if ( this.search )
				query.search = this.search;

			this.querying = true;

			$.post( window.ajaxurl, query, function( r ) {
				self.page++;
				self.querying = false;
				self.allLoaded = ! r;
				callback( r, query );
			}, 'json' );
		}
	});

	$( gcLink.init );
})( jQuery, window.gcLinkL10n, window.gc );
