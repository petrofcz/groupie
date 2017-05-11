<?php
namespace Pcz\Groupie;

/**
 * This is the heart of groupie. It contains column definitions and builds the whole structure.
 */
class Groupie
{
	/**
	 * @var GroupDefinition[]
	 */
	protected $groupDefinitions;

	/**
	 * @var ColumnDefinition[]
	 */
	protected $columnDefinitions;

	/**
	 * This method builds the whole structure.
	 * @param $entities array Flat array of entities to be grouped.
	 * @param int $level Group level index. Levels can be skipped by setting value > 0.
	 * @return Group[]
	 */
	public function buildGroups($entities, $level = 0) {
		// Building groups
		/** @var Group[] $groups */
		$groups = [];
		if(!isset($this->groupDefinitions[$level])) {
			return [];
		}
		$groupDefinition = $this->groupDefinitions[$level];
		$entitiesByGroupUid = [];
		foreach($entities as $entity) {
			$groupUid = $level . '-' . call_user_func($groupDefinition->getUidRetriever(), $entity);
			if(!isset($groups[$groupUid])) {
				$groups[$groupUid] = new Group(
					$groupUid,
					call_user_func($groupDefinition->getGroupDataFactory(), $entity),
					$level,
					$entity
				);
				$entitiesByGroupUid[$groupUid] = [];
			}
			$entitiesByGroupUid[$groupUid][] = $entity;
		}
		foreach($entitiesByGroupUid as $groupUid => $iEntities) {
			$subGroups = $this->buildGroups($iEntities, $level + 1);
			foreach($subGroups as $subGroup) {
				$groups[$groupUid]->addChild($subGroup);
			}
		}

		/** @var IColumnData[][] $columnDatasByGroupUid */
		$columnDatasByGroupUid = [];
		foreach($entitiesByGroupUid as $groupUid => $iEntities) {
			if (!isset($columnDatasByGroupUid[$groupUid])) {
				$columnDatasByGroupUid[$groupUid] = [];
			}
		}

		$groupDefinition = $this->getGroup($level);
		foreach($this->getColumnDefinitions() as $columnId => $columnDefinition) {
			$columnFoundInGroup = false;
			foreach ($groupDefinition->getColumnDefinitionsWithDataFactories() as $columnDefinitionsWithDataFactory) {
				list($groupColumnDefinition, $columnDataFactory) = $columnDefinitionsWithDataFactory;
				if ($columnDefinition->equals($groupColumnDefinition)) {
					foreach($entitiesByGroupUid as $groupUid => $iEntities) {
						$columnDatasByGroupUid[$groupUid][] = $columnDataFactory($iEntities);
					}
					$columnFoundInGroup = true;
					break;
				}
			}
			if(!$columnFoundInGroup) {
				foreach($entitiesByGroupUid as $groupUid => $iEntities) {
					$columnDatasByGroupUid[$groupUid][] = null;
				}
			}
		}

		foreach($columnDatasByGroupUid as $groupUid => $columnDatas) {
			$groups[$groupUid]->setColumnDatas($columnDatas);
		}

		$finalGroups = array_values($groups);
		if(($sortingComparator = $groupDefinition->getSortingComparator()) !== null) {
			usort($finalGroups, $sortingComparator);
		}
		return $finalGroups;
	}

	public function getColumnDefinitions(){
		return $this->columnDefinitions;
	}

	public function addGroup(GroupDefinition $groupDefinition) {
		$this->groupDefinitions[] = $groupDefinition;
		return $this;
	}

	public function getGroupCount() {
		return count($this->groupDefinitions);
	}

	public function getGroup($index) {
		if(!isset($this->groupDefinitions[$index])) {
			throw new \OutOfBoundsException;
		}
		return $this->groupDefinitions[$index];
	}

	/**
	 * This method adds a column to groupie. It must be then added to particular groups too (with IColumnData factory specification).
	 * There is also a method 'addGlobalColumn' which can be used to add column to all groups in one call.
	 * @param ColumnDefinition $columnDefinition
	 * @return $this
	 */
	public function addColumn(ColumnDefinition $columnDefinition) {
		$this->columnDefinitions[] = $columnDefinition;
		return $this;
	}

	/**
	 * This method adds column to groupie and to all available groups. IT SHOULD BE CALLED AFTER ALL GROUPS ARE ADDED!
	 * @param ColumnDefinition $columnDefinition
	 * @param $columnDataFactory callable This method should return callback that will construct the IColumnData object (it contains aggregated value to be displayed). Args: [array $entities]
	 * @return $this
	 */
	public function addGlobalColumn(ColumnDefinition $columnDefinition, callable $columnDataFactory) {
		$this->addColumn($columnDefinition);
		foreach($this->groupDefinitions as $groupDefinition) {
			$groupDefinition->addColumn($columnDefinition, $columnDataFactory);
		}
		return $this;
	}

}