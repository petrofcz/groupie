<?php

namespace PczTests\Groupie;

use Pcz\Groupie\ColumnDefinition;
use Pcz\Groupie\GroupDefinition;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

class ColumnDefinitionTest extends TestCase
{
	public function testCaption() {
		$columnDefinition = new ColumnDefinition($caption = 'captionString');
		Assert::equal($caption, $columnDefinition->getCaption());
	}

	public function testEquals() {
		$instance1 = new ColumnDefinition($caption = 'caption');
		$instance2 = new ColumnDefinition($caption);

		Assert::equal(false, $instance1->equals($instance2));
		Assert::equal(false, $instance2->equals($instance1));
		Assert::equal(true, $instance2->equals($instance2));
		Assert::equal(true, $instance1->equals($instance1));
	}

	public function testAddToGroup() {
		$groupMock = \Mockery::mock(GroupDefinition::class);
		$groupMock->shouldReceive('addColumn');
		$instance = new ColumnDefinition('caption');
		$factory = function() { return null; };
		$instance->addToGroup($groupMock, $factory);
		Assert::noError(function() use ($groupMock, $factory, $instance) {
			$groupMock->shouldHaveReceived('addColumn', [$instance, $factory]);
		});
	}

}

(new ColumnDefinitionTest)->run();