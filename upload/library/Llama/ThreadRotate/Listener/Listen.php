<?php

/**
 * Llama/ThreadRotate/Listener/Listen
*
* @package    Llama: Thread Rotate
* @author     Lazy Llama / Nigel Hardy
* @copyright  2014 Nigel Hardy
*/

class Llama_ThreadRotate_Listener_Listen
{
	public static function loadClassController($class, &$extend)
	{
		if ($class == 'XenForo_ControllerPublic_Thread')
		{
			$extend[] = 'Llama_ThreadRotate_ControllerPublic_Thread';
		}
	}

}
