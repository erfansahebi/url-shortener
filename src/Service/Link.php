<?php

namespace Erfan\Challenge\Service;

use Erfan\Challenge\Database\Connection;

class Link
{

	protected $short_link;

	public function __construct ()
	{
		$this->conn = new Connection();
	}

	public function return_if_exist ( $link )
	{

		$find_query = "SELECT * FROM `links` WHERE long_link = ?";

		$result = $this->conn->get_row( $this->conn->prepare( $find_query ), $link );

		if ( empty( $result ) )
			return false;

		return $result[ 'short_link' ];
	}


	public function generate_link ()
	{
		while ( true )
		{
			$this->short_link = $this->generate_random_string( 6 );

			if ( $this->validate() )
			{
				break;
			}
		}

		return $this->short_link;
	}

	public function generate_random_string ( $length = 10 )
	{
		$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString     = '';
		for ( $i = 0; $i < $length; $i ++ )
		{
			$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
		}

		return $randomString;
	}

	public function validate ()
	{
		$find_query = "SELECT * FROM `links` WHERE short_link = ?";

		$result = $this->conn->get_row( $this->conn->prepare( $find_query ), $this->short_link );

		if ( empty( $result ) )
			return true;

		return false;
	}

	public function save ( $long_link, $short_link, $user_id )
	{
		try
		{
			$this->conn->beginTransaction();

			$insert_query = "INSERT INTO `links` (short_link, long_link, user_id)
												VALUES (?, ?, ?)";

			$this->conn->insert( $this->conn->prepare( $insert_query ), $short_link, $long_link, $user_id );


			$this->conn->commit();

			return true;
		}
		catch ( PDOException $exception )
		{
			$this->conn->rollback();

			return $exception->getMessage();
		}

	}

}