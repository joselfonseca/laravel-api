<?php

namespace App\Tests\Auth;

use DB;
use Carbon\Carbon;
use App\Tests\TestCase;
use App\Entities\Users\User;
use App\Contracts\UserResolverInterface;
use App\Contracts\ClientResolverInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class OAuthServerTest
 * Test the oAuth2 server functionality
 * @package App\Tests\Auth
 */
class OAuthServerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A request to auth/login with no parameters in the body should return error
     * @test
     */
    public function itRejectsAnExternalRequest()
    {
        $this->post('/api/oauth/authorize', [])
            ->seeStatusCode(400)
            ->seeJson([
                'errors' => [
                    'status' => '400',
                    'code' => 'InvalidRequest',
                    'title' => 'Invalid Request',
                    'detail' => 'The body does not have the necessary data to process the transaction',
                    'source' => [
                        'parameter' => 'grant_type'
                    ]
                ]
            ]);
    }

    /**
     * A request to auth/login with wrong client and secret
     * @test
     */
    public function itRejectsInvalidClient()
    {
        $this->post('/api/oauth/authorize', [
            'grant_type' => 'password',
            'client_id' => "98798798798798798798",
            'client_secret' => "4802938409238409238409823",
            'username' => "email@email.com",
            "password" => "98989898"
        ])->seeStatusCode(401)
            ->seeJson([
                'errors' => [
                    'status' => '401',
                    'code' => 'InvalidClient',
                    'title' => 'Invalid Client',
                    'detail' => 'The client requesting the information is not registered in the API'
                ]
            ]);
    }

    /**
     * If there is an authorized client querying the API but incomplete body
     * should respond with a 400 and the errors
     * @test
     */
    public function itRejectsRequestWithIncompleteBody()
    {
        DB::table('oauth_clients')->insert([
            'id' => '12345',
            'secret' => '12345',
            'name' => 'Testing Env',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $this->post('/api/oauth/authorize', [
            'grant_type' => 'password',
            'client_id' => "12345",
            'client_secret' => "12345",
            'username' => "email@email.com"
        ])
            ->seeStatusCode(400)
            ->seeJson([
                'errors' => [
                    'status' => '400',
                    'code' => 'InvalidRequest',
                    'title' => 'Invalid Request',
                    'detail' => 'The body does not have the necessary data to process the transaction',
                    'source' => [
                        'parameter' => 'password'
                    ]
                ]
            ]);
    }

    /**
     * If an authorized client makes a request with wrong user credentials it should return 401
     * @test
     */
    public function itValidatesWrongCredentials()
    {
        DB::table('oauth_clients')->insert([
            'id' => '12345',
            'secret' => '12345',
            'name' => 'Testing Env',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $this->post('/api/oauth/authorize', [
            'grant_type' => 'password',
            'client_id' => "12345",
            'client_secret' => "12345",
            'username' => "email@email.com",
            'password' => "testing"
        ])->seeStatusCode(401)
            ->seeJson([
                'errors' => [
                    'status' => '401',
                    'code' => 'InvalidCredentials',
                    'title' => 'Invalid Credentials',
                    'detail' => 'The information for the login is invalid'
                ]
            ]);
    }

    /**
     * If the authorized client request a login with valid credentials it should respond 200
     * @test
     */
    public function itValidatesCredentials()
    {
        DB::table('oauth_clients')->insert([
            'id' => '12345',
            'secret' => '12345',
            'name' => 'Testing Env',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user = factory(User::class)->create();
        $this->post('/api/oauth/authorize', [
            'grant_type' => 'password',
            'client_id' => "12345",
            'client_secret' => "12345",
            'username' => $user->email,
            'password' => "123456789"
        ])
            ->seeStatusCode(200);
    }

    /**
     * Once a token is delivered, it can be refreshed by the server.
     * @test
     */
    public function itRefreshesTheToken()
    {
        DB::table('oauth_clients')->insert([
            'id' => '12345',
            'secret' => '12345',
            'name' => 'Testing Env',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user = factory(User::class)->create();
        $response = $this->post('/api/oauth/authorize', [
            'grant_type' => 'password',
            'client_id' => "12345",
            'client_secret' => "12345",
            'username' => $user->email,
            'password' => "123456789"
        ])
            ->seeStatusCode(200);
        $responseBody = json_decode($response->response->getContent());
        $this->post('/api/oauth/authorize', [
            'grant_type' => 'refresh_token',
            'client_id' => "12345",
            'client_secret' => "12345",
            'refresh_token' => $responseBody->refresh_token
        ])
            ->seeStatusCode(200);
    }

    /**
     * @test
     */
    public function itValidatesClientCredentials()
    {
        DB::table('oauth_clients')->insert([
            'id' => '12345',
            'secret' => '12345',
            'name' => 'Testing Env',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $this->post('/api/oauth/authorize', [
            'grant_type' => 'client_credentials',
            'client_id' => "12345",
            'client_secret' => "12345"
        ])->seeStatusCode(200);
    }

    /**
     * @test
     */
    public function itResolvesUserFromId()
    {
        $user = factory(User::class)->create();
        $resolver = app(UserResolverInterface::class);
        $resolved = $resolver->resolveById($user->id);
        $this->assertEquals($user->id, $resolved->id);
    }

    /**
     * @test
     */
    public function itResolvesClientFromId()
    {
        $resolver = app(ClientResolverInterface::class);
        DB::table('oauth_clients')->insert([
            'id' => '12345',
            'secret' => '12345',
            'name' => 'Testing Env',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $this->assertEquals('12345', $resolver->resolveById('12345')->id);
    }
}