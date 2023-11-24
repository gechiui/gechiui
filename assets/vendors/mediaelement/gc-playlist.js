/* global _gcmejsSettings, MediaElementPlayer */

(function ($, _, Backbone) {
	'use strict';

	/** @namespace gc */
	window.gc = window.gc || {};

	var GCPlaylistView = Backbone.View.extend(/** @lends GCPlaylistView.prototype */{
		/**
		 * @constructs
		 *
		 * @param {Object} options          The options to create this playlist view with.
		 * @param {Object} options.metadata The metadata
		 */
		initialize : function (options) {
			this.index = 0;
			this.settings = {};
			this.data = options.metadata || $.parseJSON( this.$('script.gc-playlist-script').html() );
			this.playerNode = this.$( this.data.type );

			this.tracks = new Backbone.Collection( this.data.tracks );
			this.current = this.tracks.first();

			if ( 'audio' === this.data.type ) {
				this.currentTemplate = gc.template( 'gc-playlist-current-item' );
				this.currentNode = this.$( '.gc-playlist-current-item' );
			}

			this.renderCurrent();

			if ( this.data.tracklist ) {
				this.itemTemplate = gc.template( 'gc-playlist-item' );
				this.playingClass = 'gc-playlist-playing';
				this.renderTracks();
			}

			this.playerNode.attr( 'src', this.current.get( 'src' ) );

			_.bindAll( this, 'bindPlayer', 'bindResetPlayer', 'setPlayer', 'ended', 'clickTrack' );

			if ( ! _.isUndefined( window._gcmejsSettings ) ) {
				this.settings = _.clone( _gcmejsSettings );
			}
			this.settings.success = this.bindPlayer;
			this.setPlayer();
		},

		bindPlayer : function (mejs) {
			this.mejs = mejs;
			this.mejs.addEventListener( 'ended', this.ended );
		},

		bindResetPlayer : function (mejs) {
			this.bindPlayer( mejs );
			this.playCurrentSrc();
		},

		setPlayer: function (force) {
			if ( this.player ) {
				this.player.pause();
				this.player.remove();
				this.playerNode = this.$( this.data.type );
			}

			if (force) {
				this.playerNode.attr( 'src', this.current.get( 'src' ) );
				this.settings.success = this.bindResetPlayer;
			}

			// This is also our bridge to the outside world.
			this.player = new MediaElementPlayer( this.playerNode.get(0), this.settings );
		},

		playCurrentSrc : function () {
			this.renderCurrent();
			this.mejs.setSrc( this.playerNode.attr( 'src' ) );
			this.mejs.load();
			this.mejs.play();
		},

		renderCurrent : function () {
			var dimensions, defaultImage = 'assets/images/media/video.png';
			if ( 'video' === this.data.type ) {
				if ( this.data.images && this.current.get( 'image' ) && -1 === this.current.get( 'image' ).src.indexOf( defaultImage ) ) {
					this.playerNode.attr( 'poster', this.current.get( 'image' ).src );
				}
				dimensions = this.current.get( 'dimensions' );
				if ( dimensions && dimensions.resized ) {
					this.playerNode.attr( dimensions.resized );
				}
			} else {
				if ( ! this.data.images ) {
					this.current.set( 'image', false );
				}
				this.currentNode.html( this.currentTemplate( this.current.toJSON() ) );
			}
		},

		renderTracks : function () {
			var self = this, i = 1, tracklist = $( '<div class="gc-playlist-tracks"></div>' );
			this.tracks.each(function (model) {
				if ( ! self.data.images ) {
					model.set( 'image', false );
				}
				model.set( 'artists', self.data.artists );
				model.set( 'index', self.data.tracknumbers ? i : false );
				tracklist.append( self.itemTemplate( model.toJSON() ) );
				i += 1;
			});
			this.$el.append( tracklist );

			this.$( '.gc-playlist-item' ).eq(0).addClass( this.playingClass );
		},

		events : {
			'click .gc-playlist-item' : 'clickTrack',
			'click .gc-playlist-next' : 'next',
			'click .gc-playlist-prev' : 'prev'
		},

		clickTrack : function (e) {
			e.preventDefault();

			this.index = this.$( '.gc-playlist-item' ).index( e.currentTarget );
			this.setCurrent();
		},

		ended : function () {
			if ( this.index + 1 < this.tracks.length ) {
				this.next();
			} else {
				this.index = 0;
				this.setCurrent();
			}
		},

		next : function () {
			this.index = this.index + 1 >= this.tracks.length ? 0 : this.index + 1;
			this.setCurrent();
		},

		prev : function () {
			this.index = this.index - 1 < 0 ? this.tracks.length - 1 : this.index - 1;
			this.setCurrent();
		},

		loadCurrent : function () {
			var last = this.playerNode.attr( 'src' ) && this.playerNode.attr( 'src' ).split('.').pop(),
				current = this.current.get( 'src' ).split('.').pop();

			this.mejs && this.mejs.pause();

			if ( last !== current ) {
				this.setPlayer( true );
			} else {
				this.playerNode.attr( 'src', this.current.get( 'src' ) );
				this.playCurrentSrc();
			}
		},

		setCurrent : function () {
			this.current = this.tracks.at( this.index );

			if ( this.data.tracklist ) {
				this.$( '.gc-playlist-item' )
					.removeClass( this.playingClass )
					.eq( this.index )
						.addClass( this.playingClass );
			}

			this.loadCurrent();
		}
	});

	/**
	 * Initialize media playlists in the document.
	 *
	 * Only initializes new playlists not previously-initialized.
	 *
	 * @since 4.9.3
	 * @return {void}
	 */
	function initialize() {
		$( '.gc-playlist:not(:has(.mejs-container))' ).each( function() {
			new GCPlaylistView( { el: this } );
		} );
	}

	/**
	 * Expose the API publicly on window.gc.playlist.
	 *
	 * @namespace gc.playlist
	 * @since 4.9.3
	 * @type {object}
	 */
	window.gc.playlist = {
		initialize: initialize
	};

	$( document ).ready( initialize );

	window.GCPlaylistView = GCPlaylistView;

}(jQuery, _, Backbone));
