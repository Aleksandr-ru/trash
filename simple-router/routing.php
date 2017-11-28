<?php
/**
 * Rounting format:
 * array(
 *	array('route', 'module', 'event', 'method'),
 *	...
 * );
 * @param route string URI relative to host (or base) with event params like
 *	{id} any non '/' named as 'id' in eventparams
 *	{num1|int} only digits named as 'num1' in eventparams
 * @param module string snake_case, default if omitted
 * @param event string without prefix, default if omitted
 * @param method string GET or POST or any if omitted
 */
return array(
	['/', /* default module if omitted ,*/ /* default event if omitted ,*/ /* all methods if omitted */],
	['/user/{id|int}',	'user',	'profile',	'get'],
	['/user/{id}/update',	'user',	'save_profile',	'post'],
	['/catalog/{section1}/{section2}', 'catalog', 'show'],
);