<?php

namespace Pcz\Groupie;

/**
 * This class represents final group instance with aggregated data (data ~= ColumnData).
 */
class Group
{
	/** @var  string Unique ID of the group. Note that group level is not included. */
	protected $uid;

	/** @var  IGroupData */
	protected $groupData;

	/** @var  int 0-based level index */
	protected $levelIndex;

	/** @var Group[] */
	protected $children;

	/** @var (IColumnData|null)[] Column datas. Array size must be the same as the column count in the Groupie object. Missing columns are represented by null values. */
	protected $columnDatas;

	/** @var mixed Representative entity for the group */
	protected $representativeEntity;

	/**
	 * Group constructor.
	 * @param string $uid Unique ID of the group.
	 * @param IGroupData $groupData
	 * @param int $levelIndex 0-based group level index
	 * @param $representativeEntity mixed Representative entity for the group
	 */
	public function __construct($uid, IGroupData $groupData, $levelIndex, $representativeEntity)
	{
		$this->uid = $uid;
		$this->groupData = $groupData;
		$this->levelIndex = $levelIndex;
		$this->representativeEntity = $representativeEntity;
	}

	public function addChild(Group $group) {
		$this->children[] = $group;
		return $this;
	}

	/**
	 * @param  (IColumnData|null)[] Sets column datas. Array size must be the same as the column count in the Groupie object. Missing columns are represented by null values.
	 */
	public function setColumnDatas(array $columnDatas) {
		$this->columnDatas = $columnDatas;
	}

	/**
	 * @return string Returns unique ID of the group. Note that group level is not included.
	 */
	public function getUid()
	{
		return $this->uid;
	}

	/**
	 * @return IGroupData
	 */
	public function getGroupData()
	{
		return $this->groupData;
	}

	/**
	 * @return int 0-based index of the group level
	 */
	public function getLevelIndex()
	{
		return $this->levelIndex;
	}

	/**
	 * @return Group[]
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * @return (IColumnData|null)[] Return column datas. Array size must be the same as the column count in the Groupie object. Missing columns are represented by null values.
	 */
	public function getColumnDatas()
	{
		return $this->columnDatas;
	}

	/**
	 * @return mixed
	 */
	public function getRepresentativeEntity()
	{
		return $this->representativeEntity;
	}

}