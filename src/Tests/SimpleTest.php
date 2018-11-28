<?php

namespace Tests\Unit;

use App\Models\Entitygenerator;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EntitygeneratorTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $user;

    protected $entitygenerator;

    protected $route;

    protected $data;

    public function setUp()
    {
        parent::setUp();
        //the admin user from table seeder
        $this->user = User::find(1);

        $this->route = config('app.url') . '/api/admin/entitygenerator';

        factory(Entitygenerator::class, 1)->create();
        $this->entitygenerator = Entitygenerator::first();

        $this->data = [
            INJECT_CODE_HERE_1
        ];
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

        INJECT_CODE_HERE_2

        $response = $this->delete($this->route . '/' .$this->entitygenerator->slug);
        $response->assertStatus(204);

        $this->assertDatabaseMissing('entitytable', $this->data);
    }

    public function testStoreRoute()
    {
        Passport::actingAs(
            $this->user
        );

        $this->assertDatabaseMissing('entitytable', $this->data);

        $response = $this->post($this->route, array(
            'entitygenerator' => $this->data
        ));
        $response->assertStatus(201);

        $this->assertDatabaseHas('entitytable', $this->data);
    }

    public function testUpdateRoute()
    {
        Passport::actingAs(
            $this->user
        );

        $this->assertDatabaseMissing('entitytable', $this->data);

        $response = $this->put($this->route . '/' .$this->entitygenerator->slug, array(
            'tag' => $this->data
        ));
        $response->assertStatus(200);

        $this->assertDatabaseHas('entitytable', $this->data);
    }

INJECT_CODE_HERE_3
}
