<?php

namespace PczTests\Groupie;

use Pcz\Groupie\ColumnData;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

class ColumnDataTest extends TestCase
{
	public function testToString() {
		$columnData = new ColumnData($caption = 'captionString');
		Assert::equal($caption, (string)$columnData);
	}
}

(new ColumnDataTest())->run();