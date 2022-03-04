<?php
/**
 * Send XML response back to Ajax request.
 *
 * @package GeChiUI
 *
 */
class GC_Ajax_Response {
	/**
	 * Store XML responses to send.
	 *
	 * @var array
	 */
	public $responses = array();

	/**
	 * Constructor - Passes args to GC_Ajax_Response::add().
	 *
	 *
	 * @see GC_Ajax_Response::add()
	 *
	 * @param string|array $args Optional. Will be passed to add() method.
	 */
	public function __construct( $args = '' ) {
		if ( ! empty( $args ) ) {
			$this->add( $args );
		}
	}

	/**
	 * Appends data to an XML response based on given arguments.
	 *
	 * With `$args` defaults, extra data output would be:
	 *
	 *     <response action='{$action}_$id'>
	 *      <$what id='$id' position='$position'>
	 *          <response_data><![CDATA[$data]]></response_data>
	 *      </$what>
	 *     </response>
	 *
	 *
	 * @param string|array $args {
	 *     Optional. An array or string of XML response arguments.
	 *
	 *     @type string          $what         XML-RPC response type. Used as a child element of `<response>`.
	 *                                         Default 'object' (`<object>`).
	 *     @type string|false    $action       Value to use for the `action` attribute in `<response>`. Will be
	 *                                         appended with `_$id` on output. If false, `$action` will default to
	 *                                         the value of `$_POST['action']`. Default false.
	 *     @type int|GC_Error    $id           The response ID, used as the response type `id` attribute. Also
	 *                                         accepts a `GC_Error` object if the ID does not exist. Default 0.
	 *     @type int|false       $old_id       The previous response ID. Used as the value for the response type
	 *                                         `old_id` attribute. False hides the attribute. Default false.
	 *     @type string          $position     Value of the response type `position` attribute. Accepts 1 (bottom),
	 *                                         -1 (top), HTML ID (after), or -HTML ID (before). Default 1 (bottom).
	 *     @type string|GC_Error $data         The response content/message. Also accepts a GC_Error object if the
	 *                                         ID does not exist. Default empty.
	 *     @type array           $supplemental An array of extra strings that will be output within a `<supplemental>`
	 *                                         element as CDATA. Default empty array.
	 * }
	 * @return string XML response.
	 */
	public function add( $args = '' ) {
		$defaults = array(
			'what'         => 'object',
			'action'       => false,
			'id'           => '0',
			'old_id'       => false,
			'position'     => 1,
			'data'         => '',
			'supplemental' => array(),
		);

		$parsed_args = gc_parse_args( $args, $defaults );

		$position = preg_replace( '/[^a-z0-9:_-]/i', '', $parsed_args['position'] );
		$id       = $parsed_args['id'];
		$what     = $parsed_args['what'];
		$action   = $parsed_args['action'];
		$old_id   = $parsed_args['old_id'];
		$data     = $parsed_args['data'];

		if ( is_gc_error( $id ) ) {
			$data = $id;
			$id   = 0;
		}

		$response = '';
		if ( is_gc_error( $data ) ) {
			foreach ( (array) $data->get_error_codes() as $code ) {
				$response  .= "<gc_error code='$code'><![CDATA[" . $data->get_error_message( $code ) . ']]></gc_error>';
				$error_data = $data->get_error_data( $code );
				if ( ! $error_data ) {
					continue;
				}
				$class = '';
				if ( is_object( $error_data ) ) {
					$class      = ' class="' . get_class( $error_data ) . '"';
					$error_data = get_object_vars( $error_data );
				}

				$response .= "<gc_error_data code='$code'$class>";

				if ( is_scalar( $error_data ) ) {
					$response .= "<![CDATA[$error_data]]>";
				} elseif ( is_array( $error_data ) ) {
					foreach ( $error_data as $k => $v ) {
						$response .= "<$k><![CDATA[$v]]></$k>";
					}
				}

				$response .= '</gc_error_data>';
			}
		} else {
			$response = "<response_data><![CDATA[$data]]></response_data>";
		}

		$s = '';
		if ( is_array( $parsed_args['supplemental'] ) ) {
			foreach ( $parsed_args['supplemental'] as $k => $v ) {
				$s .= "<$k><![CDATA[$v]]></$k>";
			}
			$s = "<supplemental>$s</supplemental>";
		}

		if ( false === $action ) {
			$action = $_POST['action'];
		}
		$x  = '';
		$x .= "<response action='{$action}_$id'>"; // The action attribute in the xml output is formatted like a nonce action.
		$x .= "<$what id='$id' " . ( false === $old_id ? '' : "old_id='$old_id' " ) . "position='$position'>";
		$x .= $response;
		$x .= $s;
		$x .= "</$what>";
		$x .= '</response>';

		$this->responses[] = $x;
		return $x;
	}

	/**
	 * Display XML formatted responses.
	 *
	 * Sets the content type header to text/xml.
	 *
	 */
	public function send() {
		header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ) );
		echo "<?xml version='1.0' encoding='" . get_option( 'blog_charset' ) . "' standalone='yes'?><gc_ajax>";
		foreach ( (array) $this->responses as $response ) {
			echo $response;
		}
		echo '</gc_ajax>';
		if ( gc_doing_ajax() ) {
			gc_die();
		} else {
			die();
		}
	}
}
