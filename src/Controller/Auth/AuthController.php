<?php

namespace Erfan\Challenge\Controller\Auth;

use Erfan\Challenge\Config\JwtBuilder;
use Erfan\Challenge\Database\Connection;

class AuthController
{

	public function login ( $request )
	{
		$body = $request->getBody();

		$validate = true;

		$username = ! empty( $body[ 'username' ] ) ? $body[ 'username' ] : null;
		$password = ! empty( $body[ 'password' ] ) ? md5( $body[ 'password' ] ) : null;

		$errors = [];

		if ( empty( $username ) )
		{
			$validate = false;
			array_push( $errors, 'username is required' );
		}
		if ( empty( $password ) )
		{
			$validate = false;
			array_push( $errors, 'password is required' );
		}

		if ( $validate )
		{

			$db = new Connection();

			$find_query = "SELECT id, username, password
							FROM `users`
							WHERE username = ?";


			$result = $db->get_row( $db->prepare( $find_query ), $username );

			if ( empty( $result ) )
			{

				try
				{
					$db->beginTransaction();

					$insert_query = "INSERT INTO `users` (username, password)
												VALUES (?, ?)";

					$user_id = $db->insert( $db->prepare( $insert_query ), $username, $password );

					$db->commit();
				}
				catch ( PDOException $exception )
				{
					$db->rollback();

					return json_encode( [
						"status" => false,
						"errors" => $exception->getMessage()
					] );
				}

			}
			else
			{

				if ( $result[ 'password' ] != $password )
				{
					return json_encode( [
						"status" => false,
						"errors" => [
							"username or password is wrong!"
						]
					] );
				}

				$user_id = $result[ 'id' ];
			}

			$jwt_builder = new JwtBuilder();

			return json_encode( [
				"status" => true,
				"data"   => [
					"token" => $jwt_builder->encode( $user_id )
				]
			] );

		}

		return json_encode( [
			"status" => false,
			"errors" => $errors
		] );
	}
}