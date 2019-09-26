<?php
//© 2019 Martin Peter Madsen
namespace MTM\Exception;

class Factories
{
	private static $_cStore=array();
	
	//USE: $aFact		= \MTM\Exception\Factories::$METHOD_NAME();
	
	public static function getException()
	{
		if (array_key_exists(__FUNCTION__, self::$_cStore) === false) {
			self::$_cStore[__FUNCTION__]	= new \MTM\Exception\Factories\Exception();
		}
		return self::$_cStore[__FUNCTION__];
	}
}