<?php

namespace Chikeet\CartSample\Cart;

use Chikeet\CartSample\IStorage;

/**
 * Class DefaultStorage
 * @package Chikeet\CartSample\Cart
 * A simple implementation of a storage for cart testing.
 */
class DefaultStorage implements IStorage
{
	/** @var array */
	private $data = [];
	
	public function setData(array $data): void
	{
		$this->data = $data;
	}
	
	
	public function getData(): array
	{
		return $this->data;
	}
	
	
	public function clearData(): void
	{
		$this->data = [];
	}
}