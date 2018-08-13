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
	
	
	public function testAddItem(): void
	{
		$storage = $this->getDefaultStorage();
		$service = new \Chikeet\CartSample\Cart\CartService($storage);
		
		$product = new DefaultProduct('My test product', 123, 10);
		$cartItem = new CartItem($product, 10);
		
		$service->addItemToCart(1, $product, 10);

		Assert::equal([1 => $cartItem], $service->getCartItems(), 'There is an item in cart after one added.');
		Assert::equal($cartItem, $service->getItemByKey(1), 'An item can be got by key.');
		Assert::equal(1, $service->getCartItemsCount(), 'Items count is 1 after one added.');
		
		Assert::equal($cartItem->getUntaxedTotalPrice(), $service->getUntaxedTotalPrice(),
			'Untaxed total price is correct after one item added.');
		Assert::equal($cartItem->getTaxedTotalPrice(), $service->getTaxedTotalPrice(),
			'Taxed total price is correct after one item added.');
		
		
		$service->removeItemFromCart(1); // An item can be removed by key.
	}
	
	
	public function testAddExistingItem(): void
	{
		$storage = $this->getDefaultStorage();
		$service = new \Chikeet\CartSample\Cart\CartService($storage);
		$product = new DefaultProduct('My test product', 123, 10);
		
		$service->addItemToCart(1, $product, 10);
		$service->addItemToCart(1, $product, 5);

		Assert::equal(1, $service->getCartItemsCount(), 'There is an item in cart.');
		$firstItem = $service->getItemByKey(1);
		Assert::equal(15.0, $firstItem->getQuantity(), 'Item quantity is correct.');
		
		Assert::equal($firstItem->getUntaxedTotalPrice(), $service->getUntaxedTotalPrice(),
			'Untaxed total price is correct.');
		Assert::equal($firstItem->getTaxedTotalPrice(), $service->getTaxedTotalPrice(),
			'Taxed total price is correct.');
		
		$service->removeItemFromCart(1); // An item can be removed by key.
	}
	
	
	public function testAddAnotherItem(): void
	{
		$storage = $this->getDefaultStorage();
		$service = new \Chikeet\CartSample\Cart\CartService($storage);
		
		$product = new DefaultProduct('My test product', 123, 10);
		$cartItem = new CartItem($product, 10);
		
		$secondProduct = new DefaultProduct('Second test product', 234, 20);
		$secondCartItem = new CartItem($secondProduct, 2);
		
		/* adding items */
		$service->addItemToCart(1, $product, 10);
		$service->addItemToCart('hello', $secondProduct, 2);
		
		Assert::equal([1 => $cartItem, 'hello' => $secondCartItem], $service->getCartItems(),
			'There are 2 items in cart after 2 added.');
		Assert::equal($cartItem, $service->getItemByKey(1), 'The first item can be got by key.');
		Assert::equal($secondCartItem, $service->getItemByKey('hello'), 'The second item can be got by key.');
		Assert::equal(2, $service->getCartItemsCount(), 'Items count is 2 after one added.');
		
		Assert::equal($cartItem->getUntaxedTotalPrice() + $secondCartItem->getUntaxedTotalPrice(),
			$service->getUntaxedTotalPrice(), 'Untaxed total price is correct after one item added.');
		Assert::equal($cartItem->getTaxedTotalPrice() + $secondCartItem->getTaxedTotalPrice(),
			$service->getTaxedTotalPrice(), 'Taxed total price is correct after one item added.');
		
		/* removing items */
		$service->removeItemFromCart(1); // The first item can be removed by key.
		
		Assert::equal(['hello' => $secondCartItem], $service->getCartItems(),
			'There is 1 item in cart after one removed.');
		
		Assert::exception(function() use ($service){
			$service->getItemByKey(1); // The first item cannot be got by key after removed.
		}, ItemNotInCartException::class, "Item with \$itemKey '1' is not in cart.");
		
		Assert::exception(function() use ($service){
			$service->removeItemFromCart(1); // The first item cannot be removed after removed.
		}, ItemNotInCartException::class, "Item with \$itemKey '1' is not in cart.");
		
		Assert::equal($secondCartItem, $service->getItemByKey('hello'), 'The second item can be got by key.');
		Assert::equal(1, $service->getCartItemsCount(), 'Items count is 1 after one removed.');
		
		Assert::equal($secondCartItem->getUntaxedTotalPrice(), $service->getUntaxedTotalPrice(),
			'Untaxed total price is correct after one item removed.');
		Assert::equal($secondCartItem->getTaxedTotalPrice(), $service->getTaxedTotalPrice(),
			'Taxed total price is correct after one item removed.');
		
		$service->removeItemFromCart('hello'); // The second item can be removed by key.
	}
	
	
	public function testSetItemQuantity(): void
	{
		$storage = $this->getDefaultStorage();
		$service = new \Chikeet\CartSample\Cart\CartService($storage);
		
		$product = new DefaultProduct('My test product', 123, 10);
		
		$service->addItemToCart(1, $product, 10);
		$service->setCartItemQuantity(1, 12);

		$returnedItem = $service->getItemByKey(1);
		Assert::type(CartItem::class, $returnedItem, 'Item can be got by key.');
		Assert::equal(1, $service->getCartItemsCount(), 'Items count is 1 after quantity set.');
		Assert::equal(12.0, $returnedItem->getQuantity(), 'Item quantity is correct.');
		
		Assert::equal($returnedItem->getUntaxedTotalPrice(), $service->getUntaxedTotalPrice(),
			'Untaxed total price is correct after quantity set.');
		Assert::equal($returnedItem->getTaxedTotalPrice(), $service->getTaxedTotalPrice(),
			'Taxed total price is correct after quantity set.');
	}
	
	
	public function testCleanItems(): void
	{
		$storage = $this->getDefaultStorage();
		$service = new \Chikeet\CartSample\Cart\CartService($storage);
		
		$product = new DefaultProduct('My test product', 123, 10);
		
		$service->addItemToCart(1, $product, 10);
		$service->cleanCartItems(); // Items can be cleaned after one item added.
		
		Assert::equal([], $service->getCartItems(), 'There are no items in cart after items cleaned.');
		Assert::equal(0, $service->getCartItemsCount(), 'Items count is 0 after items cleaned.');
		Assert::equal(0.0, $service->getUntaxedTotalPrice(), 'Untaxed total price is 0 after items cleaned.');
		Assert::equal(0.0, $service->getTaxedTotalPrice(), 'Taxed total price is 0 after items cleaned.');
	}
	
	
	private function getDefaultStorage(): DefaultStorage
	{
		return new DefaultStorage; // TODO: better use mock instead
	}
}

$test = new CartServiceTest;
$test->run();