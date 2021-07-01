<?php

namespace Daalder\BusinessCentral\Tests\Repositories;

use Daalder\BusinessCentral\API\HttpClient;
use Daalder\BusinessCentral\Models\ProductBusinessCentral;
use Daalder\BusinessCentral\Models\ReferenceModel;
use Daalder\BusinessCentral\Repositories\CustomerRepository;
use Daalder\BusinessCentral\Models\CustomerBusinessCentral;
use Daalder\BusinessCentral\Repositories\ReferenceRepository;
use Daalder\BusinessCentral\Tests\TestCase as DaalderTestCase;
use Mockery;
use Pionect\Daalder\Models\Customer\Customer;

/**
 * Class CustomerRepositoryTest
 * @package Daalder\BusinessCentral\Tests\Repositories
 * @covers ReferenceRepository
 */
class ReferenceRepositoryTest extends DaalderTestCase
{
    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

    }

    /**
     * @test
     * @covers ReferenceRepository::storeReference()
     */
    public function testStoreReference()
    {
        Customer::withoutSyncingToSearch(function () {
            /** @var Customer $customer */
            $customer = Customer::factory()->create();

            app(ReferenceRepository::class)->storeReference(new CustomerBusinessCentral(['business_central_id' => 123, 'customer_id' => $customer->id]));
            $this->assertDatabaseHas('customer_business_central', ['business_central_id' => 123, 'customer_id' => $customer->id]);
        });

    }

    /**
     * @test
     * @covers ReferenceRepository::getReference()
     */
    public function testGetReference()
    {
        $this->markTestIncomplete();
    }

}