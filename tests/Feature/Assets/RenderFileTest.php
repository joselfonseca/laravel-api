<?php

namespace Tests\Feature\Assets;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

class RenderFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_image()
    {
        $file = base64_encode(file_get_contents(base_path('tests/Resources/pic.png')));
        Passport::actingAs(User::factory()->create());
        $server = $this->transformHeadersToServerVars([
            'Content-Type' => 'image/png',
            'Content-Length' => mb_strlen($file),
        ]);
        $response = $this->call('POST', 'api/assets', [], [], [], $server, $file);
        $asset = json_decode($response->getContent());
        $response = $this->get('api/assets/'.$asset->data->id.'/render');
        $response->assertStatus(200);
        $headers = $response->headers;
        $this->assertTrue($headers->has('Content-Type'));
        $this->assertEquals('image/png', $headers->get('Content-Type'));
    }

    public function test_it_renders_placeholder_image()
    {
        Passport::actingAs(User::factory()->create());
        $response = $this->get('api/assets/'.Str::uuid().'/render');
        $response->assertStatus(200);
        $headers = $response->headers;
        $this->assertTrue($headers->has('Content-Type'));
        $this->assertEquals('image/jpeg', $headers->get('Content-Type'));
    }

    public function test_it_renders_placeholder_image_resized_to_width_100()
    {
        Passport::actingAs(User::factory()->create());
        $response = $this->get('api/assets/'.Str::uuid().'/render?width=100');
        $response->assertStatus(200);
        $headers = $response->headers;
        $this->assertTrue($headers->has('Content-Type'));
        $this->assertEquals('image/jpeg', $headers->get('Content-Type'));
        Storage::put('created.jpeg', $response->getContent());
        $size = getimagesize(storage_path('app/created.jpeg'));
        $this->assertEquals(100, $size[0]);
        $this->assertEquals(100, $size[1]);
        Storage::delete('created.jpeg');
    }

    public function test_it_renders_placeholder_image_resized_to_height_100()
    {
        Passport::actingAs(User::factory()->create());
        $response = $this->get('api/assets/'.Str::uuid().'/render?height=100');
        $response->assertStatus(200);
        $headers = $response->headers;
        $this->assertTrue($headers->has('Content-Type'));
        $this->assertEquals('image/jpeg', $headers->get('Content-Type'));
        Storage::put('created.jpeg', $response->getContent());
        $size = getimagesize(storage_path('app/created.jpeg'));
        $this->assertEquals(100, $size[0]);
        $this->assertEquals(100, $size[1]);
        Storage::delete('created.jpeg');
    }

    public function test_it_renders_placeholder_image_resized_to_width_and_height()
    {
        Passport::actingAs(User::factory()->create());
        $response = $this->get('api/assets/'.Str::uuid().'/render?height=100&width=300');
        $response->assertStatus(200);
        $headers = $response->headers;
        $this->assertTrue($headers->has('Content-Type'));
        $this->assertEquals('image/jpeg', $headers->get('Content-Type'));
        Storage::put('created.jpeg', $response->getContent());
        $size = getimagesize(storage_path('app/created.jpeg'));
        $this->assertEquals(300, $size[0]);
        $this->assertEquals(100, $size[1]);
        Storage::delete('created.jpeg');
    }
}
