<?php
/**
 * pre()
 * выводит обернутый в <pre>: print_r для массивов и объектов или var_dump для простых
 * если включен error_reporting и display_errors
 * 
 * @param mixed $expression
 * @param bool $return
 * @return bool || html
 */
function pre($expression, $return = FALSE)
{
	if(error_reporting() && ini_get('display_errors')) {
		ob_start();
		echo "<pre>";	
		if(($callee = debug_backtrace()) && isset($callee[0])) echo "{$callee[0]['file']} line {$callee[0]['line']}: ";	
		if(is_array($expression) || is_object($expression) || is_resource($expression)) print_r($expression);
		else var_dump($expression);
		echo "</pre>";
		if($return) {
			$ret = ob_get_contents();
			ob_end_clean();
			return $ret;	
		}
		else {
			ob_end_flush();
			return TRUE;
		}		
	}
	else return FALSE;
}
?>