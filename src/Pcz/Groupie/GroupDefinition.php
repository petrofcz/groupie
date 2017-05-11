<?php

namespace Pcz\Groupie;

/**
 * This is the class which defines a group of entities.
 * Each group must provide a unique id (eg. category id), group data factory (eg. caption).
 * Sorting of groups is also supported.
 */
class GroupDefinition
{
	/** @var  callable This callback should return unique id of the group per entity (eg. category id). args: [$entity] */
	protected $uidRetriever;

	/** @var  callable This callback should return IGroupData object (eg. new GroupData($category->getName())). Representative entity is passed as argument. args: [$representativeEntity] */
	protected $groupDataFactory;

	/** @var  callable|null This callback should act as comparator. Given IGroupData implementations are passed as args. If null given, no sorting of groups is provided. args: [Group $group1, Group $group2] */
	protected $sortingComparator;

	/** @var  array pairs [[ColumnDefinition $columnDefinition, callable $columnDataFactory],...] */
	protected $columnDefinitionsWithDataFactories;

	/**
	 * Level constructor.
	 * @param callable $uidRetriever This callback should return unique id of the group per entity (eg. category id). args: [$entity]
	 * @param callable $dataFactory This callback should return IGroupData object (eg. new GroupData($category->getName())). Representative entity is passed as argument. args: [$representativeEntity]
	 * @param callable|null $sortingComparator This callback should act as comparator. Given IGroupData implementations are passed as args. If null given, no sorting of groups is provided. args: [Group $group1, Group $group2]
	 */
	public function __construct(callable $uidRetriever, callable $dataFactory, $sortingComparator)
	{
		$this->uidRetriever = $uidRetriever;
		$this->groupDataFactory = $dataFactory;
		$this->sortingComparator = $sortingComparator;
	}

	/**
	 * Each group can show different columns. The ColumnData (cells) can be also generated in different way per each group.
	 * This method adds column to the group and defines corresponding ColumnData factory.
	 * @param ColumnDefinition $columnDefinition
	 * @param callable $columnDataFactory This method should return IColumnData instance (which contains aggregated value to be displayed). args: [array $entities]
	 * @return $this
	 */
	public function addColumn(ColumnDefinition $columnDefinition, callable $columnDataFactory) {
		$this->columnDefinitionsWithDataFactories[] = [$columnDefinition, $columnDataFactory];
		return $this;
	}

	/**
	 * @return callable
	 */
	public function getUidRetriever()
	{
		return $this->uidRetriever;
	}

	/**
	 * @return callable
	 */
	public function getGroupDataFactory()
	{
		return $this->groupDataFactory;
	}

	/**
	 * @return callable|null
	 */
	public function getSortingComparator()
	{
		return $this->sortingComparator;
	}

	/**
	 * @return ColumnDefinition[]
	 */
	public function getColumnDefinitionsWithDataFactories()
	{
		return $this->columnDefinitionsWithDataFactories;
	}


}