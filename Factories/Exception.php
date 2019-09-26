<?php
//© 2019 Martin Peter Madsen
namespace MTM\Exception\Factories;

class Exception extends Base
{
	public function getDisplayTool()
	{
		if (array_key_exists(__FUNCTION__, $this->_cStore) === false) {
			$this->_cStore[__FUNCTION__]	= new \MTM\Exception\Tools\Display();
		}
		return $this->_cStore[__FUNCTION__];
	}
}