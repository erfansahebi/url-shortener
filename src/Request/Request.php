<?php

namespace Erfan\Challenge\Request;

class Request implements IRequest
{
	function __construct ()
	{
		$this->bootstrapSelf();
	}

	private function bootstrapSelf ()
	{
		foreach ( $_SERVER as $key => $value )
		{
			$this->{$this->toCamelCase( $key )} = $value;
		}
	}

	private function toCamelCase ( $string )
	{
		$result = strtolower( $string );

		preg_match_all( '/_[a-z]/', $result, $matches );

		foreach ( $matches[ 0 ] as $match )
		{
			$c      = str_replace( '_', '', strtoupper( $match ) );
			$result = str_replace( $match, $c, $result );
		}

		return $result;
	}

	public function getBody ()
	{

		if ( $this->requestMethod === "GET" )
		{
			return;
		}

		if ( $this->requestMethod == "POST" )
		{

			$body = [];

			foreach ( $_POST as $key => $value )
			{

				$body[ $key ] = filter_input( INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS );
			}

			return $body;
		}

		if ( $this->requestMethod == "PUT" )
		{
			$body = [];

			parse_str(file_get_contents("php://input"),$post_vars);

			foreach ( $post_vars as $key => $post_var )
			{
				$body[ $key ] = $post_var;
			}

			return $body;
		}

	}

	public function getHeader ()
	{
		return getallheaders();
	}
}