/**
 * @output gc-includes/js/media-editor.js
 */

/* global getUserSetting, tinymce, QTags */

// GeChiUI, TinyMCE, and Media
// -----------------------------
(function($, _){
	/**
	 * Stores the editors' `gc.media.controller.Frame` instances.
	 *
	 * @static
	 */
	var workflows = {};

	/**
	 * A helper mixin function to avoid truthy and falsey values being
	 *   passed as an input that expects booleans. If key is undefined in the map,
	 *   but has a default value, set it.
	 *
	 * @param {Object} attrs Map of props from a shortcode or settings.
	 * @param {string} key The key within the passed map to check for a value.
	 * @return {mixed|undefined} The original or coerced value of key within attrs.
	 */
	gc.media.coerce = function ( attrs, key ) {
		if ( _.isUndefined( attrs[ key ] ) && ! _.isUndefined( this.defaults[ key ] ) ) {
			attrs[ key ] = this.defaults[ key ];
		} else if ( 'true' === attrs[ key ] ) {
			attrs[ key ] = true;
		} else if ( 'false' === attrs[ key ] ) {
			attrs[ key ] = false;
		}
		return attrs[ key ];
	};

	/** @namespace gc.media.string */
	gc.media.string = {
		/**
		 * Joins the `props` and `attachment` objects,
		 * outputting the proper object format based on the
		 * attachment's type.
		 *
		 * @param {Object} [props={}] Attachment details (align, link, size, etc).
		 * @param {Object} attachment The attachment object, media version of Post.
		 * @return {Object} Joined props
		 */
		props: function( props, attachment ) {
			var link, linkUrl, size, sizes,
				defaultProps = gc.media.view.settings.defaultProps;

			props = props ? _.clone( props ) : {};

			if ( attachment && attachment.type ) {
				props.type = attachment.type;
			}

			if ( 'image' === props.type ) {
				props = _.defaults( props || {}, {
					align:   defaultProps.align || getUserSetting( 'align', 'none' ),
					size:    defaultProps.size  || getUserSetting( 'imgsize', 'medium' ),
					url:     '',
					classes: []
				});
			}

			// All attachment-specific settings follow.
			if ( ! attachment ) {
				return props;
			}

			props.title = props.title || attachment.title;

			link = props.link || defaultProps.link || getUserSetting( 'urlbutton', 'file' );
			if ( 'file' === link || 'embed' === link ) {
				linkUrl = attachment.url;
			} else if ( 'post' === link ) {
				linkUrl = attachment.link;
			} else if ( 'custom' === link ) {
				linkUrl = props.linkUrl;
			}
			props.linkUrl = linkUrl || '';

			// Format properties for images.
			if ( 'image' === attachment.type ) {
				props.classes.push( 'gc-image-' + attachment.id );

				sizes = attachment.sizes;
				size = sizes && sizes[ props.size ] ? sizes[ props.size ] : attachment;

				_.extend( props, _.pick( attachment, 'align', 'caption', 'alt' ), {
					width:     size.width,
					height:    size.height,
					src:       size.url,
					captionId: 'attachment_' + attachment.id
				});
			} else if ( 'video' === attachment.type || 'audio' === attachment.type ) {
				_.extend( props, _.pick( attachment, 'title', 'type', 'icon', 'mime' ) );
			// Format properties for non-images.
			} else {
				props.title = props.title || attachment.filename;
				props.rel = props.rel || 'attachment gc-att-' + attachment.id;
			}

			return props;
		},
		/**
		 * Create link markup that is suitable for passing to the editor
		 *
		 * @param {Object} props Attachment details (align, link, size, etc).
		 * @param {Object} attachment The attachment object, media version of Post.
		 * @return {string} The link markup
		 */
		link: function( props, attachment ) {
			var options;

			props = gc.media.string.props( props, attachment );

			options = {
				tag:     'a',
				content: props.title,
				attrs:   {
					href: props.linkUrl
				}
			};

			if ( props.rel ) {
				options.attrs.rel = props.rel;
			}

			return gc.html.string( options );
		},
		/**
		 * Create an Audio shortcode string that is suitable for passing to the editor
		 *
		 * @param {Object} props Attachment details (align, link, size, etc).
		 * @param {Object} attachment The attachment object, media version of Post.
		 * @return {string} The audio shortcode
		 */
		audio: function( props, attachment ) {
			return gc.media.string._audioVideo( 'audio', props, attachment );
		},
		/**
		 * Create a Video shortcode string that is suitable for passing to the editor
		 *
		 * @param {Object} props Attachment details (align, link, size, etc).
		 * @param {Object} attachment The attachment object, media version of Post.
		 * @return {string} The video shortcode
		 */
		video: function( props, attachment ) {
			return gc.media.string._audioVideo( 'video', props, attachment );
		},
		/**
		 * Helper function to create a media shortcode string
		 *
		 * @access private
		 *
		 * @param {string} type The shortcode tag name: 'audio' or 'video'.
		 * @param {Object} props Attachment details (align, link, size, etc).
		 * @param {Object} attachment The attachment object, media version of Post.
		 * @return {string} The media shortcode
		 */
		_audioVideo: function( type, props, attachment ) {
			var shortcode, html, extension;

			props = gc.media.string.props( props, attachment );
			if ( props.link !== 'embed' ) {
				return gc.media.string.link( props );
			}

			shortcode = {};

			if ( 'video' === type ) {
				if ( attachment.image && -1 === attachment.image.src.indexOf( attachment.icon ) ) {
					shortcode.poster = attachment.image.src;
				}

				if ( attachment.width ) {
					shortcode.width = attachment.width;
				}

				if ( attachment.height ) {
					shortcode.height = attachment.height;
				}
			}

			extension = attachment.filename.split('.').pop();

			if ( _.contains( gc.media.view.settings.embedExts, extension ) ) {
				shortcode[extension] = attachment.url;
			} else {
				// Render unsupported audio and video files as links.
				return gc.media.string.link( props );
			}

			html = gc.shortcode.string({
				tag:     type,
				attrs:   shortcode
			});

			return html;
		},
		/**
		 * Create image markup, optionally with a link and/or wrapped in a caption shortcode,
		 *  that is suitable for passing to the editor
		 *
		 * @param {Object} props Attachment details (align, link, size, etc).
		 * @param {Object} attachment The attachment object, media version of Post.
		 * @return {string}
		 */
		image: function( props, attachment ) {
			var img = {},
				options, classes, shortcode, html;

			props.type = 'image';
			props = gc.media.string.props( props, attachment );
			classes = props.classes || [];

			img.src = ! _.isUndefined( attachment ) ? attachment.url : props.url;
			_.extend( img, _.pick( props, 'width', 'height', 'alt' ) );

			// Only assign the align class to the image if we're not printing
			// a caption, since the alignment is sent to the shortcode.
			if ( props.align && ! props.caption ) {
				classes.push( 'align' + props.align );
			}

			if ( props.size ) {
				classes.push( 'size-' + props.size );
			}

			img['class'] = _.compact( classes ).join(' ');

			// Generate `img` tag options.
			options = {
				tag:    'img',
				attrs:  img,
				single: true
			};

			// Generate the `a` element options, if they exist.
			if ( props.linkUrl ) {
				options = {
					tag:   'a',
					attrs: {
						href: props.linkUrl
					},
					content: options
				};
			}

			html = gc.html.string( options );

			// Generate the caption shortcode.
			if ( props.caption ) {
				shortcode = {};

				if ( img.width ) {
					shortcode.width = img.width;
				}

				if ( props.captionId ) {
					shortcode.id = props.captionId;
				}

				if ( props.align ) {
					shortcode.align = 'align' + props.align;
				}

				html = gc.shortcode.string({
					tag:     'caption',
					attrs:   shortcode,
					content: html + ' ' + props.caption
				});
			}

			return html;
		}
	};

	gc.media.embed = {
		coerce : gc.media.coerce,

		defaults : {
			url : '',
			width: '',
			height: ''
		},

		edit : function( data, isURL ) {
			var frame, props = {}, shortcode;

			if ( isURL ) {
				props.url = data.replace(/<[^>]+>/g, '');
			} else {
				shortcode = gc.shortcode.next( 'embed', data ).shortcode;

				props = _.defaults( shortcode.attrs.named, this.defaults );
				if ( shortcode.content ) {
					props.url = shortcode.content;
				}
			}

			frame = gc.media({
				frame: 'post',
				state: 'embed',
				metadata: props
			});

			return frame;
		},

		shortcode : function( model ) {
			var self = this, content;

			_.each( this.defaults, function( value, key ) {
				model[ key ] = self.coerce( model, key );

				if ( value === model[ key ] ) {
					delete model[ key ];
				}
			});

			content = model.url;
			delete model.url;

			return new gc.shortcode({
				tag: 'embed',
				attrs: model,
				content: content
			});
		}
	};

	/**
	 * @class gc.media.collection
	 *
	 * @param {Object} attributes
	 */
	gc.media.collection = function(attributes) {
		var collections = {};

		return _.extend(/** @lends gc.media.collection.prototype */{
			coerce : gc.media.coerce,
			/**
			 * Retrieve attachments based on the properties of the passed shortcode
			 *
			 * @param {gc.shortcode} shortcode An instance of gc.shortcode().
			 * @return {gc.media.model.Attachments} A Backbone.Collection containing
			 *                                      the media items belonging to a collection.
			 *                                      The query[ this.tag ] property is a Backbone.Model
			 *                                      containing the 'props' for the collection.
			 */
			attachments: function( shortcode ) {
				var shortcodeString = shortcode.string(),
					result = collections[ shortcodeString ],
					attrs, args, query, others, self = this;

				delete collections[ shortcodeString ];
				if ( result ) {
					return result;
				}
				// Fill the default shortcode attributes.
				attrs = _.defaults( shortcode.attrs.named, this.defaults );
				args  = _.pick( attrs, 'orderby', 'order' );

				args.type    = this.type;
				args.perPage = -1;

				// Mark the `orderby` override attribute.
				if ( undefined !== attrs.orderby ) {
					attrs._orderByField = attrs.orderby;
				}

				if ( 'rand' === attrs.orderby ) {
					attrs._orderbyRandom = true;
				}

				// Map the `orderby` attribute to the corresponding model property.
				if ( ! attrs.orderby || /^menu_order(?: ID)?$/i.test( attrs.orderby ) ) {
					args.orderby = 'menuOrder';
				}

				// Map the `ids` param to the correct query args.
				if ( attrs.ids ) {
					args.post__in = attrs.ids.split(',');
					args.orderby  = 'post__in';
				} else if ( attrs.include ) {
					args.post__in = attrs.include.split(',');
				}

				if ( attrs.exclude ) {
					args.post__not_in = attrs.exclude.split(',');
				}

				if ( ! args.post__in ) {
					args.uploadedTo = attrs.id;
				}

				// Collect the attributes that were not included in `args`.
				others = _.omit( attrs, 'id', 'ids', 'include', 'exclude', 'orderby', 'order' );

				_.each( this.defaults, function( value, key ) {
					others[ key ] = self.coerce( others, key );
				});

				query = gc.media.query( args );
				query[ this.tag ] = new Backbone.Model( others );
				return query;
			},
			/**
			 * Triggered when clicking 'Insert {label}' or 'Update {label}'
			 *
			 * @param {gc.media.model.Attachments} attachments A Backbone.Collection containing
			 *      the media items belonging to a collection.
			 *      The query[ this.tag ] property is a Backbone.Model
			 *          containing the 'props' for the collection.
			 * @return {gc.shortcode}
			 */
			shortcode: function( attachments ) {
				var props = attachments.props.toJSON(),
					attrs = _.pick( props, 'orderby', 'order' ),
					shortcode, clone;

				if ( attachments.type ) {
					attrs.type = attachments.type;
					delete attachments.type;
				}

				if ( attachments[this.tag] ) {
					_.extend( attrs, attachments[this.tag].toJSON() );
				}

				/*
				 * Convert all gallery shortcodes to use the `ids` property.
				 * Ignore `post__in` and `post__not_in`; the attachments in
				 * the collection will already reflect those properties.
				 */
				attrs.ids = attachments.pluck('id');

				// Copy the `uploadedTo` post ID.
				if ( props.uploadedTo ) {
					attrs.id = props.uploadedTo;
				}
				// Check if the gallery is randomly ordered.
				delete attrs.orderby;

				if ( attrs._orderbyRandom ) {
					attrs.orderby = 'rand';
				} else if ( attrs._orderByField && 'rand' !== attrs._orderByField ) {
					attrs.orderby = attrs._orderByField;
				}

				delete attrs._orderbyRandom;
				delete attrs._orderByField;

				// If the `ids` attribute is set and `orderby` attribute
				// is the default value, clear it for cleaner output.
				if ( attrs.ids && 'post__in' === attrs.orderby ) {
					delete attrs.orderby;
				}

				attrs = this.setDefaults( attrs );

				shortcode = new gc.shortcode({
					tag:    this.tag,
					attrs:  attrs,
					type:   'single'
				});

				// Use a cloned version of the gallery.
				clone = new gc.media.model.Attachments( attachments.models, {
					props: props
				});
				clone[ this.tag ] = attachments[ this.tag ];
				collections[ shortcode.string() ] = clone;

				return shortcode;
			},
			/**
			 * Triggered when double-clicking a collection shortcode placeholder
			 *   in the editor
			 *
			 * @param {string} content Content that is searched for possible
			 *    shortcode markup matching the passed tag name,
			 *
			 * @this gc.media.{prop}
			 *
			 * @return {gc.media.view.MediaFrame.Select} A media workflow.
			 */
			edit: function( content ) {
				var shortcode = gc.shortcode.next( this.tag, content ),
					defaultPostId = this.defaults.id,
					attachments, selection, state;

				// Bail if we didn't match the shortcode or all of the content.
				if ( ! shortcode || shortcode.content !== content ) {
					return;
				}

				// Ignore the rest of the match object.
				shortcode = shortcode.shortcode;

				if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) ) {
					shortcode.set( 'id', defaultPostId );
				}

				attachments = this.attachments( shortcode );

				selection = new gc.media.model.Selection( attachments.models, {
					props:    attachments.props.toJSON(),
					multiple: true
				});

				selection[ this.tag ] = attachments[ this.tag ];

				// Fetch the query's attachments, and then break ties from the
				// query to allow for sorting.
				selection.more().done( function() {
					// Break ties with the query.
					selection.props.set({ query: false });
					selection.unmirror();
					selection.props.unset('orderby');
				});

				// Destroy the previous gallery frame.
				if ( this.frame ) {
					this.frame.dispose();
				}

				if ( shortcode.attrs.named.type && 'video' === shortcode.attrs.named.type ) {
					state = 'video-' + this.tag + '-edit';
				} else {
					state = this.tag + '-edit';
				}

				// Store the current frame.
				this.frame = gc.media({
					frame:     'post',
					state:     state,
					title:     this.editTitle,
					editing:   true,
					multiple:  true,
					selection: selection
				}).open();

				return this.frame;
			},

			setDefaults: function( attrs ) {
				var self = this;
				// Remove default attributes from the shortcode.
				_.each( this.defaults, function( value, key ) {
					attrs[ key ] = self.coerce( attrs, key );
					if ( value === attrs[ key ] ) {
						delete attrs[ key ];
					}
				});

				return attrs;
			}
		}, attributes );
	};

	gc.media._galleryDefaults = {
		itemtag: 'dl',
		icontag: 'dt',
		captiontag: 'dd',
		columns: '3',
		link: 'post',
		size: 'thumbnail',
		order: 'ASC',
		id: gc.media.view.settings.post && gc.media.view.settings.post.id,
		orderby : 'menu_order ID'
	};

	if ( gc.media.view.settings.galleryDefaults ) {
		gc.media.galleryDefaults = _.extend( {}, gc.media._galleryDefaults, gc.media.view.settings.galleryDefaults );
	} else {
		gc.media.galleryDefaults = gc.media._galleryDefaults;
	}

	gc.media.gallery = new gc.media.collection({
		tag: 'gallery',
		type : 'image',
		editTitle : gc.media.view.l10n.editGalleryTitle,
		defaults : gc.media.galleryDefaults,

		setDefaults: function( attrs ) {
			var self = this, changed = ! _.isEqual( gc.media.galleryDefaults, gc.media._galleryDefaults );
			_.each( this.defaults, function( value, key ) {
				attrs[ key ] = self.coerce( attrs, key );
				if ( value === attrs[ key ] && ( ! changed || value === gc.media._galleryDefaults[ key ] ) ) {
					delete attrs[ key ];
				}
			} );
			return attrs;
		}
	});

	/**
	 * @namespace gc.media.featuredImage
	 * @memberOf gc.media
	 */
	gc.media.featuredImage = {
		/**
		 * Get the featured image post ID
		 *
		 * @return {gc.media.view.settings.post.featuredImageId|number}
		 */
		get: function() {
			return gc.media.view.settings.post.featuredImageId;
		},
		/**
		 * Sets the featured image ID property and sets the HTML in the post meta box to the new featured image.
		 *
		 * @param {number} id The post ID of the featured image, or -1 to unset it.
		 */
		set: function( id ) {
			var settings = gc.media.view.settings;

			settings.post.featuredImageId = id;

			gc.media.post( 'get-post-thumbnail-html', {
				post_id:      settings.post.id,
				thumbnail_id: settings.post.featuredImageId,
				_gcnonce:     settings.post.nonce
			}).done( function( html ) {
				if ( '0' === html ) {
					window.alert( gc.i18n.__( '无法设置为缩略图。请尝试其他附件。' ) );
					return;
				}
				$( '.inside', '#postimagediv' ).html( html );
			});
		},
		/**
		 * Remove the featured image id, save the post thumbnail data and
		 * set the HTML in the post meta box to no featured image.
		 */
		remove: function() {
			gc.media.featuredImage.set( -1 );
		},
		/**
		 * The Featured Image workflow
		 *
		 * @this gc.media.featuredImage
		 *
		 * @return {gc.media.view.MediaFrame.Select} A media workflow.
		 */
		frame: function() {
			if ( this._frame ) {
				gc.media.frame = this._frame;
				return this._frame;
			}

			this._frame = gc.media({
				state: 'featured-image',
				states: [ new gc.media.controller.FeaturedImage() , new gc.media.controller.EditImage() ]
			});

			this._frame.on( 'toolbar:create:featured-image', function( toolbar ) {
				/**
				 * @this gc.media.view.MediaFrame.Select
				 */
				this.createSelectToolbar( toolbar, {
					text: gc.media.view.l10n.setFeaturedImage
				});
			}, this._frame );

			this._frame.on( 'content:render:edit-image', function() {
				var selection = this.state('featured-image').get('selection'),
					view = new gc.media.view.EditImage( { model: selection.single(), controller: this } ).render();

				this.content.set( view );

				// After bringing in the frame, load the actual editor via an Ajax call.
				view.loadEditor();

			}, this._frame );

			this._frame.state('featured-image').on( 'select', this.select );
			return this._frame;
		},
		/**
		 * 'select' callback for Featured Image workflow, triggered when
		 *  the 'Set Featured Image' button is clicked in the media modal.
		 *
		 * @this gc.media.controller.FeaturedImage
		 */
		select: function() {
			var selection = this.get('selection').single();

			if ( ! gc.media.view.settings.post.featuredImageId ) {
				return;
			}

			gc.media.featuredImage.set( selection ? selection.id : -1 );
		},
		/**
		 * Open the content media manager to the '特色图片' tab when
		 * the post thumbnail is clicked.
		 *
		 * Update the featured image id when the 'remove' link is clicked.
		 */
		init: function() {
			$('#postimagediv').on( 'click', '#set-post-thumbnail', function( event ) {
				event.preventDefault();
				// Stop propagation to prevent thickbox from activating.
				event.stopPropagation();

				gc.media.featuredImage.frame().open();
			}).on( 'click', '#remove-post-thumbnail', function() {
				gc.media.featuredImage.remove();
				return false;
			});
		}
	};

	$( gc.media.featuredImage.init );

	/** @namespace gc.media.editor */
	gc.media.editor = {
		/**
		 * Send content to the editor
		 *
		 * @param {string} html Content to send to the editor
		 */
		insert: function( html ) {
			var editor, gcActiveEditor,
				hasTinymce = ! _.isUndefined( window.tinymce ),
				hasQuicktags = ! _.isUndefined( window.QTags );

			if ( this.activeEditor ) {
				gcActiveEditor = window.gcActiveEditor = this.activeEditor;
			} else {
				gcActiveEditor = window.gcActiveEditor;
			}

			/*
			 * Delegate to the global `send_to_editor` if it exists.
			 * This attempts to play nice with any themes/plugins
			 * that have overridden the insert functionality.
			 */
			if ( window.send_to_editor ) {
				return window.send_to_editor.apply( this, arguments );
			}

			if ( ! gcActiveEditor ) {
				if ( hasTinymce && tinymce.activeEditor ) {
					editor = tinymce.activeEditor;
					gcActiveEditor = window.gcActiveEditor = editor.id;
				} else if ( ! hasQuicktags ) {
					return false;
				}
			} else if ( hasTinymce ) {
				editor = tinymce.get( gcActiveEditor );
			}

			if ( editor && ! editor.isHidden() ) {
				editor.execCommand( 'mceInsertContent', false, html );
			} else if ( hasQuicktags ) {
				QTags.insertContent( html );
			} else {
				document.getElementById( gcActiveEditor ).value += html;
			}

			// If the old thickbox remove function exists, call it in case
			// a theme/plugin overloaded it.
			if ( window.tb_remove ) {
				try { window.tb_remove(); } catch( e ) {}
			}
		},

		/**
		 * Setup 'workflow' and add to the 'workflows' cache. 'open' can
		 *  subsequently be called upon it.
		 *
		 * @param {string} id A slug used to identify the workflow.
		 * @param {Object} [options={}]
		 *
		 * @this gc.media.editor
		 *
		 * @return {gc.media.view.MediaFrame.Select} A media workflow.
		 */
		add: function( id, options ) {
			var workflow = this.get( id );

			// Only add once: if exists return existing.
			if ( workflow ) {
				return workflow;
			}

			workflow = workflows[ id ] = gc.media( _.defaults( options || {}, {
				frame:    'post',
				state:    'insert',
				title:    gc.media.view.l10n.addMedia,
				multiple: true
			} ) );

			workflow.on( 'insert', function( selection ) {
				var state = workflow.state();

				selection = selection || state.get('selection');

				if ( ! selection ) {
					return;
				}

				$.when.apply( $, selection.map( function( attachment ) {
					var display = state.display( attachment ).toJSON();
					/**
					 * @this gc.media.editor
					 */
					return this.send.attachment( display, attachment.toJSON() );
				}, this ) ).done( function() {
					gc.media.editor.insert( _.toArray( arguments ).join('\n\n') );
				});
			}, this );

			workflow.state('gallery-edit').on( 'update', function( selection ) {
				/**
				 * @this gc.media.editor
				 */
				this.insert( gc.media.gallery.shortcode( selection ).string() );
			}, this );

			workflow.state('playlist-edit').on( 'update', function( selection ) {
				/**
				 * @this gc.media.editor
				 */
				this.insert( gc.media.playlist.shortcode( selection ).string() );
			}, this );

			workflow.state('video-playlist-edit').on( 'update', function( selection ) {
				/**
				 * @this gc.media.editor
				 */
				this.insert( gc.media.playlist.shortcode( selection ).string() );
			}, this );

			workflow.state('embed').on( 'select', function() {
				/**
				 * @this gc.media.editor
				 */
				var state = workflow.state(),
					type = state.get('type'),
					embed = state.props.toJSON();

				embed.url = embed.url || '';

				if ( 'link' === type ) {
					_.defaults( embed, {
						linkText: embed.url,
						linkUrl: embed.url
					});

					this.send.link( embed ).done( function( resp ) {
						gc.media.editor.insert( resp );
					});

				} else if ( 'image' === type ) {
					_.defaults( embed, {
						title:   embed.url,
						linkUrl: '',
						align:   'none',
						link:    'none'
					});

					if ( 'none' === embed.link ) {
						embed.linkUrl = '';
					} else if ( 'file' === embed.link ) {
						embed.linkUrl = embed.url;
					}

					this.insert( gc.media.string.image( embed ) );
				}
			}, this );

			workflow.state('featured-image').on( 'select', gc.media.featuredImage.select );
			workflow.setState( workflow.options.state );
			return workflow;
		},
		/**
		 * Determines the proper current workflow id
		 *
		 * @param {string} [id=''] A slug used to identify the workflow.
		 *
		 * @return {gcActiveEditor|string|tinymce.activeEditor.id}
		 */
		id: function( id ) {
			if ( id ) {
				return id;
			}

			// If an empty `id` is provided, default to `gcActiveEditor`.
			id = window.gcActiveEditor;

			// If that doesn't work, fall back to `tinymce.activeEditor.id`.
			if ( ! id && ! _.isUndefined( window.tinymce ) && tinymce.activeEditor ) {
				id = tinymce.activeEditor.id;
			}

			// Last but not least, fall back to the empty string.
			id = id || '';
			return id;
		},
		/**
		 * Return the workflow specified by id
		 *
		 * @param {string} id A slug used to identify the workflow.
		 *
		 * @this gc.media.editor
		 *
		 * @return {gc.media.view.MediaFrame} A media workflow.
		 */
		get: function( id ) {
			id = this.id( id );
			return workflows[ id ];
		},
		/**
		 * Remove the workflow represented by id from the workflow cache
		 *
		 * @param {string} id A slug used to identify the workflow.
		 *
		 * @this gc.media.editor
		 */
		remove: function( id ) {
			id = this.id( id );
			delete workflows[ id ];
		},
		/** @namespace gc.media.editor.send */
		send: {
			/**
			 * Called when sending an attachment to the editor
			 *   from the medial modal.
			 *
			 * @param {Object} props Attachment details (align, link, size, etc).
			 * @param {Object} attachment The attachment object, media version of Post.
			 * @return {Promise}
			 */
			attachment: function( props, attachment ) {
				var caption = attachment.caption,
					options, html;

				// If captions are disabled, clear the caption.
				if ( ! gc.media.view.settings.captions ) {
					delete attachment.caption;
				}

				props = gc.media.string.props( props, attachment );

				options = {
					id:           attachment.id,
					post_content: attachment.description,
					post_excerpt: caption
				};

				if ( props.linkUrl ) {
					options.url = props.linkUrl;
				}

				if ( 'image' === attachment.type ) {
					html = gc.media.string.image( props );

					_.each({
						align: 'align',
						size:  'image-size',
						alt:   'image_alt'
					}, function( option, prop ) {
						if ( props[ prop ] ) {
							options[ option ] = props[ prop ];
						}
					});
				} else if ( 'video' === attachment.type ) {
					html = gc.media.string.video( props, attachment );
				} else if ( 'audio' === attachment.type ) {
					html = gc.media.string.audio( props, attachment );
				} else {
					html = gc.media.string.link( props );
					options.post_title = props.title;
				}

				return gc.media.post( 'send-attachment-to-editor', {
					nonce:      gc.media.view.settings.nonce.sendToEditor,
					attachment: options,
					html:       html,
					post_id:    gc.media.view.settings.post.id
				});
			},
			/**
			 * Called when 'Insert From URL' source is not an image. Example: YouTube url.
			 *
			 * @param {Object} embed
			 * @return {Promise}
			 */
			link: function( embed ) {
				return gc.media.post( 'send-link-to-editor', {
					nonce:     gc.media.view.settings.nonce.sendToEditor,
					src:       embed.linkUrl,
					link_text: embed.linkText,
					html:      gc.media.string.link( embed ),
					post_id:   gc.media.view.settings.post.id
				});
			}
		},
		/**
		 * Open a workflow
		 *
		 * @param {string} [id=undefined] Optional. A slug used to identify the workflow.
		 * @param {Object} [options={}]
		 *
		 * @this gc.media.editor
		 *
		 * @return {gc.media.view.MediaFrame}
		 */
		open: function( id, options ) {
			var workflow;

			options = options || {};

			id = this.id( id );
			this.activeEditor = id;

			workflow = this.get( id );

			// Redo workflow if state has changed.
			if ( ! workflow || ( workflow.options && options.state !== workflow.options.state ) ) {
				workflow = this.add( id, options );
			}

			gc.media.frame = workflow;

			return workflow.open();
		},

		/**
		 * Bind click event for .insert-media using event delegation
		 */
		init: function() {
			$(document.body)
				.on( 'click.add-media-button', '.insert-media', function( event ) {
					var elem = $( event.currentTarget ),
						editor = elem.data('editor'),
						options = {
							frame:    'post',
							state:    'insert',
							title:    gc.media.view.l10n.addMedia,
							multiple: true
						};

					event.preventDefault();

					if ( elem.hasClass( 'gallery' ) ) {
						options.state = 'gallery';
						options.title = gc.media.view.l10n.createGalleryTitle;
					}

					gc.media.editor.open( editor, options );
				});

			// Initialize and render the Editor drag-and-drop uploader.
			new gc.media.view.EditorUploader().render();
		}
	};

	_.bindAll( gc.media.editor, 'open' );
	$( gc.media.editor.init );
}(jQuery, _));
