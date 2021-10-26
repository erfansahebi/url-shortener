<?php

namespace Erfan\Challenge\Database;

use PDO;

class Connection
{

	public $conn;

	public function __construct ()
	{
		$this->conn = new PDO( "mysql:host=db;port=3306;dbname={$_ENV['DB_NAME']}", $_ENV[ 'DB_USERNAME' ], $_ENV[ 'DB_PASSWORD' ] );
		$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}

	public function exec ( $sql )
	{
		return $this->conn->exec( $sql );
	}

	public function rollback ()
	{
		return $this->conn->rollBack();
	}

	public function beginTransaction ()
	{
		return $this->conn->beginTransaction();
	}

	public function commit ()
	{
		return $this->conn->commit();
	}

	public function prepare ( $sql )
	{
		return $this->conn->prepare( $sql );
	}

	public function get_result ( $stmt, ...$args )
	{
		$stmt->execute( $args );
		$stmt->setFetchMode( PDO::FETCH_ASSOC );

		return $stmt->fetchAll();
	}

	public function get_row ( $stmt, ...$args )
	{
		$stmt->execute( $args );
		$stmt->setFetchMode( PDO::FETCH_ASSOC );

		return $stmt->fetch();
	}

	public function insert ( $stmt, ...$args )
	{
		$stmt->execute( $args );

		return $this->conn->lastInsertId();
	}

	public function update ( $stmt, ...$args )
	{
		$stmt->execute( $args );

		return $stmt->rowCount();
	}

	public function delete ( $stmt, ...$args )
	{
		$stmt->execute( $args );

		return true;
	}

	public function __destruct ()
	{
		$this->conn = null;
	}

}