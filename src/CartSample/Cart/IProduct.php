<?php

namespace Chikeet\CartSample\Cart;

/**
 * Interface IProduct
 * @package Chikeet\CartSample\Cart
 * A product passed to CartItem.
 */
interface IProduct
{
	
	public function getName(): string;
	
	public function getUntaxedUnitPrice(): float;
	
	public function getTaxPercents(): float;
}