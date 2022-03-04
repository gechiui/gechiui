<?php
/**
 * IXR_ClientMulticall
 *
 * @package IXR
 *
 */
class IXR_ClientMulticall extends IXR_Client
{
    var $calls = array();

	/**
	 * PHP5 constructor.
	 */
    function __construct( $server, $path = false, $port = 80 )
    {
        parent::IXR_Client($server, $path, $port);
        $this->useragent = 'The Incutio XML-RPC PHP Library (multicall client)';
    }

	/**
	 * PHP4 constructor.
	 */
	public function IXR_ClientMulticall( $server, $path = false, $port = 80 ) {
		self::__construct( $server, $path, $port );
	}

	/**
	 *              to the function signature.
	 */
    function addCall( ...$args )
    {
        $methodName = array_shift($args);
        $struct = array(
            'methodName' => $methodName,
            'params' => $args
        );
        $this->calls[] = $struct;
    }

	/**
	 *              to the function signature.
	 *
	 * @return bool
	 */
    function query( ...$args )
    {
        // Prepare multicall, then call the parent::query() method
        return parent::query('system.multicall', $this->calls);
    }
}
