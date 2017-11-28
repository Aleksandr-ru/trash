<?php
class SimpleRouter
{
	protected static $routing = [];
	protected $route = [];
	protected $default_module = 'DEFAULT';
	protected $default_event = 'DEFAULT';

	/**
	 * Загрузка конфигурации
	 * @param string $base базовый УРЛ относительно хоста
	 */
	function __construct($base = '')
	{
		if(!count($this->routing)) {
			$this->routing = require('./routing.php');
			$base = preg_replace('^[a-z0-9]+://[^/]+/', '/', $base);
			if($base) foreach($this->routing as &$r) {
				$r[0] = '/' . trim($base, '/') . $r[0];
			}
		}
	}

	/**
	 * Разбор УРЛ в маршрут
	 * @param string $uri
	 * @param string $method
	 * @return boolean
	 */
	function parse($uri = null, $method = null)
	{
		if(is_null($uri)) {
			$uri = $_SERVER['REQUEST_URI'];
			$method = $_SERVER['REQUEST_METHOD'];
		}
		@list($request, $params) = explode('?', $uri);
		$request = rtrim($request, '/') or $request = '/';
		foreach($this->routing as &$route) {
			@list($rr, $mod, $evt, $met) = $route;
			$rr = rtrim($rr, '/') or $rr = '/';
			$regexp = '#^' . preg_replace_callback('@{(?P<name>[a-z0-9]+)(\|(?P<type>[a-z0-9]+))?}@i', [$this, 'parseCallback'], $rr) . '$#';
			if(preg_match($regexp, $request, $matches) && (strtoupper($met) == $method || !$met)) {
				foreach($matches as $key => &$v) {
					if(!is_string($key)) unset($matches[$key]);
				}
				$this->route['module'] = $mod or $this->route['module'] = $this->default_module;
				$this->route['event']  = $evt or $this->route['event']  = $this->default_event;
				$this->route['eventparams'] = $matches;
				parse_str($params, $this->route['queryparams']);
				if($method == 'POST') {
					$this->route['queryparams'] = array_merge($route['queryparams'], $_POST);
				}
				return true;
			}
		}
		if(!$method) $method = '*';
		trigger_error("No route for '$method $request'");
		return false;
	}

	private static function parseCallback($a)
	{
		$name = $a['name'];
		$type = @$a['type'];
		switch($type) {
			case 'int':
				return "(?P<$name>[0-9]+)";
			default:
				return "(?P<$name>[^/]+)";
		}
	}

	/**
	 * Подбор УРЛ по параметрам марштрута
	 * @param string $module
	 * @param string $event
	 * @param array $eventparams
	 * @param array $queryparams
	 * @param string $method
	 * @return boolean|string
	 */
	function find($module = null, $event = null, $eventparams = [], $queryparams = [], $method = null)
	{
		foreach($this->routing as &$route) {
			@list($rr, $mod, $evt, $met) = $route;
			if(($module == $mod || $module == $this->default_module && !$mod) && ($event == $evt || $event == $this->default_event && !$evt) && (strtoupper($method) == strtoupper($met) || !$method)) {
				$found = 0;
				foreach($eventparams as $key=>&$value) {
					if(preg_match("@\{$key\}@", $rr) && preg_match("@^[^/]+$@", $value) || preg_match("@\{$key\|int\}@", $rr) && preg_match("@^[0-9]+$@", $value)) {
						$found++;
					}
				}
				if(count($eventparams) == $found) {
					$url = preg_replace_callback('@{(?P<name>[a-z0-9]+)(\|[a-z0-9]+)?}@i', function($a) use($eventparams){
						$key = $a['name'];
						return $eventparams[$key];
					}, $rr);
					if(is_array($queryparams) && count($queryparams)) {
						$url .= '?' . http_build_query($queryparams);
					}
					return $url;
				}
			}
		}
		$args = preg_replace('@[\r\n\t\s]+@', ' ', print_r(func_get_args(), true));
		trigger_error("Route not found for '$args'!");
		return false;
	}
}