<?php

namespace Erfan\Challenge\Database;


require __DIR__ . "/../../vendor/autoload.php";

$conn = new Connection();

$users = "CREATE TABLE IF NOT EXISTS users (
			id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			username VARCHAR(30) NOT NULL UNIQUE,
			password VARCHAR(255) NOT NULL,
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
			)";

$conn->exec( $users );

$links = "CREATE TABLE IF NOT EXISTS links (
			id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			long_link TEXT NOT NULL,
			short_link TEXT NOT NULL,
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
			)";

$conn->exec( $links );
