<?php

namespace Api\Controllers;

use \App\Controller\Base as BaseController,
	\App\Exception\Http as HttpException;

class TestController extends BaseController
{
	protected $exampleRecords = array(
		array('id' => 1, 'name' => 'Ariel', 'location' => 'Under The Sea', 'prince_name' => 'Eric', 'popular' => 'false'),
		array('id' => 2, 'name' => 'Snow White', 'location' => 'Forest', 'prince_name' => 'The Prince', 'popular' => 'true'),
		array('id' => 3, 'name' => 'Belle', 'location' => 'France', 'prince_name' => 'The Beast', 'popular' => 'false'),
		array('id' => 4, 'name' => 'Nala', 'location' => 'Pride Rock', 'prince_name' => 'Simba', 'popular' => 'true'),
		array('id' => 5, 'name' => 'Sleeping Beauty', 'location' => 'Castle', 'prince_name' => 'Charming', 'popular' => 'true'),
		array('id' => 6, 'name' => 'Jasmine', 'location' => 'Aghraba', 'prince_name' => 'Aladdin', 'popular' => 'true'),
		array('id' => 7, 'name' => 'Mulan', 'location' => 'China', 'prince_name' => 'Li Shang', 'popular' => 'false'),
	);

	/**
	 * {@inheritdoc}
	 */
	protected $allowedFields = array(
		'search'	=> array(),
		'partials'	=> array('id'),
	);

	public function getAction()
	{
#		d('hello');
#		d($this->respond(($this->isSearch) ? $this->search() : $this->exampleRecords));
#		throw new \Exception('testing1');
#		throw new HttpException('testing2');

#		return $this->respond(($this->isSearch) ? $this->search() : $this->exampleRecords);
		return $this->respond(($this->isSearch) ? $this->search() : $this->exampleRecords);
	}

	public function getOne($id)
	{
		$id--;
		return (@count($this->exampleRecords[$id])) ? $this->respond($this->exampleRecords[$id]) : $this->respond(array());
	}

	public function postAction()
	{
		return array('Post / stub');
	}

	public function deleteAction($id) {
		return array('Delete / stub');
	}

	public function putAction($id)
	{
		return array('Put / stub');
	}

	public function patchAction($id)
	{
		return array('Patch / stub');
	}
}
