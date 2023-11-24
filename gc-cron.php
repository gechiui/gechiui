<?php
/**
 * A pseudo-cron daemon for scheduling GeChiUI tasks.
 *
 * GC-Cron is triggered when the site receives a visit. In the scenario
 * where a site may not receive enough visits to execute scheduled tasks
 * in a timely manner, this file can be called directly or via a server
 * cron daemon for X number of times.
 *
 * Defining DISABLE_GC_CRON as true and calling this file directly are
 * mutually exclusive and the latter does not rely on the former to work.
 *
 * The HTTP request to this file will not slow down the visitor who happens to
 * visit when a scheduled cron event runs.
 *
 * @package GeChiUI
 */

ignore_user_abort( true );

if ( ! headers_sent() ) {
	header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
	header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
}

// Don't run cron until the request finishes, if possible.
if ( PHP_VERSION_ID >= 70016 && function_exists( 'fastcgi_finish_request' ) ) {
	fastcgi_finish_request();
} elseif ( function_exists( 'litespeed_finish_request' ) ) {
	litespeed_finish_request();
}

if ( ! empty( $_POST ) || defined( 'DOING_AJAX' ) || defined( 'DOING_CRON' ) ) {
	die();
}

/**
 * Tell GeChiUI the cron task is running.
 *
 * @var bool
 */
define( 'DOING_CRON', true );

if ( ! defined( 'ABSPATH' ) ) {
	/** Set up GeChiUI environment */
	require_once __DIR__ . '/gc-load.php';
}

// Attempt to raise the PHP memory limit for cron event processing.
gc_raise_memory_limit( 'cron' );

/**
 * Retrieves the cron lock.
 *
 * Returns the uncached `doing_cron` transient.
 *
 * @ignore
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @return string|int|false Value of the `doing_cron` transient, 0|false otherwise.
 */
function _get_cron_lock() {
	global $gcdb;

	$value = 0;
	if ( gc_using_ext_object_cache() ) {
		/*
		 * Skip local cache and force re-fetch of doing_cron transient
		 * in case another process updated the cache.
		 */
		$value = gc_cache_get( 'doing_cron', 'transient', true );
	} else {
		$row = $gcdb->get_row( $gcdb->prepare( "SELECT option_value FROM $gcdb->options WHERE option_name = %s LIMIT 1", '_transient_doing_cron' ) );
		if ( is_object( $row ) ) {
			$value = $row->option_value;
		}
	}

	return $value;
}

$crons = gc_get_ready_cron_jobs();
if ( empty( $crons ) ) {
	die();
}

$gmt_time = microtime( true );

// The cron lock: a unix timestamp from when the cron was spawned.
$doing_cron_transient = get_transient( 'doing_cron' );

// Use global $doing_gc_cron lock, otherwise use the GET lock. If no lock, try to grab a new lock.
if ( empty( $doing_gc_cron ) ) {
	if ( empty( $_GET['doing_gc_cron'] ) ) {
		// Called from external script/job. Try setting a lock.
		if ( $doing_cron_transient && ( $doing_cron_transient + GC_CRON_LOCK_TIMEOUT > $gmt_time ) ) {
			return;
		}
		$doing_gc_cron        = sprintf( '%.22F', microtime( true ) );
		$doing_cron_transient = $doing_gc_cron;
		set_transient( 'doing_cron', $doing_gc_cron );
	} else {
		$doing_gc_cron = $_GET['doing_gc_cron'];
	}
}

/*
 * The cron lock (a unix timestamp set when the cron was spawned),
 * must match $doing_gc_cron (the "key").
 */
if ( $doing_cron_transient !== $doing_gc_cron ) {
	return;
}

foreach ( $crons as $timestamp => $cronhooks ) {
	if ( $timestamp > $gmt_time ) {
		break;
	}

	foreach ( $cronhooks as $hook => $keys ) {

		foreach ( $keys as $k => $v ) {

			$schedule = $v['schedule'];

			if ( $schedule ) {
				$result = gc_reschedule_event( $timestamp, $schedule, $hook, $v['args'], true );

				if ( is_gc_error( $result ) ) {
					error_log(
						sprintf(
							/* translators: 1: Hook name, 2: Error code, 3: Error message, 4: Event data. */
							__( 'Cron 重新计划事件时出现错误，钩子名称：%1$s，错误代码：%2$s，错误消息：%3$s，数据：%4$s' ),
							$hook,
							$result->get_error_code(),
							$result->get_error_message(),
							gc_json_encode( $v )
						)
					);

					/**
					 * Fires when an error happens rescheduling a cron event.
					 *
					 * @since 6.1.0
					 *
					 * @param GC_Error $result The GC_Error object.
					 * @param string   $hook   Action hook to execute when the event is run.
					 * @param array    $v      Event data.
					 */
					do_action( 'cron_reschedule_event_error', $result, $hook, $v );
				}
			}

			$result = gc_unschedule_event( $timestamp, $hook, $v['args'], true );

			if ( is_gc_error( $result ) ) {
				error_log(
					sprintf(
						/* translators: 1: Hook name, 2: Error code, 3: Error message, 4: Event data. */
						__( 'Cron 移出计划事件时出现错误，钩子名称：%1$s，错误代码：%2$s，错误消息：%3$s，数据：%4$s' ),
						$hook,
						$result->get_error_code(),
						$result->get_error_message(),
						gc_json_encode( $v )
					)
				);

				/**
				 * Fires when an error happens unscheduling a cron event.
				 *
				 * @since 6.1.0
				 *
				 * @param GC_Error $result The GC_Error object.
				 * @param string   $hook   Action hook to execute when the event is run.
				 * @param array    $v      Event data.
				 */
				do_action( 'cron_unschedule_event_error', $result, $hook, $v );
			}

			/**
			 * Fires scheduled events.
			 *
			 * @ignore
			 * @since 2.1.0
			 *
			 * @param string $hook Name of the hook that was scheduled to be fired.
			 * @param array  $args The arguments to be passed to the hook.
			 */
			do_action_ref_array( $hook, $v['args'] );

			// If the hook ran too long and another cron process stole the lock, quit.
			if ( _get_cron_lock() !== $doing_gc_cron ) {
				return;
			}
		}
	}
}

if ( _get_cron_lock() === $doing_gc_cron ) {
	delete_transient( 'doing_cron' );
}

die();
