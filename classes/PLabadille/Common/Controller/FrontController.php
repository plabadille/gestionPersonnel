<?php

namespace PLabadille\Common\Controller;

class FrontController
{
	protected $request;
	protected $response;
	protected $router;

	public function __construct($request, $response, $router)
	{
		$this->request = $request;
		$this->response = $response;
		$this->router = $router;
	}

	public function execute()
	{
		$router = $this->router;
		$classController = $router->getClassController();
		$action = $router->getAction();

		$controller = new $classController($this->request, $this->response);

		if (method_exists($controller, $action)){
			$controller->$action();
		} else {
			$controller->defaultAction();
		}
	}

}