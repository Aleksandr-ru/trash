<?php
/**
 * Rounting format:
 * array(
 *	array('route', 'module', 'event', 'method'),
 *	...
 * );
 * @param route string URI relative to host (or base) with event params like
 *	{id} any non '/' named as 'id' in eventparams
 *	{id|str} any non '/' named as 'id' in eventparams
 *	{num1|int} only digits named as 'num1' in eventparams
 *	{bar2|raw} any chars named as 'bar2' in eventparams,
 *	be careful! 'raw' should be last part of route because it takes slashes '/'
 *	and it's values is not urlencoded in 'find' function result URL
 * @param module string snake_case, default if omitted
 * @param event string without prefix, default if omitted
 * @param method string GET or POST or any if omitted
 */
return array(
	['/', /* default module if omitted ,*/ /* default event if omitted ,*/ /* all methods if omitted */],
	['/user/{id|int}',	'user',	'profile',	'get'],
	['/user/{id}/update',	'user',	'save_profile',	'post'],
	['/catalog/{section1}/{section2}', 'catalog', 'show'],
	['/blog/filter/{filter|raw}', 'blog', 'list'],
	['/blog/filter/{tag}', 'blog', 'list'],
);