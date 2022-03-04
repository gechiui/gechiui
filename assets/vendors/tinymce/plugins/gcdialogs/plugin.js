/* global tinymce */
/**
 * Included for back-compat.
 * The default WindowManager in TinyMCE 4.0 supports three types of dialogs:
 *	- With HTML created from JS.
 *	- With inline HTML (like GCWindowManager).
 *	- Old type iframe based dialogs.
 * For examples see the default plugins: https://github.com/tinymce/tinymce/tree/master/js/tinymce/plugins
 */
tinymce.GCWindowManager = tinymce.InlineWindowManager = function( editor ) {
	if ( this.gc ) {
		return this;
	}

	this.gc = {};
	this.parent = editor.windowManager;
	this.editor = editor;

	tinymce.extend( this, this.parent );

	this.open = function( args, params ) {
		var $element,
			self = this,
			gc = this.gc;

		if ( ! args.gcDialog ) {
			return this.parent.open.apply( this, arguments );
		} else if ( ! args.id ) {
			return;
		}

		if ( typeof jQuery === 'undefined' || ! jQuery.gc || ! jQuery.gc.gcdialog ) {
			// gcdialog.js is not loaded.
			if ( window.console && window.console.error ) {
				window.console.error('gcdialog.js is not loaded. Please set "gcdialogs" as dependency for your script when calling gc_enqueue_script(). You may also want to enqueue the "gc-jquery-ui-dialog" stylesheet.');
			}

			return;
		}

		gc.$element = $element = jQuery( '#' + args.id );

		if ( ! $element.length ) {
			return;
		}

		if ( window.console && window.console.log ) {
			window.console.log('tinymce.GCWindowManager is deprecated. Use the default editor.windowManager to open dialogs with inline HTML.');
		}

		gc.features = args;
		gc.params = params;

		// Store selection. Takes a snapshot in the FocusManager of the selection before focus is moved to the dialog.
		editor.nodeChanged();

		// Create the dialog if necessary.
		if ( ! $element.data('gcdialog') ) {
			$element.gcdialog({
				title: args.title,
				width: args.width,
				height: args.height,
				modal: true,
				dialogClass: 'gc-dialog',
				zIndex: 300000
			});
		}

		$element.gcdialog('open');

		$element.on( 'gcdialogclose', function() {
			if ( self.gc.$element ) {
				self.gc = {};
			}
		});
	};

	this.close = function() {
		if ( ! this.gc.features || ! this.gc.features.gcDialog ) {
			return this.parent.close.apply( this, arguments );
		}

		this.gc.$element.gcdialog('close');
	};
};

tinymce.PluginManager.add( 'gcdialogs', function( editor ) {
	// Replace window manager.
	editor.on( 'init', function() {
		editor.windowManager = new tinymce.GCWindowManager( editor );
	});
});
