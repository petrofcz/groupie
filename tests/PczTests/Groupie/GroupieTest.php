<?php

namespace PczTests\Groupie;

use Mockery\Mock;
use Pcz\Groupie\ColumnData;
use Pcz\Groupie\ColumnDefinition;
use Pcz\Groupie\Group;
use Pcz\Groupie\GroupData;
use Pcz\Groupie\GroupDefinition;
use Pcz\Groupie\Groupie;
use Tester\Assert;
use Tester\TestCase;


require __DIR__ . '/../../bootstrap.php';

class GroupieTest extends TestCase
{
	public function testGroupCount() {
		$groupie = new Groupie();
		Assert::equal(0, $groupie->getGroupCount());
		$groupie->addGroup(new GroupDefinition(
			function(){}, function() {}, null
		));
		Assert::equal(1, $groupie->getGroupCount());
		$groupie->addGroup(new GroupDefinition(
			function(){}, function() {}, null
		));
		Assert::equal(2, $groupie->getGroupCount());
	}

	public function testAddGlobalColumn() {
		$groupie = new Groupie();
		/** @var Mock[]|GroupDefinition[] $gds */
		$gds = [\Mockery::mock(GroupDefinition::class), \Mockery::mock(GroupDefinition::class)];
		foreach($gds as $gd) {
			$gd->shouldReceive('addColumn');
		}
		foreach($gds as $gd) {
			$groupie->addGroup($gd);
		}

		$columnDefinition = new ColumnDefinition('test');
		$columnDataFactory = function() { };

		$groupie->addGlobalColumn($columnDefinition, $columnDataFactory);

		foreach($gds as $gd) {
			Assert::noError(function() use ($gd, $columnDataFactory, $columnDefinition) {
				$gd->shouldHaveReceived('addColumn', [$columnDefinition, $columnDataFactory]);
			});
		}
	}

	public function testGroupIndexing() {
		$groupie = new Groupie();
		$groupie->addGroup($def1 = new GroupDefinition(function(){}, function() {}, null));
		$groupie->addGroup($def2 = new GroupDefinition(function(){}, function() {}, null));

		Assert::equal($def1, $groupie->getGroup(0));
		Assert::equal($def2, $groupie->getGroup(1));
		Assert::exception(function() use ($groupie) {
			$groupie->getGroup(2);
		}, \OutOfBoundsException::class);
	}

	public function testGroupBuilding() {
		$groupie = new Groupie();

		$cat1 = new Category('food');
		$cat2 = new Category('services');
		$cat3 = new Category('music');

		$partner1 = new Partner('Jacob');
		$partner2 = new Partner('Collier');

		$entities = [
			new Order(1, $cat3, $partner2),
			new Order(2, $cat1, null),
			new Order(4, $cat2, $partner1),
			new Order(8, $cat2, null),
			new Order(16, $cat1, $partner1),
			new Order(32, $cat3, null),
			new Order(64, $cat2, $partner2),
			new Order(128, $cat2, $partner1),
		];

		$groupie->addGroup($partnerGroup = new GroupDefinition(
			function(Order $order) { return ($order->getPartner() ? $order->getPartner()->getId() : 0); },
			function(Order $order) { return new GroupData($order->getPartner() ? $order->getPartner()->getName() : 'no partner'); },
			null
		));

		$groupie->addGroup(new GroupDefinition(
			function(Order $order) { return $order->getCategory()->getId(); },
			function(Order $order) { return new GroupData($order->getCategory()->getName()); },
			null
		));

		$groupie->addGlobalColumn(
			new ColumnDefinition('Price'),
			function(array $entities) {
				$sum = 0;
				/** @var Order[] $entities */
				foreach($entities as $entity) {
					$sum += $entity->getPrice();
				}
				return new ColumnData($sum);
			}
		);

		$groupie->addColumn(
			$countColumn = new ColumnDefinition('Order count')
		);

		$partnerGroup->addColumn($countColumn, function(array $entities) {
			$sum = 0;
			/** @var Order[] $entities */
			foreach($entities as $entity) {
				$sum += 1;
			}
			return new CustomColumnData($sum, $sum >= 3 ? 'red' : 'transparent');
		});

		$groups = $groupie->buildGroups($entities);

		// Test null sorting
		$i = 0;
		Assert::equal($partner2, $groups[$i]->getRepresentativeEntity()->getPartner());

		// Test row data
		Assert::equal('Collier', (string)$groups[$i]->getGroupData());

		// Test column datas
		$columnDatas = $groups[$i]->getColumnDatas();
		Assert::equal(65, $columnDatas[0]->getValue());
		Assert::equal(2, $columnDatas[1]->getValue());
		Assert::equal('transparent', $columnDatas[1]->getColor());

		// Test children
		$children = $groups[$i]->getChildren();
		Assert::equal('music', (string) $children[0]->getGroupData());
		Assert::equal(1, $children[0]->getColumnDatas()[0]->getValue());
		Assert::equal(null, $children[0]->getColumnDatas()[1]);
		Assert::equal('services', (string) $children[1]->getGroupData());
		Assert::equal(64, $children[1]->getColumnDatas()[0]->getValue());
		Assert::equal(null, $children[1]->getColumnDatas()[1]);

		$i++;

		// Test row data
		Assert::equal('no partner', (string)$groups[$i]->getGroupData());

		// Test column datas
		$columnDatas = $groups[$i]->getColumnDatas();
		Assert::equal(42, $columnDatas[0]->getValue());
		Assert::equal(3, $columnDatas[1]->getValue());
		Assert::equal('red', $columnDatas[1]->getColor());

		// Test children
		$children = $groups[$i]->getChildren();
		Assert::equal('food', (string) $children[0]->getGroupData());
		Assert::equal(2, $children[0]->getColumnDatas()[0]->getValue());
		Assert::equal(null, $children[0]->getColumnDatas()[1]);
		Assert::equal('services', (string) $children[1]->getGroupData());
		Assert::equal(8, $children[1]->getColumnDatas()[0]->getValue());
		Assert::equal(null, $children[1]->getColumnDatas()[1]);
		Assert::equal('music', (string) $children[2]->getGroupData());
		Assert::equal(32, $children[2]->getColumnDatas()[0]->getValue());
		Assert::equal(null, $children[2]->getColumnDatas()[1]);


		$i++;

		// Test row data
		Assert::equal('Jacob', (string)$groups[$i]->getGroupData());

		// Test column datas
		$columnDatas = $groups[$i]->getColumnDatas();
		Assert::equal(148, $columnDatas[0]->getValue());
		Assert::equal(3, $columnDatas[1]->getValue());
		Assert::equal('red', $columnDatas[1]->getColor());

		// Test children
		$children = $groups[$i]->getChildren();
		Assert::equal('services', (string) $children[0]->getGroupData());
		Assert::equal(132, $children[0]->getColumnDatas()[0]->getValue());
		Assert::equal(null, $children[0]->getColumnDatas()[1]);
		Assert::equal('food', (string) $children[1]->getGroupData());
		Assert::equal(16, $children[1]->getColumnDatas()[0]->getValue());
		Assert::equal(null, $children[1]->getColumnDatas()[1]);

	}

	public function testGroupSorting() {
		$groupie = new Groupie();

		$cat1 = new Category('food');
		$cat2 = new Category('services');
		$cat3 = new Category('music');

		$partner1 = new Partner('Jacob');
		$partner2 = new Partner('Collier');

		$entities = [
			new Order(1, $cat3, $partner2),
			new Order(2, $cat1, null),
			new Order(4, $cat2, $partner1),
			new Order(8, $cat2, null),
			new Order(16, $cat1, $partner1),
			new Order(32, $cat3, null),
			new Order(64, $cat2, $partner2),
			new Order(128, $cat2, $partner1),
		];

		$groupie->addGroup(new GroupDefinition(
			function(Order $order) { return $order->getCategory()->getId(); },
			function(Order $order) { return new GroupData($order->getCategory()->getName()); },
			function(Group $group1, Group $group2) {
				return strcmp((string)$group1->getGroupData(), (string)$group2->getGroupData());
			}
		));

		$groupie->addGroup($partnerGroup = new GroupDefinition(
			function(Order $order) { return ($order->getPartner() ? $order->getPartner()->getId() : 0); },
			function(Order $order) { return new GroupData($order->getPartner() ? $order->getPartner()->getName() : 'no partner'); },
			function(Group $group1, Group $group2) {
				return strcmp((string)$group1->getGroupData(), (string)$group2->getGroupData());
			}
		));

		$groups = $groupie->buildGroups($entities);

		Assert::equal('food', (string) $groups[0]->getGroupData());
		Assert::equal('music', (string) $groups[1]->getGroupData());
		Assert::equal('services', (string) $groups[2]->getGroupData());

		$subgroups = $groupie->buildGroups($entities, 1);

		Assert::equal('Collier', (string) $subgroups[0]->getGroupData());
		Assert::equal('Jacob', (string) $subgroups[1]->getGroupData());
		Assert::equal('no partner', (string) $subgroups[2]->getGroupData());

	}

}

(new GroupieTest())->run();