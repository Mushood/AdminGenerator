<?php

namespace Tests\Unit;

use App\Models\Entitygenerator;
use App\Models\User;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EntitygeneratorTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected $entitygenerator;

    protected $route;

    public function setUp()
    {
        parent::setUp();
        //the admin user from table seeder
        $this->user = User::find(1);

        $this->route = config('app.url') . '/api/admin/entitygenerator';

        $this->entitygenerator = Entitygenerator::find(1);
    }

    /**
     * Asserts welcome page is working
     *
     * @return void
     */
    public function testGetRoutes()
    {
        Passport::actingAs(
            $this->user
        );

        $response = $this->get($this->route);
        $response->assertStatus(200);

        $response = $this->get($this->route . '/' .$this->entitygenerator->slug);
        $response->assertStatus(200);
    }

    public function testDeleteRoute()
    {
        Passport::actingAs(
            $this->user
        );

        $response = $this->delete($this->route . '/' .$this->entitygenerator->slug);
        $response->assertStatus(204);
    }

}
