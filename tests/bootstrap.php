<?php

if (!$loader = @include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}
$loader->add('PczTests', __DIR__ . '/');

// configure environment
Tester\Environment::setup();
