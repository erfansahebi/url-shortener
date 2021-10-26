<?php

namespace Erfan\Challenge\Middleware;

use Erfan\Challenge\Config\JwtBuilder;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class Jwt
{

	public function __construct ()
	{

	}

	/**
	 * @throws \Exception
	 */
	public function validate ( $request )
	{
		$header = $request->getHeader();

		if ( ! isset( $header[ 'Authorization' ] ) )
		{
			echo json_encode( [
				"status" => false,
				"errors" => [ "Bearer token not exist!" ]
			] );

			return null;
		}

		$authorization = str_replace( "Bearer ", "", $header[ 'Authorization' ] );

		$jwtBuilder = new JwtBuilder();

		try
		{
			return $jwtBuilder->decode( $authorization );
		}
		catch ( ExpiredException | InvalidArgumentException | UnexpectedValueException | SignatureInvalidException | BeforeValidException  $exception )
		{
			echo json_encode( [
				"status" => false,
				"errors" => $exception->getMessage()
			] );
		}

		return null;
	}

}