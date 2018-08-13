<?php
/**
 * @testCase
 * @phpVersion >= 7.1
 */

require __DIR__ . '/../../../../../bootstrap.php';
require __DIR__ . '/../../../../../../src/CartSample/Utils/Arrays/ArrayChecker.php';

use Tester\Assert;
use Tester\TestCase;
use Chikeet\CartSample\Utils\Arrays\ArrayChecker;

class ArrayCheckerTest extends TestCase
{
	
	public function testGetMissingKeys()
	{
		$arrayToCheck = [
			1 => 'afds',
			'tomato' => FALSE,
			'potato' => NULL,
			0 => [],
			5 => new \stdClass,
		];
		Assert::equal([], ArrayChecker::getMissingKeys($arrayToCheck, []),
			'An empty array is returned when no keys are required.');
		Assert::equal([], ArrayChecker::getMissingKeys($arrayToCheck, [1, 'tomato', 'potato', 0, 5]),
			'An empty array is returned when no required keys are missing (all checked).');
		Assert::equal([], ArrayChecker::getMissingKeys($arrayToCheck, [1, 'tomato', 0]),
			'An empty array is returned when no required keys are missing (some checked).');
		Assert::equal([], ArrayChecker::getMissingKeys($arrayToCheck, [0, 1, 'tomato']),
			'An empty array is returned when no required keys are missing (some checked, different order).');
		Assert::equal([3, 'hello'], ArrayChecker::getMissingKeys($arrayToCheck, [1, 3, 'hello']),
			'A non-empty array is returned when some required keys are missing.');
		Assert::exception(function() use($arrayToCheck){
			ArrayChecker::getMissingKeys($arrayToCheck, [1, 'tomato', 'potato', new \stdClass]);
		}, \InvalidArgumentException::class, 'An array key has to be integer or string, object given.');
	}
	
	
	public function testIsValidArrayKey()
	{
		Assert::true(ArrayChecker::isValidArrayKey('key'), "'key' is a valid key.");
		Assert::true(ArrayChecker::isValidArrayKey(''), 'An empty string is a valid key.');
		Assert::true(ArrayChecker::isValidArrayKey(1), '1 is a valid key.');
		Assert::true(ArrayChecker::isValidArrayKey(0), '0 is a valid key.');
		
		Assert::false(ArrayChecker::isValidArrayKey([]), 'An array is not a valid key.');
		Assert::false(ArrayChecker::isValidArrayKey(new \stdClass()), 'An object is not a valid key.');
		Assert::false(ArrayChecker::isValidArrayKey(1.2), 'A float is not a valid key.');
		Assert::false(ArrayChecker::isValidArrayKey(NULL), 'NULL is not a valid key.');
		Assert::false(ArrayChecker::isValidArrayKey(TRUE), 'A boolean is not a valid key.');
	}
}

$test = new ArrayCheckerTest;
$test->run();