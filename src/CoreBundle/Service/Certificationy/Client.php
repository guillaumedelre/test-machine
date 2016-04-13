<?php

namespace CoreBundle\Service\Certificationy;


use Certificationy\Certification\Loader;

class Client
{
	/**
	 * @return array
	 */
	public function getCategories()
	{
		return Loader::getCategories($this->path());
	}

	public function launch($number, $categories = [])
	{
		return Loader::init($number, $categories, $this->path());
	}

	/**
	 * @return integer
	 */
	public function count()
	{
		return Loader::count($this->path());
	}

	/**
	 * Returns configuration file path
	 *
	 * @return  String       $path      The configuration filepath
	 */
	protected function path()
	{
		return realpath(dirname(__DIR__).DIRECTORY_SEPARATOR.'../../../app/config/certificationy.yml');
	}

}