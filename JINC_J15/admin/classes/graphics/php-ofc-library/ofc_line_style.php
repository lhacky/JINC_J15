<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
class line_style
{
	function line_style($on, $off)
	{
		$this->style	= "dash";
		$this->on		= $on;
		$this->off		= $off;
	}
}