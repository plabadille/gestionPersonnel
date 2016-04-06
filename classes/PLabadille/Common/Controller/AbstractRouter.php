<?php

namespace PLabadille\Common\Controller;

abstract class AbstractRouter
{
	protected $request;

	public function __construct($request)
	{
		$this->request = $request;
	}

	abstract public function getClassController();

	abstract public function getAction();
}