<?php
/**
 * Проверяет наличие строковых ключей в массиве 
 * @param array $arr
 * @return bool
 * @link http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
 */
function is_assoc($arr)
{
	if(!is_array($arr)) {
		trigger_error("Argument should be an array for is_assoc", E_USER_WARNING);
		return FALSE;
	}
	return count(array_filter(array_keys($arr), 'is_string')) > 0;
}
?>
