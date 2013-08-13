<?php

namespace Api\Controller\LocationGroup\Instance;

use \App\Controller\Base as BaseController,
	\App\Exception\Http as HttpException;

class HeaderController extends BaseController
{
	protected $exampleRecords = array(
		array('id' => 1, 'name' => 'Ariel', 'location' => 'Under The Sea', 'prince_name' => 'Eric', 'popular' => 'false'),
	);

	/**
	 * {@inheritdoc}
	 */
	protected $allowedFields = array(
		'search'	=> array('name', 'popular', 'prince_name'),
		'partials'	=> array('id', 'name'),
	);

	public function getAction()
	{
		throw new HttpException('testing2');
		return $this->respond(($this->isSearch) ? $this->search() : $this->exampleRecords);
	}

	public function getOne($id)
	{
		$id--;
		return (@count($this->exampleRecords[$id])) ? $this->respond($this->exampleRecords[$id]) : $this->respond(array());
	}
}
