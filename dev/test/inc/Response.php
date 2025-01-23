<?php

namespace Dev\Test\Inc;

class Response
{
	use AssertTrait;
	
	protected $response = null;

	public function __construct($response)
	{
		$this->response = $response;
	}

	public function dd()
	{
		dd([
			'status' => $this->response->get_status(),
			'data' => $this->response->get_data()
		]);

		return $this;
	}

	public function ddToArray()
	{
		dd($this->toArray());

		return $this;
	}

	public function ddd()
	{
		ddd([
			'status' => $this->response->get_status(),
			'data' => $this->response->get_data()
		]);

		return $this;
	}

	public function dddToArray()
	{
		ddd($this->toArray());

		return $this;
	}

	public function isOkay()
	{
		$status = $this->getStatus();
		return $status >= 200 && $status < 300;
	}

	public function isBadRequest()
	{
		return $this->getStatus() === 400;
	}

	public function isForbidden($code = 403)
	{
		return $this->getStatus() === $code;
	}

	public function isNotFound()
	{
		return $this->getStatus() === 404;
	}

	public function isValidationError()
    {
        return $this->getStatus() === 422;
    }

    public function hasValidationErrors()
    {
        return count($this->getValidationErrors()) > 0;
    }

    public function getValidationErrors()
    {
        return $this->response->get_data() ?? [];
    }

    public function getData()
    {
        return $this->response->get_data();
    }

	public function toArray()
	{
		$result = $this->response->get_data();

		if (is_array($result)) return $result;

		if (method_exists($result, 'toArray')) {
			return $result->toArray();
		}

		throw new \ErrorException('Response is not an arrayable object');
	}

	public function __call($method, $params = [])
	{
		if (str_starts_with($method, 'assert')) {
			return Assert::$method($this->response->get_data(), ...$params);
		}

		if (preg_match('/[A-Z]/', $method, $matches)) {
			foreach ($matches as $match) {
				$method = str_replace($match, '_'.strtolower($match), $method);
			}
		}
		
		return $this->response->{$method}(...$params);
	}
}
