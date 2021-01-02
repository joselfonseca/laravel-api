<?php

namespace Tests\Feature\Assets;

use App\Events\AssetWasCreated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UploadImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uploads_an_image_from_direct_file()
    {
        $this->expectsEvents([AssetWasCreated::class]);
        $file = base64_encode(file_get_contents(base_path('tests/Resources/pic.png')));
        Passport::actingAs(User::factory()->create());
        $server = $this->transformHeadersToServerVars([
            'Content-Type' => 'image/png',
            'Content-Length' => mb_strlen($file),
        ]);
        $response = $this->call('POST', 'api/assets', [], [], [], $server, $file);
        $image = json_decode($response->getContent());
        $this->assertEquals(201, $response->getStatusCode());
        $response->assertJson([
            'data' => [
                'type' => 'image',
                'mime' => 'image/png',
            ],
        ]);
        $this->assertDatabaseHas('assets', [
            'type' => 'image',
            'mime' => 'image/png',
            'uuid' => $image->data->id,
        ]);
        $this->assertTrue(Storage::has($image->data->path));
    }

    public function test_it_verifies_max_file_size()
    {
        $this->doesntExpectEvents([AssetWasCreated::class]);
        Passport::actingAs(User::factory()->create());
        $file = base64_encode(file_get_contents(base_path('tests/Resources/bigpic.jpg')));
        $server = $this->transformHeadersToServerVars([
            'Content-Type' => 'image/jpeg',
            'Content-Length' => mb_strlen($file),
            'Accept' => 'application/json',
        ]);
        $response = $this->call('POST', 'api/assets', [], [], [], $server, $file);
        $this->assertEquals(413, $response->getStatusCode());
    }

    public function test_it_validates_mime_type()
    {
        $this->doesntExpectEvents([AssetWasCreated::class]);
        Passport::actingAs(User::factory()->create());
        $server = $this->transformHeadersToServerVars([
            'Content-Type' => 'application/xml',
            'Content-Length' => mb_strlen('some ramdon content'),
            'Accept' => 'application/json',
        ]);
        $response = $this->call('POST', 'api/assets', [], [], [], $server, 'some ramdon content');
        $this->assertEquals(422, $response->getStatusCode());
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('Content-Type', $jsonResponse['errors']);
    }

    public function test_it_uploads_from_url()
    {
        $this->expectsEvents([AssetWasCreated::class]);
        Passport::actingAs(User::factory()->create());
        $response = $this->json('POST', 'api/assets', [
            'url' => 'http://via.placeholder.com/350x150',
        ]);
        $image = json_decode($response->getContent());
        $this->assertEquals(201, $response->getStatusCode());
        $response->assertJson([
            'data' => [
                'type' => 'image',
                'mime' => 'image/png',
            ],
        ]);
        $this->assertDatabaseHas('assets', [
            'type' => 'image',
            'mime' => 'image/png',
            'uuid' => $image->data->id,
        ]);
        $this->assertTrue(Storage::has($image->data->path));
    }

    public function test_it_respond_validation_unreachable_error_in_url()
    {
        $this->doesntExpectEvents([AssetWasCreated::class]);
        Passport::actingAs(User::factory()->create());
        $response = $this->json('POST', 'api/assets', [
            'url' => 'http://somedomain/350x150',
        ], [
            'Accept' => 'application/json',
        ]);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('url', $jsonResponse['errors']);
    }

    public function test_it_validates_mime_on_url()
    {
        $this->doesntExpectEvents([AssetWasCreated::class]);
        Passport::actingAs(User::factory()->create());
        $response = $this->json('POST', 'api/assets', [
            'url' => 'http://google.com',
        ], [
            'Accept' => 'application/json',
        ]);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('Content-Type', $jsonResponse['errors']);
    }

    public function test_it_validates_url()
    {
        Passport::actingAs(User::factory()->create());
        $response = $this->json('POST', 'api/assets', [
            'url' => 'http://somedomain.com/350x150',
        ]);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('Content-Type', $jsonResponse['errors']);
    }

    public function test_it_validates_size_using_multipart_file()
    {
        Storage::fake();
        Passport::actingAs(User::factory()->create());
        config()->set('files.maxsize', 10);
        $file = UploadedFile::fake()->image('avatar.jpg')->size(1000);
        $response = $this->post('api/assets', [
            'file' => $file,
        ], [
            'Accept' => 'application/json',
        ]);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertEquals(413, $response->getStatusCode());
        $this->assertArrayHasKey('message', $jsonResponse);
        $this->assertEquals('The body is too large', $jsonResponse['message']);
    }
}
