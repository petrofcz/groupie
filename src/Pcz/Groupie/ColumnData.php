<?php

namespace Pcz\Groupie;

/**
 * This class represents base column data (single cell).
 * It's possible to override it and add other attributes (eg. color, ...)
 */
class ColumnData implements IColumnData
{
	/** @var  mixed Cell value. */
	protected $value;

	/**
	 * ColumnData constructor.
	 * @param mixed $value
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	function __toString()
	{
		return (string)$this->value;
	}

}