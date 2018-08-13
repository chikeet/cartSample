<?php
/**
 * @testCase
 * @phpVersion >= 7.1
 */

require __DIR__ . '/../../../../../bootstrap.php';
require __DIR__ . '/../../../../../../src/CartSample/Utils/Math/Percents.php';

use Tester\Assert;
use Tester\TestCase;
use Chikeet\CartSample\Utils\Math\Percents;

class PercentsTest extends TestCase
{
	
	public function testGetPercentsOfBase(): void
	{
		Assert::equal(100.1, Percents::getPercentsOfBase(200.2, 50.0),
			'Returns correct result with two floats.');
		Assert::equal(2.2, Percents::getPercentsOfBase(200, 1.1),
			'Returns correct result with integer base and float percents.');
		Assert::equal(200.2, Percents::getPercentsOfBase(200.2, 100),
			'Returns correct result with float base and integer percents.');
		Assert::equal(60.0, Percents::getPercentsOfBase(200, 30),
			'Returns correct result with two integers.');
		Assert::equal(123.0, Percents::getPercentsOfBase(123, 100),
			'Returns correct result with two integers and 100%.');
		Assert::equal(0.0, Percents::getPercentsOfBase(123, 0),
			'Returns correct result with two integers and 0%.');
		Assert::equal(0.0, Percents::getPercentsOfBase(0, 123),
			'Returns correct result with two integers and base of 0.');
		Assert::equal(0.0, Percents::getPercentsOfBase(0, 0),
			'Returns correct result with two zeros.');
		Assert::equal(-60.0, Percents::getPercentsOfBase(200, -30),
			'Returns correct result with percents < 0.');
		Assert::equal(-60.0, Percents::getPercentsOfBase(-200, 30),
			'Returns correct result with base < 0.');
	}
	
	
	public function testAddPercentsToBase(): void
	{
		Assert::equal(300.3, Percents::addPercentsToBase(200.2, 50.0),
			'Returns correct result with two floats.');
		Assert::equal(202.2, Percents::addPercentsToBase(200, 1.1),
			'Returns correct result with integer base and float percents.');
		Assert::equal(400.4, Percents::addPercentsToBase(200.2, 100),
			'Returns correct result with float base and integer percents.');
		Assert::equal(220.0, Percents::addPercentsToBase(200, 10),
			'Returns correct result with two integers.');
		Assert::equal(246.0, Percents::addPercentsToBase(123, 100),
			'Returns correct result with two integers and 100%.');
		Assert::equal(123.0, Percents::addPercentsToBase(123, 0),
			'Returns correct result with two integers and 0%.');
		Assert::equal(0.0, Percents::addPercentsToBase(0, 123),
			'Returns correct result with two integers and base of 0.');
		Assert::equal(0.0, Percents::addPercentsToBase(0, 0),
			'Returns correct result with two zeros.');
		Assert::equal(140.0, Percents::addPercentsToBase(200, -30),
			'Returns correct result with percents < 0.');
		Assert::equal(-260.0, Percents::addPercentsToBase(-200, 30),
			'Returns correct result with base < 0.');
	}
	
	
	public function testSubtractPercentsToGetBase(): void
	{
		Assert::equal(133.46666666667, Percents::subtractPercentsToGetBase(200.2, 50.0),
			'Returns correct result with two floats.');
		Assert::equal(200.0, Percents::subtractPercentsToGetBase(202.2, 1.1),
			'Returns correct result with integer base and float percents.');
		Assert::equal(100.1, Percents::subtractPercentsToGetBase(200.2, 100),
			'Returns correct result with float base and integer percents.');
		Assert::equal(200.0, Percents::subtractPercentsToGetBase(220, 10),
			'Returns correct result with two integers.');
		Assert::equal(61.5, Percents::subtractPercentsToGetBase(123, 100),
			'Returns correct result with two integers and 100%.');
		Assert::equal(123.0, Percents::subtractPercentsToGetBase(123, 0),
			'Returns correct result with two integers and 0%.');
		Assert::equal(0.0, Percents::subtractPercentsToGetBase(0, 123),
			'Returns correct result with two integers and base of 0.');
		Assert::equal(0.0, Percents::subtractPercentsToGetBase(0, 0),
			'Returns correct result with two zeros.');
		Assert::equal(285.71428571429, Percents::subtractPercentsToGetBase(200, -30),
			'Returns correct result with percents < 0.');
		Assert::equal(-153.84615384615, Percents::subtractPercentsToGetBase(-200, 30),
			'Returns correct result with base < 0.');
		
		Assert::exception(function(){
		    Percents::subtractPercentsToGetBase(200, -100);
		}, InvalidArgumentException::class, "Value of argument \$percents cannot be -100 due to division by zero.");
	}
}

$test = new PercentsTest;
$test->run();