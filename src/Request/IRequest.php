<?php

namespace Erfan\Challenge\Request;

interface IRequest
{
	public function getHeader ();

	public function getBody ();
}