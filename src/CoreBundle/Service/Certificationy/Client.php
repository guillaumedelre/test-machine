<?php

namespace CoreBundle\Service\Certificationy;


use Certificationy\Certification\Loader;
use Certificationy\Certification\Set;
use CoreBundle\Entity\AbstractEntity;
use Symfony\Component\HttpFoundation\Request;

class Client
{
	/**
	 * @return array
	 */
	public function getCategories()
	{
		return Loader::getCategories($this->getPath());
	}

	/**
	 * @param $number
	 * @param array $categories
	 * @return \Certificationy\Certification\Set
	 */
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

	/**
	 * @param Request $request
	 * @param AbstractEntity $test
	 * @return array
	 */
	public function validate(Request $request, AbstractEntity $test)
	{
		$results = [];

		/** @var Set $set */
		$set = unserialize($test->getData());

		foreach($set->getQuestions() as $key => $question) {
			if (array_key_exists($key, $request->request->get('answers'))) {

				$answers = $request->request->get('answers')[$key];
				$set->setAnswer($key, $answers);

				$isCorrect = $set->isCorrect($key);

				$results[$key] = array(
					sprintf('%s', $question->getQuestion()),
					$question->getCorrectAnswersValues(),
					$isCorrect
				);
			}
		}

		return $results;
	}

}