<?php
/**
 * Exception for 306 Switch Proxy responses
 *
 * @package Requests\Exceptions
 */

namespace GcOrg\Requests\Exception\Http;

use GcOrg\Requests\Exception\Http;

/**
 * Exception for 306 Switch Proxy responses
 *
 * @package Requests\Exceptions
 */
final class Status306 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 306;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Switch Proxy';
}
