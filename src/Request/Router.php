<?php

namespace Erfan\Challenge\Request;

use Erfan\Challenge\Middleware\Jwt_middleware;

class Router
{
	private $request;
	private $supportedHttpMethods = [
		"GET",
		"POST",
		"DELETE",
		"PUT"
	];

	function __construct ( IRequest $request )
	{
		$this->request = $request;
	}

	function __call ( $name, $args )
	{
		list( $route, $method, $middleware ) = $args;

		if ( ! in_array( strtoupper( $name ), $this->supportedHttpMethods ) )
		{
			$this->invalidMethodHandler();
		}

		$this->{strtolower( $name )}[ $this->formatRoute( $route ) ] = [
			"method"     => $method,
			"middleware" => $middleware
		];
	}

	private function invalidMethodHandler ()
	{
		header( "{$this->request->serverProtocol} 405 Method Not Allowed" );
	}

	/**
	 * Removes trailing forward slashes from the right of the route.
	 *
	 * @param route (string)
	 */
	private function formatRoute ( $route )
	{
		return $route;
	}

	function __destruct ()
	{
		$this->resolve();
	}

	/**
	 * Resolves a route
	 */
	function resolve ()
	{
		$methodDictionary = $this->{strtolower( $this->request->requestMethod )};
		$formatedRoute    = $this->formatRoute( $this->request->requestUri );
		$method           = null;

		foreach ( $methodDictionary as $key => $item )
		{
			if ( preg_match( $key, $formatedRoute, $matches ) )
			{
				$method     = $item[ "method" ];
				$middleware = $item[ "middleware" ];

				foreach ( $matches as $key_match => $match )
				{
					if ( is_int( $key_match ) )
						unset( $matches[ $key_match ] );
				}

				$params = $matches;

				break;
			}
		}


		if ( ! empty( $middleware ) )
		{
			$res = call_user_func_array( $middleware, array( $this->request ) );

			if ( empty( $res ) )
				return;

			$this->request->payload = $res;
		}

		if ( is_null( $method ) )
		{
			$this->defaultRequestHandler();

			return;
		}

		$args = array_merge( [ $this->request ], $params );

		echo call_user_func_array( $method, $args );
	}

	private function defaultRequestHandler ()
	{
		header( "{$this->request->serverProtocol} 404 Not Found" );
	}
}