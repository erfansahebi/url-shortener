<?php

namespace Erfan\Challenge\Controller\Link;

use Erfan\Challenge\Database\Connection;
use Erfan\Challenge\Service\Link;
use PDOException;

class LinkController
{

	public function list ( $request )
	{
		$connection      = new Connection();
		$current_user_id = $request->payload->user_id;

		$list_query = 'SELECT id,long_link,short_link,created_at FROM `links` WHERE user_id = ? ORDER BY created_at DESC';

		$result = $connection->get_result( $connection->prepare( $list_query ), $current_user_id );

		return json_encode( [
			"status" => true,
			"data"   => $result
		] );
	}

	public function create ( $request )
	{
		$current_user_id = $request->payload->user_id;

		$body = $request->getBody();

		$validate = true;

		$link = ! empty( $body[ 'link' ] ) ? $body[ 'link' ] : null;

		$errors = [];

		if ( empty( $link ) )
		{
			$validate = false;
			array_push( $errors, 'Link is required' );
		}

		if ( $validate )
		{
			$link_service = new Link();

			$short_link = $link_service->return_if_exist( $link );

			if ( ! $short_link )
			{
				$short_link = $link_service->generate_link();
				$status     = $link_service->save( $link, $short_link, $current_user_id );
				if ( $status !== true )
					return json_encode( [
						"status" => false,
						"errors" => [ $status ]
					] );
			}

			return json_encode( [
				"status" => true,
				"data"   => [
					"id"  => $link,
					"long_link"  => $link,
					"short_link" => $short_link
				]
			] );

		}

		return json_encode( [
			"status" => false,
			"errors" => $errors
		] );
	}

	public function edit ( $request, $link_id )
	{
		$current_user_id = $request->payload->user_id;
		$body            = $request->getBody();
		$validate        = true;

		$link_id = ! empty( $link_id ) && is_int( (int) $link_id ) ? abs( $link_id ) : null;
		$link    = ! empty( $body[ 'link' ] ) ? $body[ 'link' ] : null;

		$errors = [];

		if ( empty( $link_id ) )
		{
			$validate = false;
			array_push( $errors, 'link_id is required and must be integer' );
		}

		if ( $validate )
		{
			$connection = new Connection();

			$check_query = "SELECT * FROM `links` WHERE user_id = ? AND id = ?";

			$check = $connection->get_row( $connection->prepare( $check_query ), $current_user_id, $link_id );

			if ( ! $check )
			{
				return json_encode( [
					"status" => true,
					"errors" => [
						"Not Allowed!"
					]
				] );
			}

			try
			{
				$connection->beginTransaction();

				$update_query = "UPDATE `links` SET long_link = ? WHERE id = ?";

				$connection->update( $connection->prepare( $update_query ), $link, $link_id );

				$connection->commit();
			}
			catch ( PDOException $exception )
			{
				$connection->rollback();

				return json_encode( [
					"status" => false,
					"errors" => $exception->getMessage()
				] );
			}

			return json_encode( [
				"status" => true
			] );
		}

		return json_encode( [
			"status" => false,
			"errors" => $errors
		] );

	}

	public function delete ( $request, $link_id )
	{
		$validate = true;

		$link_id = ! empty( $link_id ) && is_int( (int) $link_id ) ? abs( $link_id ) : null;

		$errors = [];

		if ( empty( $link_id ) )
		{
			$validate = false;
			array_push( $errors, 'link_id is required and must be integer' );
		}

		if ( $validate )
		{

			$current_user_id = $request->payload->user_id;

			$connection = new Connection();

			$delete_query = "DELETE FROM `links` 
								WHERE id = ?
									AND user_id = ?";

			try
			{
				$connection->delete( $connection->prepare( $delete_query ), $link_id, $current_user_id );

				return json_encode( [
					"status" => true
				] );
			}
			catch ( PDOException $exception )
			{
				array_push( $errors, $exception->getMessage() );
			}

		}

		return json_encode( [
			"status" => false,
			"errors" => $errors
		] );

	}

	public function translate ( $request, $short_link )
	{
		$connection = new Connection();

		$find_query = "SELECT long_link FROM `links` WHERE short_link = ?";

		$result = $connection->get_row( $connection->prepare( $find_query ), $short_link );

		if ( ! empty( $result ) )
		{
			return json_encode( [
				"status" => true,
				"data"   => [
					"long_link"  => $result[ 'long_link' ],
					"short_link" => $short_link,
				]
			] );
		}

		return json_encode( [
			"status" => false,
			"errors" => [
				"Not found!"
			]
		] );

	}

}