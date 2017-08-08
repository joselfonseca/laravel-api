<?php

namespace Tests\Feature\Assets;

use Tests\TestCase;
use App\Entities\User;
use Laravel\Passport\Passport;
use App\Events\AssetWasCreated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UploadImageTest extends TestCase
{

    use DatabaseMigrations;


    function test_it_uploads_an_image_from_direct_file()
    {
        $this->expectsEvents([
            AssetWasCreated::class
        ]);
        $file = base64_encode(file_get_contents(base_path('tests/pic.png')));
        Passport::actingAs(
            factory(User::class)->create()
        );
        $server = $this->transformHeadersToServerVars([
            'Content-Type' => 'image/png',
            'Content-Length' => mb_strlen($file)
        ]);
        $response = $this->call('POST', 'api/assets', [], [], [], $server, $file);
        $image = json_decode($response->getContent());
        $this->assertEquals(201, $response->getStatusCode());
        $response->assertJson([
            'data' => [
                'type' => 'image',
                'mime' => 'image/png',
            ]
        ]);
        $this->assertDatabaseHas('assets', [
            'type' => 'image',
            'mime' => 'image/png',
            'uuid' => $image->data->id
        ]);
        $this->assertTrue(Storage::has($image->data->path));
    }

    function test_it_verifies_max_file_size()
    {
        $this->doesntExpectEvents([
            AssetWasCreated::class
        ]);
        Passport::actingAs(
            factory(User::class)->create()
        );
        $file = file_get_contents(__DIR__.'/../../bigpic.jpg');
        $server = $this->transformHeadersToServerVars([
            'Content-Type' => 'image/jpeg',
            'Content-Length' => mb_strlen($file)
        ]);
        $response = $this->call('POST', 'api/assets', [], [], [], $server, $file);
        $this->assertEquals(413, $response->getStatusCode());
    }

    function test_it_validates_mime_type()
    {
        $this->doesntExpectEvents([
            AssetWasCreated::class
        ]);
        Passport::actingAs(
            factory(User::class)->create()
        );
        $server = $this->transformHeadersToServerVars([
            'Content-Type' => 'application/xml',
            'Content-Length' => mb_strlen('some ramdon content')
        ]);
        $response = $this->call('POST', 'api/assets', [], [], [], $server, 'some ramdon content');
        $this->assertEquals(422, $response->getStatusCode());
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('Content-Type', $jsonResponse['errors']);
    }

    function test_it_uploads_from_url()
    {
        $this->expectsEvents([
            AssetWasCreated::class
        ]);
        Passport::actingAs(
            factory(User::class)->create()
        );
        $response = $this->json('POST', 'api/assets', [
            'url' => 'http://via.placeholder.com/350x150'
        ]);
        $image = json_decode($response->getContent());
        $this->assertEquals(201, $response->getStatusCode());
        $response->assertJson([
            'data' => [
                'type' => 'image',
                'mime' => 'image/png',
            ]
        ]);
        $this->assertDatabaseHas('assets', [
            'type' => 'image',
            'mime' => 'image/png',
            'uuid' => $image->data->id
        ]);
        $this->assertTrue(Storage::has($image->data->path));
    }

    function test_it_respond_validation_unreachable_error_in_url()
    {
        $this->doesntExpectEvents([
            AssetWasCreated::class
        ]);
        Passport::actingAs(
            factory(User::class)->create()
        );
        $response = $this->json('POST', 'api/assets', [
            'url' => 'http://somedomain/350x150'
        ]);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('url', $jsonResponse['errors']);

    }

    function test_it_validates_mime_on_url()
    {
        $this->doesntExpectEvents([
            AssetWasCreated::class
        ]);
        Passport::actingAs(
            factory(User::class)->create()
        );
        $response = $this->json('POST', 'api/assets', [
            'url' => 'http://google.com'
        ]);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('Content-Type', $jsonResponse['errors']);

    }
}
