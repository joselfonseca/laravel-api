<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiDocsTest extends TestCase
{
    public function test_it_renders_api_docs_page()
    {
        $this->get('/')
            ->assertSeeText('The API uses conventional HTTP response codes to indicate the success or failure of an API request. The table below contains a summary of the typical response codes');
    }
}
