<?php

namespace Dev\Test\Inc;

trait AssertTrait
{
	public function assertStatus($code, $message = '')
	{
		Assert::assertTrue(
			$this->getStatus() === $code, $message
		);

		return $this;
	}

	public function assert200($message = '')
	{
		return $this->assertStatus(200, $message);
	}

	public function assert400($message = '')
	{
		return $this->assertStatus(400, $message);
	}

	public function assert401($message = '')
	{
		return $this->assertStatus(401, $message);
	}

	public function assert403($message = '')
	{
		return $this->assertStatus(403, $message);
	}

	public function assert404($message = '')
	{
		return $this->assertStatus(404, $message);
	}

	public function assert422($message = '')
	{
		return $this->assertStatus(422, $message);
	}

	public function assert500($message = '')
	{
		return $this->assertStatus(500, $message);
	}

	public function assertOkay($message = '')
	{
		Assert::assertTrue($this->isOkay(), $message);
		
		return $this;
	}

	public function assertForbidden($code = 401, $message = '')
	{
		Assert::assertTrue($this->isForbidden($code), $message);

		return $this;
	}

	public function assertNotFound($message = '')
	{
		Assert::assertTrue($this->isNotFound(), $message);

		return $this;
	}

	public function assertBadRequest($message = '')
	{
		Assert::assertTrue($this->isBadRequest(), $message);
	}

	public function assertDataIs($data, $message = '')
	{
		Assert::assertTrue($this->getData() === $data, $message);

		return $this;
	}

	public function assertValidationError($message = '')
    {
        Assert::assertTrue($this->isValidationError(), $message);

        return $this;
    }

	public function assertArrayHasKey($key, $message = '')
	{
		Assert::assertThatArrayHasKey(
			$this->toArray(), $key, $message
		);

		return $this;
	}

	public function assertArrayHasValue($key, $value, $message = '')
	{
		Assert::assertThatArrayHasValue(
			$this->toArray(), $key, $value, $message
		);

		return $this;
	}

	public function assertHeaderExists($name, $message = '')
	{
		$headers = $this->response->get_headers();

		Assert::assertArrayHasKey(
            $name, 
            $headers, 
            $message ?: "Failed asserting that the response has the header '$name'."
        );

        return $this;
	}

	public function assertHeaderHasValue($name, $value, $message = '')
	{
		$headers = $this->response->get_headers();

		if (!array_key_exists($name, $headers)) {
			$this->fail('The response does not contain the header '.$name.'.');
		}

		Assert::assertSame(
            $value, 
            $headers[$name], 
            $message ?: "Failed asserting that the header '$name' matches the expected value."
        );

        return $this;
	}

	public function assertHeader($name, $value = null, $message = '')
    {
        if (is_null($value)) {
        	return $this->assertHeaderExists($name, $message);
        } else {
        	return $this->assertHeaderHasValue($name, $value, $message);
        }
    }

    public function assertHeaderMissing($name, $message = '')
    {
    	$headers = $this->response->get_headers();

        Assert::assertArrayNotHasKey(
            $name, 
            $headers, 
            $message ?: "Failed asserting that the header '$name' is missing."
        );
    }

    public function assertHeaderContains($name, $value, $message = '')
    {
    	$headers = $this->response->get_headers();

        Assert::assertArrayHasKey(
            $name, 
            $headers, 
            $message ?: "Failed asserting that the response has the header '$name'."
        );

        Assert::assertStringContainsString(
            $value, 
            $headers[$name], 
            $message ?: "Failed asserting that the header '$name' contains '$value'."
        );
    }
}
