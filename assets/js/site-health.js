/**
 * Interactions used by the Site Health modules in GeChiUI.
 *
 * @output gc-admin/js/site-health.js
 */

/* global ajaxurl, ClipboardJS, SiteHealth, gc */

jQuery( function( $ ) {

	var __ = gc.i18n.__,
		_n = gc.i18n._n,
		sprintf = gc.i18n.sprintf,
		clipboard = new ClipboardJS( '.site-health-copy-buttons .copy-button' ),
		isStatusTab = $( '.health-check-body.health-check-status-tab' ).length,
		isDebugTab = $( '.health-check-body.health-check-debug-tab' ).length,
		pathsSizesSection = $( '#health-check-accordion-block-gc-paths-sizes' ),
		successTimeout;

	// Debug information copy section.
	clipboard.on( 'success', function( e ) {
		var triggerElement = $( e.trigger ),
			successElement = $( '.success', triggerElement.closest( 'div' ) );

		// Clear the selection and move focus back to the trigger.
		e.clearSelection();
		// Handle ClipboardJS focus bug, see https://github.com/zenorocha/clipboard.js/issues/680
		triggerElement.trigger( 'focus' );

		// Show success visual feedback.
		clearTimeout( successTimeout );
		successElement.removeClass( 'hidden' );

		// Hide success visual feedback after 3 seconds since last success.
		successTimeout = setTimeout( function() {
			successElement.addClass( 'hidden' );
			// Remove the visually hidden textarea so that it isn't perceived by assistive technologies.
			if ( clipboard.clipboardAction.fakeElem && clipboard.clipboardAction.removeFake ) {
				clipboard.clipboardAction.removeFake();
			}
		}, 3000 );

		// Handle success audible feedback.
		gc.a11y.speak( __( '站点信息已被复制到您的剪贴板。' ) );
	} );

	// Accordion handling in various areas.
	$( '.health-check-accordion' ).on( 'click', '.health-check-accordion-trigger', function() {
		var isExpanded = ( 'true' === $( this ).attr( 'aria-expanded' ) );

		if ( isExpanded ) {
			$( this ).attr( 'aria-expanded', 'false' );
			$( '#' + $( this ).attr( 'aria-controls' ) ).attr( 'hidden', true );
		} else {
			$( this ).attr( 'aria-expanded', 'true' );
			$( '#' + $( this ).attr( 'aria-controls' ) ).attr( 'hidden', false );
		}
	} );

	// Site Health test handling.

	$( '.site-health-view-passed' ).on( 'click', function() {
		var goodIssuesWrapper = $( '#health-check-issues-good' );

		goodIssuesWrapper.toggleClass( 'hidden' );
		$( this ).attr( 'aria-expanded', ! goodIssuesWrapper.hasClass( 'hidden' ) );
	} );

	/**
	 * Validates the Site Health test result format.
	 *
	 *
	 * @param {Object} issue
	 *
	 * @return {boolean}
	 */
	function validateIssueData( issue ) {
		// Expected minimum format of a valid SiteHealth test response.
		var minimumExpected = {
				test: 'string',
				label: 'string',
				description: 'string'
			},
			passed = true,
			key, value, subKey, subValue;

		// If the issue passed is not an object, return a `false` state early.
		if ( 'object' !== typeof( issue ) ) {
			return false;
		}

		// Loop over expected data and match the data types.
		for ( key in minimumExpected ) {
			value = minimumExpected[ key ];

			if ( 'object' === typeof( value ) ) {
				for ( subKey in value ) {
					subValue = value[ subKey ];

					if ( 'undefined' === typeof( issue[ key ] ) ||
						'undefined' === typeof( issue[ key ][ subKey ] ) ||
						subValue !== typeof( issue[ key ][ subKey ] )
					) {
						passed = false;
					}
				}
			} else {
				if ( 'undefined' === typeof( issue[ key ] ) ||
					value !== typeof( issue[ key ] )
				) {
					passed = false;
				}
			}
		}

		return passed;
	}

	/**
	 * Appends a new issue to the issue list.
	 *
	 *
	 * @param {Object} issue The issue data.
	 */
	function appendIssue( issue ) {
		var template = gc.template( 'health-check-issue' ),
			issueWrapper = $( '#health-check-issues-' + issue.status ),
			heading,
			count;

		/*
		 * Validate the issue data format before using it.
		 * If the output is invalid, discard it.
		 */
		if ( ! validateIssueData( issue ) ) {
			return false;
		}

		SiteHealth.site_status.issues[ issue.status ]++;

		count = SiteHealth.site_status.issues[ issue.status ];

		// If no test name is supplied, append a placeholder for markup references.
		if ( typeof issue.test === 'undefined' ) {
			issue.test = issue.status + count;
		}

		if ( 'critical' === issue.status ) {
			heading = sprintf(
				_n( '%s个关键问题', '%s个关键问题', count ),
				'<span class="issue-count">' + count + '</span>'
			);
		} else if ( 'recommended' === issue.status ) {
			heading = sprintf(
				_n( '%s个推荐的改进', '%s个推荐的改进', count ),
				'<span class="issue-count">' + count + '</span>'
			);
		} else if ( 'good' === issue.status ) {
			heading = sprintf(
				_n( '%s个没有问题的项目', '%s个没有问题的项目', count ),
				'<span class="issue-count">' + count + '</span>'
			);
		}

		if ( heading ) {
			$( '.site-health-issue-count-title', issueWrapper ).html( heading );
		}

		$( '.issues', '#health-check-issues-' + issue.status ).append( template( issue ) );
	}

	/**
	 * Updates site health status indicator as asynchronous tests are run and returned.
	 *
	 */
	function recalculateProgression() {
		var r, c, pct;
		var $progress = $( '.site-health-progress' );
		var $wrapper = $progress.closest( '.site-health-progress-wrapper' );
		var $progressLabel = $( '.site-health-progress-label', $wrapper );
		var $circle = $( '.site-health-progress svg #bar' );
		var totalTests = parseInt( SiteHealth.site_status.issues.good, 0 ) +
			parseInt( SiteHealth.site_status.issues.recommended, 0 ) +
			( parseInt( SiteHealth.site_status.issues.critical, 0 ) * 1.5 );
		var failedTests = ( parseInt( SiteHealth.site_status.issues.recommended, 0 ) * 0.5 ) +
			( parseInt( SiteHealth.site_status.issues.critical, 0 ) * 1.5 );
		var val = 100 - Math.ceil( ( failedTests / totalTests ) * 100 );

		if ( 0 === totalTests ) {
			$progress.addClass( 'hidden' );
			return;
		}

		$wrapper.removeClass( 'loading' );

		r = $circle.attr( 'r' );
		c = Math.PI * ( r * 2 );

		if ( 0 > val ) {
			val = 0;
		}
		if ( 100 < val ) {
			val = 100;
		}

		pct = ( ( 100 - val ) / 100 ) * c + 'px';

		$circle.css( { strokeDashoffset: pct } );

		if ( 1 > parseInt( SiteHealth.site_status.issues.critical, 0 ) ) {
			$( '#health-check-issues-critical' ).addClass( 'hidden' );
		}

		if ( 1 > parseInt( SiteHealth.site_status.issues.recommended, 0 ) ) {
			$( '#health-check-issues-recommended' ).addClass( 'hidden' );
		}

		if ( 80 <= val && 0 === parseInt( SiteHealth.site_status.issues.critical, 0 ) ) {
			$wrapper.addClass( 'green' ).removeClass( 'orange' );

			$progressLabel.text( __( '良好' ) );
			gc.a11y.speak( __( '所有站点健康检查都已完成运行。您的站点看起来不错，您可以在此页查看结果。' ) );
		} else {
			$wrapper.addClass( 'orange' ).removeClass( 'green' );

			$progressLabel.text( __( '有待改进' ) );
			gc.a11y.speak( __( '所有站点健康检查都已完成运行。有一些项目需要您的注意，您可以在此页查看结果。' ) );
		}

		if ( isStatusTab ) {
			$.post(
				ajaxurl,
				{
					'action': 'health-check-site-status-result',
					'_gcnonce': SiteHealth.nonce.site_status_result,
					'counts': SiteHealth.site_status.issues
				}
			);

			if ( 100 === val ) {
				$( '.site-status-all-clear' ).removeClass( 'hide' );
				$( '.site-status-has-issues' ).addClass( 'hide' );
			}
		}
	}

	/**
	 * Queues the next asynchronous test when we're ready to run it.
	 *
	 */
	function maybeRunNextAsyncTest() {
		var doCalculation = true;

		if ( 1 <= SiteHealth.site_status.async.length ) {
			$.each( SiteHealth.site_status.async, function() {
				var data = {
					'action': 'health-check-' + this.test.replace( '_', '-' ),
					'_gcnonce': SiteHealth.nonce.site_status
				};

				if ( this.completed ) {
					return true;
				}

				doCalculation = false;

				this.completed = true;

				if ( 'undefined' !== typeof( this.has_rest ) && this.has_rest ) {
					gc.apiRequest( {
						url: gc.url.addQueryArgs( this.test, { _locale: 'user' } ),
						headers: this.headers
					} )
						.done( function( response ) {
							/** This filter is documented in gc-admin/includes/class-gc-site-health.php */
							appendIssue( gc.hooks.applyFilters( 'site_status_test_result', response ) );
						} )
						.fail( function( response ) {
							var description;

							if ( 'undefined' !== typeof( response.responseJSON ) && 'undefined' !== typeof( response.responseJSON.message ) ) {
								description = response.responseJSON.message;
							} else {
								description = __( '无详细资料可用' );
							}

							addFailedSiteHealthCheckNotice( this.url, description );
						} )
						.always( function() {
							maybeRunNextAsyncTest();
						} );
				} else {
					$.post(
						ajaxurl,
						data
					).done( function( response ) {
						/** This filter is documented in gc-admin/includes/class-gc-site-health.php */
						appendIssue( gc.hooks.applyFilters( 'site_status_test_result', response.data ) );
					} ).fail( function( response ) {
						var description;

						if ( 'undefined' !== typeof( response.responseJSON ) && 'undefined' !== typeof( response.responseJSON.message ) ) {
							description = response.responseJSON.message;
						} else {
							description = __( '无详细资料可用' );
						}

						addFailedSiteHealthCheckNotice( this.url, description );
					} ).always( function() {
						maybeRunNextAsyncTest();
					} );
				}

				return false;
			} );
		}

		if ( doCalculation ) {
			recalculateProgression();
		}
	}

	/**
	 * Add the details of a failed asynchronous test to the list of test results.
	 *
	 */
	function addFailedSiteHealthCheckNotice( url, description ) {
		var issue;

		issue = {
			'status': 'recommended',
			'label': __( '测试不可用' ),
			'badge': {
				'color': 'red',
				'label': __( '不可用' )
			},
			'description': '<p>' + url + '</p><p>' + description + '</p>',
			'actions': ''
		};

		/** This filter is documented in gc-admin/includes/class-gc-site-health.php */
		appendIssue( gc.hooks.applyFilters( 'site_status_test_result', issue ) );
	}

	if ( 'undefined' !== typeof SiteHealth ) {
		if ( 0 === SiteHealth.site_status.direct.length && 0 === SiteHealth.site_status.async.length ) {
			recalculateProgression();
		} else {
			SiteHealth.site_status.issues = {
				'good': 0,
				'recommended': 0,
				'critical': 0
			};
		}

		if ( 0 < SiteHealth.site_status.direct.length ) {
			$.each( SiteHealth.site_status.direct, function() {
				appendIssue( this );
			} );
		}

		if ( 0 < SiteHealth.site_status.async.length ) {
			maybeRunNextAsyncTest();
		} else {
			recalculateProgression();
		}
	}

	function getDirectorySizes() {
		var timestamp = ( new Date().getTime() );

		// After 3 seconds announce that we're still waiting for directory sizes.
		var timeout = window.setTimeout( function() {
			gc.a11y.speak( __( '请稍候…' ) );
		}, 3000 );

		gc.apiRequest( {
			path: '/gc-site-health/v1/directory-sizes'
		} ).done( function( response ) {
			updateDirSizes( response || {} );
		} ).always( function() {
			var delay = ( new Date().getTime() ) - timestamp;

			$( '.health-check-gc-paths-sizes.spinner' ).css( 'visibility', 'hidden' );
			recalculateProgression();

			if ( delay > 3000 ) {
				/*
				 * We have announced that we're waiting.
				 * Announce that we're ready after giving at least 3 seconds
				 * for the first announcement to be read out, or the two may collide.
				 */
				if ( delay > 6000 ) {
					delay = 0;
				} else {
					delay = 6500 - delay;
				}

				window.setTimeout( function() {
					gc.a11y.speak( __( '所有站点健康检查都已完成运行。' ) );
				}, delay );
			} else {
				// Cancel the announcement.
				window.clearTimeout( timeout );
			}

			$( document ).trigger( 'site-health-info-dirsizes-done' );
		} );
	}

	function updateDirSizes( data ) {
		var copyButton = $( 'button.button.copy-button' );
		var clipboardText = copyButton.attr( 'data-clipboard-text' );

		$.each( data, function( name, value ) {
			var text = value.debug || value.size;

			if ( typeof text !== 'undefined' ) {
				clipboardText = clipboardText.replace( name + ': loading...', name + ': ' + text );
			}
		} );

		copyButton.attr( 'data-clipboard-text', clipboardText );

		pathsSizesSection.find( 'td[class]' ).each( function( i, element ) {
			var td = $( element );
			var name = td.attr( 'class' );

			if ( data.hasOwnProperty( name ) && data[ name ].size ) {
				td.text( data[ name ].size );
			}
		} );
	}

	if ( isDebugTab ) {
		if ( pathsSizesSection.length ) {
			getDirectorySizes();
		} else {
			recalculateProgression();
		}
	}

	// Trigger a class toggle when the extended menu button is clicked.
	$( '.health-check-offscreen-nav-wrapper' ).on( 'click', function() {
		$( this ).toggleClass( 'visible' );
	} );
} );
