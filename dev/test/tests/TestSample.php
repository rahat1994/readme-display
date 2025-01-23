<?php

namespace Dev\Test\Tests;

use Dev\Test\Inc\App;
use Dev\Test\Inc\TestCase;
use Dev\Test\Inc\UsersAndPostsSeeder;
use __NAMESPACE\App\Models\User;

class TestSample extends TestCase
{
	use UsersAndPostsSeeder;

	public function setUp(): void
	{
		parent::setUp();
		$this->seedUsersAndPosts();
	}

	public function testWorks()
	{
		$this->assertCount(10, User::get());
	}
}
