<?php

namespace ReadmeDisplay\App\Hooks\Handlers;

use ReadmeDisplay\App\App;

class HeartBeat
{
    public function handle($response, $data)
    {
        $key = App::config()->get('app.slug');

        $response[base64_encode($key)] = wp_create_nonce('wp_rest');

        return $response;
    }
}
