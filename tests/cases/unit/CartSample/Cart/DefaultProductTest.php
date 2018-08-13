<?php
/**
 * @testCase
 * @phpVersion >= 7.1
 */

require __DIR__ . '/../../../../bootstrap.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/IProduct.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/DefaultProduct.php';

use Tester\Assert;
use Tester\TestCase;
use Chikeet\CartSample\Cart\DefaultProduct;

class DefaultProductTest extends TestCase
{
	
	public function testHappyPath()
	{
		$product = new DefaultProduct('My test product', 123.4, 10.0);
		Assert::equal('My test product', $product->getName(), 'Correct name got.');
		Assert::equal(10.0, $product->getTaxPercents(), 'Correct tax percents got.');
		Assert::equal(123.4, $product->getUntaxedUnitPrice(), 'Correct untaxed unit price got.');
	}
	
	
	public function testReturnFloatPriceWhenSetAsInt()
	{
		$product = new DefaultProduct('My test product', 123, 10.0);
		Assert::equal(123.0, $product->getUntaxedUnitPrice(), 'Correct untaxed unit price got.');
	}
	
	
	public function testReturnFloatTaxPercentsWhenSetAsInt()
	{
		$product = new DefaultProduct('My test product', 123.0, 10);
		Assert::equal(10.0, $product->getTaxPercents(), 'Correct tax percents got.');
	}
}

$test = new DefaultProductTest;
$test->run();