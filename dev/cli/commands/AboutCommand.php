<?php

class AboutCommand
{
	private $args = [];

	public function __construct($args)
	{
		$this->args = $args;
	}

	public function run()
	{
		$readMe = file_get_contents(
		    $this->args['cwd'] . '/vendor/wpfluent/framework/README.md'
		);

		$about = array_merge([
		    'PHP version' => PHP_VERSION], $this->args['config']
		);

		if (preg_match('/version - \d+\.\d+.\d+/i', $readMe, $matches)) {
		    $pieces = explode('-', $matches[0]);
		    $about = array_merge([
		        'Framework version' => trim(end($pieces))
		    ], $about);
		}

		foreach ($about as $key => $value) {
		    echo 'Plugin ' . str_pad(
		        ucwords(str_replace('_', ' ', $key)), 18
		    ) . ' : ' . $value . PHP_EOL;
		}
	}
}
