<?php

namespace Chikeet\CartSample;

/**
 * Interface IStorage
 * @package Chikeet\CartSample
 * A storage for cart items data (e.g. a session storage adaptor).
 */
interface IStorage
{
	
	public function setData(array $data): void;
	
	
	public function getData(): array;
	
	
	public function clearData(): void;
	
}