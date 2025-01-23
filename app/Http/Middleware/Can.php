<?php

namespace ReadmeDisplay\App\Http\Middleware;

class Can
{
	/**
	 * Handle the request
	 * 
	 * @param  WPFluent\Request\Request $request
	 * @param  Closure $next
	 * @param  array $params
	 * @return mixed
	 */
	public function handle($request, \Closure $next, ...$params)
	{
		if (isset($params[0]) && $this->has($params[0])) {
			return $next($request);
		}

		// return false or nothing for null, the Rest API will handle the response as
		// rest_forbidden (status:403) or call $request->abort to send a custom
		// response. This call will simply call Response::json method.
		return $request->abort(/*int code, string message*/);
	}

	protected function has($permission)
	{
		// Implement your permission checking mechanism
		// and return true on success, otherwise false.
		return true;
	}
}
