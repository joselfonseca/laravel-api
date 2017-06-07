<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class PingTest
 * @package Tests\Feature
 */
class PingTest extends TestCase
{

    /**
     *
     */
    public function test_it_returns_ping()
    {
        $response = $this->json('GET', 'api/ping');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }
}
