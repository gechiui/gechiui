/* global tinymce */
tinymce.PluginManager.add('gcgallery', function( editor ) {

	function replaceGalleryShortcodes( content ) {
		return content.replace( /\[gallery([^\]]*)\]/g, function( match ) {
			return html( 'gc-gallery', match );
		});
	}

	function html( cls, data ) {
		data = window.encodeURIComponent( data );
		return '<img src="' + tinymce.Env.transparentSrc + '" class="gc-media mceItem ' + cls + '" ' +
			'data-gc-media="' + data + '" data-mce-resize="false" data-mce-placeholder="1" alt="" />';
	}

	function restoreMediaShortcodes( content ) {
		function getAttr( str, name ) {
			name = new RegExp( name + '=\"([^\"]+)\"' ).exec( str );
			return name ? window.decodeURIComponent( name[1] ) : '';
		}

		return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
			var data = getAttr( image, 'data-gc-media' );

			if ( data ) {
				return '<p>' + data + '</p>';
			}

			return match;
		});
	}

	function editMedia( node ) {
		var gallery, frame, data;

		if ( node.nodeName !== 'IMG' ) {
			return;
		}

		// Check if the `gc.media` API exists.
		if ( typeof gc === 'undefined' || ! gc.media ) {
			return;
		}

		data = window.decodeURIComponent( editor.dom.getAttrib( node, 'data-gc-media' ) );

		// Make sure we've selected a gallery node.
		if ( editor.dom.hasClass( node, 'gc-gallery' ) && gc.media.gallery ) {
			gallery = gc.media.gallery;
			frame = gallery.edit( data );

			frame.state('gallery-edit').on( 'update', function( selection ) {
				var shortcode = gallery.shortcode( selection ).string();
				editor.dom.setAttrib( node, 'data-gc-media', window.encodeURIComponent( shortcode ) );
				frame.detach();
			});
		}
	}

	// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('...').
	editor.addCommand( 'GC_Gallery', function() {
		editMedia( editor.selection.getNode() );
	});

	editor.on( 'mouseup', function( event ) {
		var dom = editor.dom,
			node = event.target;

		function unselect() {
			dom.removeClass( dom.select( 'img.gc-media-selected' ), 'gc-media-selected' );
		}

		if ( node.nodeName === 'IMG' && dom.getAttrib( node, 'data-gc-media' ) ) {
			// Don't trigger on right-click.
			if ( event.button !== 2 ) {
				if ( dom.hasClass( node, 'gc-media-selected' ) ) {
					editMedia( node );
				} else {
					unselect();
					dom.addClass( node, 'gc-media-selected' );
				}
			}
		} else {
			unselect();
		}
	});

	// Display gallery, audio or video instead of img in the element path.
	editor.on( 'ResolveName', function( event ) {
		var dom = editor.dom,
			node = event.target;

		if ( node.nodeName === 'IMG' && dom.getAttrib( node, 'data-gc-media' ) ) {
			if ( dom.hasClass( node, 'gc-gallery' ) ) {
				event.name = 'gallery';
			}
		}
	});

	editor.on( 'BeforeSetContent', function( event ) {
		// 'gcview' handles the gallery shortcode when present.
		if ( ! editor.plugins.gcview || typeof gc === 'undefined' || ! gc.mce ) {
			event.content = replaceGalleryShortcodes( event.content );
		}
	});

	editor.on( 'PostProcess', function( event ) {
		if ( event.get ) {
			event.content = restoreMediaShortcodes( event.content );
		}
	});
});
