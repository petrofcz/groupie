<?php

namespace Pcz\Groupie;

/**
 * This class represents a column definition. Feel free to override it and add extra attributes (eg. description)
 */
class ColumnDefinition
{

	/** @var string Column caption */
	protected $caption;

	/**
	 * Column constructor.
	 * @param string $caption
	 */
	public function __construct($caption)
	{
		$this->caption = $caption;
	}

	/**
	 * @return string
	 */
	public function getCaption()
	{
		return $this->caption;
	}

	/**
	 * @param ColumnDefinition $columnDefinition
	 * @return bool
	 */
	public function equals(ColumnDefinition $columnDefinition) {
		return spl_object_hash($columnDefinition) == spl_object_hash($this);
	}

	/**
	 * This methods adds the column to GroupDefinition (columns can be present only in particular groups, even the ColumnData (cell) factory can be different for each group)
	 * @param GroupDefinition $groupDefinition
	 * @param $columnDataFactory callable This method should return callback that will construct the IColumnData object (it contains value to be displayed). Args: [array $entities]
	 */
	public function addToGroup(GroupDefinition $groupDefinition, $columnDataFactory) {
		$groupDefinition->addColumn($this, $columnDataFactory);
	}

}