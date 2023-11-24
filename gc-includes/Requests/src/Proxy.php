<?php
/**
 * Proxy connection interface
 *
 * @package Requests\Proxy
 * @since   1.6
 */

namespace GcOrg\Requests;

use GcOrg\Requests\Hooks;

/**
 * Proxy connection interface
 *
 * Implement this interface to handle proxy settings and authentication
 *
 * Parameters should be passed via the constructor where possible, as this
 * makes it much easier for users to use your provider.
 *
 * @see \GcOrg\Requests\Hooks
 *
 * @package Requests\Proxy
 * @since   1.6
 */
interface Proxy {
	/**
	 * Register hooks as needed
	 *
	 * This method is called in {@see \GcOrg\Requests\Requests::request()} when the user
	 * has set an instance as the 'auth' option. Use this callback to register all the
	 * hooks you'll need.
	 *
	 * @see \GcOrg\Requests\Hooks::register()
	 * @param \GcOrg\Requests\Hooks $hooks Hook system
	 */
	public function register(Hooks $hooks);
}
