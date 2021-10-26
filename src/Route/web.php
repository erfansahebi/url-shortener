<?php

use Erfan\Challenge\Controller\Auth\AuthController;
use Erfan\Challenge\Controller\Link\LinkController;
use Erfan\Challenge\Middleware\Jwt;
use Erfan\Challenge\Request\Request;
use Erfan\Challenge\Request\Router;

$router = new Router( new Request );

$router->post( '/^\/login/', [
	AuthController::class,
	'login'
] );

$router->get( '/^\/link$/', [
	LinkController::class,
	'list'
], [
	Jwt::class,
	'validate'
] );

$router->post( '/^\/link$/', [
	LinkController::class,
	'create'
], [
	Jwt::class,
	'validate'
] );

$router->put( '/^\/link\/(?P<link_id>.+)$/', [
	LinkController::class,
	'edit'
], [
	Jwt::class,
	'validate'
] );

$router->delete( '/^\/link\/(?P<link_id>.+)$/', [
	LinkController::class,
	'delete'
], [
	Jwt::class,
	'validate'
] );

$router->get( '/^\/(?P<link_id>.+)$/', [
	LinkController::class,
	'translate'
]);

