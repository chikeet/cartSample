<?php

namespace Chikeet\CartSample\Cart;

use Chikeet\CartSample\ClassNotFoundException;
use Chikeet\CartSample\ClassNotImplementingRequiredInterfaceException;
use Chikeet\CartSample\IStorage;
use Chikeet\CartSample\ItemNotInCartException;
use Chikeet\CartSample\MissingArrayKeysException;
use Chikeet\CartSample\Utils\Arrays\ArrayChecker;

class CartService
{
	private const DEFAULT_PRODUCT_CLASS = DefaultProduct::class;
	
	
	/**
	 * @var CartItem[]
	 */
	private $items = [];
	
	/**
	 * @var IStorage
	 */
	private $storage;
	
	/**
	 * @var string
	 */
	private $productClass;
	
	
	/**
	 * @param IStorage $storage
	 * @param string|NULL $productClass Used to recreate a product from storage data. A custom product class may be used.
	 * @throws ClassNotFoundException
	 * @throws ClassNotImplementingRequiredInterfaceException
	 */
	public function __construct(IStorage $storage, string $productClass = self::DEFAULT_PRODUCT_CLASS)
	{
		$this->storage = $storage;
		
		$this->validateProductClass($productClass);
		$this->productClass = $productClass;
	}
	
	
	/* Items ******************************************************************/
	
	/**
	 * @return CartItem[]
	 * @throws MissingArrayKeysException
	 */
	public function getCartItems(): array
	{
		if(!$this->items){
			$storageData = $this->storage->getData();
			$this->setItemsFromStorage($storageData);
		}
		return $this->items;
	}
	
	
	/**
	 * @param string|int $itemKey An unique key to identify the product within the cart, e.g. product id.
	 * @param IProduct $product
	 * @param float $quantity
	 * @return CartItem
	 * @throws ItemNotInCartException
	 * @throws MissingArrayKeysException
	 */
	public function addItemToCart($itemKey, IProduct $product, float $quantity): CartItem
	{
		$this->validateItemKey($itemKey);
		$this->getCartItems();
		
		if(array_key_exists($itemKey, $this->items)){
			/** @var CartItem $item */
			$item = $this->getItemByKey($itemKey);
			$item->addQuantity($quantity);
			
			if($item->getQuantity() === 0.0){
				$this->removeItemFromCart($product);
			}
		} else {
			$this->setItemByKey($itemKey, new CartItem($product, $quantity));
		}
		$this->updateStoredItems();
		
		return $this->getItemByKey($itemKey);
	}
	
	
	/**
	 * @param int|string $itemKey An unique key to identify the product within the cart, e.g. product id.
	 * @throws ItemNotInCartException
	 * @throws MissingArrayKeysException
	 */
	public function removeItemFromCart($itemKey): void
	{
		$this->validateItemKey($itemKey);
		$this->checkItemPresenceInCart($itemKey);
		
		unset($this->items[$itemKey]);
		$this->updateStoredItems();
	}
	
	
	/**
	 * @param int|string $itemKey An unique key to identify the product within the cart, e.g. product id.
	 * @param float $quantity
	 * @throws ItemNotInCartException
	 * @throws MissingArrayKeysException
	 */
	public function setCartItemQuantity($itemKey, float $quantity): void
	{
		$this->getCartItems();
		
		/** @var CartItem $item */
		$item = $this->getItemByKey($itemKey);
		$item->setQuantity($quantity);
		
		if($item->getQuantity() === 0.0){
			$this->removeItemFromCart($itemKey);
		}
		
		$this->updateStoredItems();
	}
	
	
	/**
	 * @return int
	 * @throws MissingArrayKeysException
	 */
	public function getCartItemsCount(): int
	{
		return count($this->getCartItems());
	}
	
	
	/**
	 * @param int|string $itemKey An unique key to identify the product within the cart, e.g. product id.
	 * @return CartItem
	 * @throws ItemNotInCartException
	 * @throws MissingArrayKeysException
	 */
	public function getItemByKey($itemKey): CartItem
	{
		$this->validateItemKey($itemKey);
		$this->checkItemPresenceInCart($itemKey);
		return $this->items[$itemKey];
	}
	
	
	public function cleanCartItems(): void
	{
		$this->items = [];
		$this->cleanStorage();
	}
	
	
	/**
	 * @param int|string $itemKey An unique key to identify the product within the cart, e.g. product id.
	 * @param CartItem $cartItem
	 */
	private function setItemByKey($itemKey, CartItem $cartItem): void
	{
		$this->validateItemKey($itemKey);
		$this->items[$itemKey] = $cartItem;
	}
	
	
	/* Totals *****************************************************************/
	
	/**
	 * @return float
	 * @throws MissingArrayKeysException
	 */
	public function getUntaxedTotalPrice(): float
	{
		$totalPrice = 0.0;
		foreach($this->getCartItems() as $item){
			$totalPrice += $item->getUntaxedTotalPrice();
		}
		return $totalPrice;
	}
	
	
	public function getTaxedTotalPrice(): float
	{
		$totalPrice = 0.0;
		foreach($this->items as $item){
			$totalPrice += $item->getTaxedTotalPrice();
		}
		return $totalPrice;
	}
	
	
	/* Storage ****************************************************************/
	
	/**
	 * @desc Cleans cart items storage.
	 */
	private function cleanStorage(): void
	{
		$this->storage->clearData();
	}
	
	
	private function updateStoredItems(): void
	{
		$this->storage->setData($this->prepareItemsForStorage());
	}
	
	
	/**
	 * @desc Prepares cart items for storage. Only basic scalar data are stored since some objects cannot be serialized for session etc.
	 * @return array
	 */
	private function prepareItemsForStorage(): array
	{
		$itemsData = [];
		
		foreach($this->items as $itemKey => $item){
			$product = $item->getProduct();
			$itemsData[$itemKey] = [
				'name' => $product->getName(),
				'untaxedUnitPrice' => $product->getUntaxedUnitPrice(),
				'taxPercents' => $product->getTaxPercents(),
				'quantity' => $item->getQuantity(),
			];
		}
		return $itemsData;
	}
	
	
	/**
	 * @desc Awakens cart items from storage.
	 * @param array $storageData
	 * @throws MissingArrayKeysException
	 */
	private function setItemsFromStorage(array $storageData): void
	{
		$this->items = [];
		
		foreach($storageData as $itemKey => $storageItemData){
			$cartItem = $this->createCartItemFromStorageItemData($storageItemData);
			$this->setItemByKey($itemKey, $cartItem);
		}
	}
	
	
	/* Utils ******************************************************************/
	
	/**
	 * @param int|string $itemKey
	 * @throws \InvalidArgumentException
	 */
	private function validateItemKey($itemKey): void
	{
		if(!ArrayChecker::isValidArrayKey($itemKey)){
			$message = 'Argument $itemKey has to be integer or string, ' . gettype($itemKey) . ' given.';
			throw new \InvalidArgumentException($message);
		}
	}
	
	
	/**
	 * @param int|string $itemKey
	 * @throws ItemNotInCartException
	 * @throws MissingArrayKeysException
	 */
	private function checkItemPresenceInCart($itemKey): void
	{
		$this->getCartItems();
		if(!array_key_exists($itemKey, $this->items)){
			$message = "Item with \$itemKey '$itemKey' is not in cart.";
			throw new ItemNotInCartException($message);
		}
	}
	
	
	/**
	 * @param string $productClass
	 * @throws ClassNotFoundException
	 * @throws ClassNotImplementingRequiredInterfaceException
	 */
	private function validateProductClass(string $productClass): void
	{
		/* check class */
		if(!class_exists($productClass)){
			$message = "Argument \$productClass refers to non-existing class '$productClass'.";
			throw new ClassNotFoundException($message);
		}
		
		$this->validateProductClassInterface($productClass);
	}
	
	
	/**
	 * @param string $productClass
	 * @throws ClassNotImplementingRequiredInterfaceException
	 */
	private function validateProductClassInterface(string $productClass): void
	{
		$interfaces = class_implements($productClass);
		if(!array_key_exists(IProduct::class, $interfaces)){
			$message = "Argument \$productClass refers to a class '$productClass' which " .
				"does not implement a required interface " . IProduct::class . ".";
			throw new ClassNotImplementingRequiredInterfaceException($message);
		}
	}
	
	
	/**
	 * @param $storageItemData
	 * @throws MissingArrayKeysException
	 */
	private function checkStorageItemData(array $storageItemData): void
	{
		$requiredKeys = ['name', 'untaxedUnitPrice', 'taxPercents', 'quantity'];
		$missingKeys = ArrayChecker::getMissingKeys($storageItemData, $requiredKeys);
		
		if(count($missingKeys) > 0){ // condition is more readable like this
			$message = 'Cart item data loaded from storage must contain keys "'
				. implode('", "', $missingKeys) .  '".';
			throw new MissingArrayKeysException($message);
		}
	}
	
	
	/**
	 * @param array $storageItemData
	 * @return CartItem
	 * @throws MissingArrayKeysException
	 */
	private function createCartItemFromStorageItemData(array $storageItemData): CartItem
	{
		$this->checkStorageItemData($storageItemData);
		
		$name = (string) $storageItemData['name'];
		$untaxedUnitPrice = (float) $storageItemData['untaxedUnitPrice'];
		$taxPercents = (float) $storageItemData['taxPercents'];
		$product = new $this->productClass($name, $untaxedUnitPrice, $taxPercents);
		
		$quantity = (float) $storageItemData['quantity'];
		return new CartItem($product, $quantity);
	}
	
}