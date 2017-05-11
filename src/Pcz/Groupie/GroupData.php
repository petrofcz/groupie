<?php

namespace Pcz\Groupie;

/**
 * This class represents group data (row data). Feel free to override it and add extra attributes (eg. description of a group)
 */
class GroupData implements IGroupData
{
	/** @var  string group caption */
	protected $caption;

	/**
	 * GroupData constructor.
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

	/** @return string */
	public function __toString()
	{
		return $this->caption;
	}
}