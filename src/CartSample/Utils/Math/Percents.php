<?php
namespace Chikeet\CartSample\Utils\Math;

/**
 * Class Percents
 * @package Chikeet\CartSample\Utils\Math
 * Static utils class for simple work with percents.
 */
class Percents
{
	
	/**
	 * @desc Use e.g. to get tax amount from untaxed amount ($base) and tax percents.
	 * @param float $base
	 * @param float $percents
	 * @return float
	 */
	public static function getPercentsOfBase(float $base, float $percents): float
	{
		return $base * $percents / 100;
	}
	
	
	/**
	 * @desc Use e.g. to get taxed amount from untaxed amount ($base) and tax percents.
	 * @param float $base
	 * @param float $percents
	 * @return float
	 */
	public static function addPercentsToBase(float $base, float $percents): float
	{
		return $base + self::getPercentsOfBase($base, $percents);
	}
	
	
	/**
	 * @desc Use e.g. to get untaxed amount from taxed amount ($baseWithAddedPercents) and tax percents.
	 * @param float $baseWithAddedPercents
	 * @param float $percents
	 * @return float
	 */
	public static function subtractPercentsToGetBase(float $baseWithAddedPercents, float $percents): float
	{
		$coefficient = 1 + $percents / 100;
		if($coefficient === 0.0){
			throw new \InvalidArgumentException("Value of argument \$percents cannot be $percents due to division by zero.");
		}
		return $baseWithAddedPercents / $coefficient;
	}
}