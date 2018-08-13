<?php

namespace Chikeet\CartSample\Cart;

/**
 * Class DefaultProduct
 * @package Chikeet\CartSample\Cart
 * A simple implementation of a cart product. Used as default class if no custom product class is set.
 */
class DefaultProduct implements IProduct
{
	
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var float
	 */
	private $untaxedUnitPrice;
	
	/**
	 * @var float
	 */
	private $taxPercents;
	
	
	public function __construct(string $name, float $untaxedUnitPrice, float $taxPercents)
	{
		$this->name = $name;
		$this->untaxedUnitPrice = $untaxedUnitPrice;
		$this->taxPercents = $taxPercents;
	}
	
	
	public function getName(): string
	{
		return $this->name;
	}
	
	
	public function getUntaxedUnitPrice(): float
	{
		return $this->untaxedUnitPrice;
	}
	
	
	public function getTaxPercents(): float
	{
		return $this->taxPercents;
	}
}