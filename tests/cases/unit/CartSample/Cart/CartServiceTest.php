<?php
/**
 * @testCase
 * @phpVersion >= 7.1
 */

require __DIR__ . '/../../../../bootstrap.php';
require __DIR__ . '/../../../../../src/CartSample/Utils/Arrays/ArrayChecker.php';
require __DIR__ . '/../../../../../src/CartSample/Utils/Math/Percents.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/IProduct.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/DefaultProduct.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/CartItem.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/IStorage.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/DefaultStorage.php';
require __DIR__ . '/../../../../../src/CartSample/Cart/CartService.php';
require __DIR__ . '/../../../../../src/CartSample/exceptions.php';

use Chikeet\CartSample\Cart\DefaultStorage;
use Chikeet\CartSample\ItemNotInCartException;
use Tester\Assert;
use Tester\TestCase;
use Chikeet\CartSample\Cart\DefaultProduct;
use Chikeet\CartSample\Cart\CartItem;

class CartServiceTest extends TestCase
{
	
	public function testInitialState(): void
	{
		$storage = $this->getDefaultStorage();
		$service = new \Chikeet\CartSample\Cart\CartService($storage);
		
		Assert::equal([], $service->getCartItems(), 'There are no items in cart at the beginning.');
		Assert::equal(0, $service->getCartItemsCount(), 'Items count is 0 at the beginning.');
		Assert::equal(0.0, $service->getUntaxedTotalPrice(), 'Untaxed total price is 0 at the beginning.');
		Assert::equal(0.0, $service->getTaxedTotalPrice(), 'Taxed total price is 0 at the beginning.');
		
		$service->cleanCartItems(); // Items can be cleaned when empty.
		
		Assert::exception(function() use ($service){
		    $service->getItemByKey(3.5);
		}, InvalidArgumentException::class, 'Argument $itemKey has to be integer or string, double given.');
		
		Assert::exception(function() use ($service){
		    $service->getItemByKey(5);
		}, ItemNotInCartException::class, "Item with \$itemKey '5' is not in cart.");
	}
	
	
	public function testCreateWithInvalidProductClass(): void
	{
		$storage = $this->getDefaultStorage();
		
		Assert::exception(function() use ($storage){
			$service = new \Chikeet\CartSample\Cart\CartService($storage, 'NoClass');
		}, \Chikeet\CartSample\ClassNotFoundException::class,
			"Argument \$productClass refers to non-existing class 'NoClass'.");
		
		Assert::exception(function() use ($storage){
			$service = new \Chikeet\CartSample\Cart\CartService($storage, DefaultStorage::class);
		}, \Chikeet\CartSample\ClassNotImplementingRequiredInterfaceException::class,
			"Argument \$productClass refers to a class 'Chikeet\CartSample\Cart\DefaultStorage' which " .
			"does not implement a required interface " . 'Chikeet\CartSample\Cart\IProduct.');
	}
	
	
	public function testAddItem(): void
	{
		$storage = $this->getDefaultStorage();
		$service = new \Chikeet\CartSample\Cart\CartService($storage);
		
		$product = new DefaultProduct('My test product', 123, 10);
		$cartItem = new CartItem($product, 10);
		
		$service->addItemToCart(1, $product);

		Assert::equal([1 => $cartItem], $service->getCartItems(), 'There is an item in cart after one added.');
		Assert::equal($cartItem, $service->getItemByKey(1), 'An item can be got by key.');
		Assert::equal(1, $service->getCartItemsCount(), 'Items count is 1 after one added.');
		
		Assert::equal($cartItem->getUntaxedTotalPrice(), $service->getUntaxedTotalPrice(),
			'Untaxed total price is correct after one item added.');
		Assert::equal($cartItem->getTaxedTotalPrice(), $service->getTaxedTotalPrice(),
			'Taxed total price is correct after one item added.');
		
		
		Assert::equal($cartItem, $service->getItemByKey(1), 'An item can be removed by key.');
		
//		$service->cleanCartItems(); // Items can be cleaned after one item added.
//		$service->cleanStorage(); // Storage can be cleaned after one item added.
	}
	
	
	public function testCleanItems(): void
	{
		$storage = $this->getDefaultStorage();
		$service = new \Chikeet\CartSample\Cart\CartService($storage);
		
		$product = new DefaultProduct('My test product', 123, 10);
		$cartItem = new CartItem($product, 10);
		
		$service->addItemToCart(1, $product);
		
		$service->cleanCartItems(); // Items can be cleaned after one item added.
		
		Assert::equal(0.0, $service->getUntaxedTotalPrice(),
			'Untaxed total price is 0 after items cleaned.');
		Assert::equal(0.0, $service->getTaxedTotalPrice(),
			'Taxed total price is 0 after items cleaned.');
	}
	
	
	private function getDefaultStorage(): DefaultStorage
	{
		return new DefaultStorage; // TODO: better use mock instead
	}
}

$test = new CartServiceTest;
$test->run();