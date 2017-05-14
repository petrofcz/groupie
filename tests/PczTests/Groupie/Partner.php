<?php

namespace PczTests\Groupie;

class Partner
{
	/** @var  string */
	protected $name;

	/**
	 * Category constructor.
	 * @param $name string
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/** @return string */
	public function getId() {
		return md5($this->name);
	}

}