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
		return Loader::getCategories($this->getPath());
	}

	public function getTest($number, $categories = [])
	{
		return Loader::init($number, $categories, $this->getPath());
	}

	/**
	 * @return integer
	 */
	public function count()
	{
		return Loader::count($this->getPath());
	}

	/**
	 * @return bool
	 */
	function isCli()
	{
		return (php_sapi_name() === 'cli');
	}

	/**
	 * Returns configuration file path
	 *
	 * @return  String       $path      The configuration filepath
	 */
	public function getPath()
	{
		return ($this->isCli())
			? realpath(dirname(__DIR__).DIRECTORY_SEPARATOR.'../../../app/config/certificationy-cli.yml')
			: realpath(dirname(__DIR__).DIRECTORY_SEPARATOR.'../../../app/config/certificationy.yml');
	}

}