<?php

namespace Dev\Factories;

use __NAMESPACE\App\Models\Post;
use Dev\Factories\Core\Factory;

class PostFactory extends Factory
{
	protected static $model = Post::class;

	public function defination($data = [])
	{
		return [
			'post_author' => $data['ID'],
			'post_title' => $this->fake->sentence(2),
			'post_content' => $this->fake->paragraph(5)
		];
	}
}
