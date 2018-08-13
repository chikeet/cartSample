<?php
/**
 * @testCase
 * @phpVersion >= 7.1
 */

require __DIR__ . '/../../../../bootstrap.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/IStorage.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/DefaultStorage.php';

use Tester\Assert;
use Tester\TestCase;
use Chikeet\CartSample\Cart\DefaultStorage;

class DefaultStorageTest extends TestCase
{
	
	public function testSetAndGetData()
	{
		$data = [
			'apple' => 'juice',
			'banana' => 'split',
			7 => 'lucky',
			'hello' => 10,
			'items' => [],
		];
		
		$storage = new DefaultStorage;
		Assert::equal([], $storage->getData(),
			'Got empty array when no data set.');
		
		$storage->setData($data);
		Assert::equal($data, $storage->getData(),
			'Got data equal to previously set data.');
	}
	
	
	public function testClearData()
	{
		$storage = new DefaultStorage;
		$storage->clearData();
		Assert::equal([], $storage->getData(),
			'Got empty array when data cleared.');
	}
}

$test = new DefaultStorageTest;
$test->run();