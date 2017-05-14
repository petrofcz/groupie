<?php

namespace PczTests\Groupie;

use Pcz\Groupie\ColumnData;

class CustomColumnData extends ColumnData
{

	protected $color;

	public function __construct($value, $color)
	{
		parent::__construct($value);
		$this->color = $color;
	}

	/**
	 * @return mixed
	 */
	public function getColor()
	{
		return $this->color;
	}

}