<?php

namespace Chikeet\CartSample;

/**
 * Trying to modify/remove cart item that is not present in the cart.
 */
class ItemNotInCartException extends \Exception
{
}

/**
 * Trying to use non-existing class.
 */
class ClassNotFoundException extends \Exception
{
}

/**
 * Trying to use a class that does not implement a required interface.
 */
class ClassNotImplementingRequiredInterfaceException extends \Exception
{
}

/**
 * Trying to use an array structure that does not contain required keys.
 */
class MissingArrayKeysException extends \Exception
{
}