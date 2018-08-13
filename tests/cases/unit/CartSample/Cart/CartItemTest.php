<?php
/**
 * @testCase
 * @phpVersion >= 7.1
 */

require __DIR__ . '/../../../../bootstrap.php';
require __DIR__ . '/../../../../../src/CartSample/Utils/Math/Percents.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/IProduct.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/DefaultProduct.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/CartItem.php';

use Tester\Assert;
use Tester\TestCase;
use Chikeet\CartSample\Cart\DefaultProduct;
use Chikeet\CartSample\Cart\CartItem;

class CartItemTest extends TestCase
{
	
	public function testBasicGetters(): void
	{
		$product = $this->getTestProduct();
		$cartItem = new CartItem($product, 10);
		
		Assert::equal('My test product', $cartItem->getProduct()->getName(), 'Correct product name got.');
		Assert::equal(20.0, $cartItem->getTaxPercents(), 'Correct product tax percents got.');
		Assert::equal(123.4, $cartItem->getUntaxedUnitPrice(), 'Correct product tax percents got.');
		Assert::equal(10.0, $cartItem->getQuantity(), 'Correct quantity got.');
	}
	
	
	public function testTaxesMath(): void
	{
		$product = $this->getTestProduct();
		$cartItem = new CartItem($product, 10);
		
		Assert::equal(1234.0, $cartItem->getUntaxedTotalPrice(), 'Correct untaxed total price got.');
		Assert::equal(24.68, $cartItem->getUnitTax(), 'Correct unit tax got.');
		Assert::equal(148.08, $cartItem->getTaxedUnitPrice(), 'Correct taxed unit price got.');
		Assert::equal(1480.8, $cartItem->getTaxedTotalPrice(), 'Correct taxed total price got.');
	}
	
	
	public function testAddQuantity(): void
	{
		$product = $this->getTestProduct();
		$cartItem = new CartItem($product, 10);
		
		$cartItem->addQuantity(5);
		Assert::equal(15.0, $cartItem->getQuantity(), 'Correct quantity after adding got.');
		
		Assert::equal(1851.0, $cartItem->getUntaxedTotalPrice(), 'Correct untaxed total price after adding quantity got.');
		Assert::equal(24.68, $cartItem->getUnitTax(), 'Correct unit tax after adding quantity got.');
		Assert::equal(148.08, $cartItem->getTaxedUnitPrice(), 'Correct taxed unit price after adding quantity got.');
		Assert::equal(2221.2, $cartItem->getTaxedTotalPrice(), 'Correct taxed total price after adding quantity got.');
	}
	
	
	public function testSetQuantity(): void
	{
		$product = $this->getTestProduct();
		$cartItem = new CartItem($product, 10);
		
		$cartItem->setQuantity(15);
		Assert::equal(15.0, $cartItem->getQuantity(), 'Correct quantity after setting got.');
		
		Assert::equal(1851.0, $cartItem->getUntaxedTotalPrice(), 'Correct untaxed total price after setting quantity got.');
		Assert::equal(24.68, $cartItem->getUnitTax(), 'Correct unit tax after setting quantity got.');
		Assert::equal(148.08, $cartItem->getTaxedUnitPrice(), 'Correct taxed unit price after setting quantity got.');
		Assert::equal(2221.2, $cartItem->getTaxedTotalPrice(), 'Correct taxed total price after setting quantity got.');
	}
	
	
	private function getTestProduct(): DefaultProduct
	{
		return new DefaultProduct('My test product', 123.4, 20);
	}
}

$test = new CartItemTest;
$test->run();