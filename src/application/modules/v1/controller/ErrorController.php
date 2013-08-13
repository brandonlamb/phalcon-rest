<?php

namespace Api\Controller;

use \App\Controller\Base as BaseController,
	\App\Exception\Http as HttpException;

class ErrorController extends BaseController
{
	public function notfoundAction()
	{
		throw new HttpException('Resource not found', 404);
	}
}
