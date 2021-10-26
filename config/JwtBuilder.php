<?php

namespace Erfan\Challenge\Config;

use Firebase\JWT\JWT;

class JwtBuilder
{

	protected $secret;

	public function __construct ()
	{
		$this->secret = $_ENV[ 'JWT_SECRET' ];
	}

	public function encode ( $user_id )
	{
		$issuedAt       = time();
		$expirationTime = $issuedAt + ( 3600 * 24 );  // jwt valid for 60 seconds from the issued time
		$payload        = [
			'user_id' => $user_id,
			'iat'     => $issuedAt,
			'exp'     => $expirationTime
		];
		$key            = $this->secret;
		$alg            = 'HS256';

		return JWT::encode( $payload, $key, $alg );
	}


	public function decode ( $token )
	{
		return JWT::decode( $token, $this->secret, array( 'HS256' ) );
	}

}