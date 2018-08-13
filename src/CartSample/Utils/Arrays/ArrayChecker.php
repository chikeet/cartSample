<?php
/**
 * Created by PhpStorm.
 * User: Zuzana Kreizlova
 * Date: 13.08.2018
 * Time: 0:28
 */

namespace Chikeet\CartSample\Utils\Arrays;

class ArrayChecker
{
	
	/**
	 * @desc Checks presence of required keys in array and returns missing keys if any.
	 * @param array $arrayToCheck
	 * @param array $requiredKeys
	 * @return array
	 */
	public static function getMissingKeys(array $arrayToCheck, array $requiredKeys): array
	{
		$missingKeys = [];
		foreach($requiredKeys as $requiredKey) {
			if(!self::isValidArrayKey($requiredKey)){
				throw new \InvalidArgumentException('An array key has to be integer or string, ' . gettype($requiredKey) . ' given.');
			}
			if(!array_key_exists($requiredKey, $arrayToCheck)){
				$missingKeys[] = $requiredKey;
			}
		}
		return $missingKeys;
	}
	
	
	/**
	 * @param mixed $key
	 * @return bool
	 */
	public static function isValidArrayKey($key)
	{
		return is_int($key) || is_string($key);
	}
	
	
}