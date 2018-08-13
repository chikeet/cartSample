<?php

namespace Chikeet\CartSample\Cart;

use Chikeet\CartSample\Utils\Math\Percents;

class CartItem
{
	
	/**
	 * @var IProduct
	 */
	private $product;
	
	/**
	 * @var float
	 */
	private $quantity;
	
	
	public function __construct(IProduct $product, float $quantity)
	{
		$this->product = $product;
		$this->quantity = $quantity;
	}
	
	
	public function getProduct(): IProduct
	{
		return $this->product;
	}
	
	
	public function getQuantity(): float
	{
		return $this->quantity;
	}
	
	
	public function setQuantity(float $quantity)
	{
		$this->quantity = $quantity;
	}
	
	
	public function addQuantity(float $quantity)
	{
		$this->quantity += $quantity;
	}
	
	
	public function getTaxPercents(): float
	{
		return $this->product->getTaxPercents();
	}
	
	
	public function getUntaxedUnitPrice(): float
	{
		return $this->product->getUntaxedUnitPrice();
	}
	
	
	public function getUntaxedTotalPrice(): float
	{
		return $this->quantity * $this->getUntaxedUnitPrice();
	}
	
	
	public function getTaxedUnitPrice(): float
	{
		return Percents::addPercentsToBase($this->getUntaxedUnitPrice(), $this->getTaxPercents());
	}
	
	
	public function getTaxedTotalPrice(): float
	{
		return $this->quantity * $this->getTaxedUnitPrice();
	}
	
	
	public function getUnitTax(): float
	{
		return Percents::getPercentsOfBase($this->getUntaxedUnitPrice(), $this->getTaxPercents());
	}
	
	
	public function getTotalTax(): float
	{
		return $this->quantity * $this->getUnitTax();
	}
}