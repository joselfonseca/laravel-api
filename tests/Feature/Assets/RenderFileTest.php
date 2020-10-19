<?php

namespace Tests\Feature\Assets;

use App\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

class RenderFileTest extends TestCase
{
    use RefreshDatabase;

    function test_it_renders_image()
    {
        $file = base64_encode(file_get_contents(base_path('tests/Resources/pic.png')));
        Passport::actingAs(factory(User::class)->create());
        $server = $this->transformHeadersToServerVars([
            'Content-Type' => 'image/png',
            'Content-Length' => mb_strlen($file)
        ]);
        $response = $this->call('POST', 'api/assets', [], [], [], $server, $file);
        $asset = json_decode($response->getContent());
        $response = $this->get('api/assets/'.$asset->data->id.'/render');
        $response->assertStatus(200);
        $headers = $response->headers;
        $this->assertTrue($headers->has('Content-Type'));
        $this->assertEquals('image/png', $headers->get('Content-Type'));
    }

    function test_it_renders_placeholder_image()
    {
        Passport::actingAs(factory(User::class)->create());
        $response = $this->get('api/assets/'.Str::uuid().'/render');
        $response->assertStatus(200);
        $headers = $response->headers;
        $this->assertTrue($headers->has('Content-Type'));
        $this->assertEquals('image/jpeg', $headers->get('Content-Type'));
    }
}
