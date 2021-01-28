<?php

declare(strict_types=1);

namespace Daalder\BusinessCentral\API\Repositories;

use App\Console\Commands\BusinessCentral\PullFromBusinessCentral;
use Daalder\BusinessCentral\Models\CustomerBusinessCentral;
use Pionect\Backoffice\Models\Customer\Customer;

/**
 * Class Product
 *
 * @package BusinessCentral\API\Resources
 */
class CustomerRepository extends RepositoryAbstract
{
    public $objectName = 'customers';

    /**
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function create(Customer $customer): ?\stdClass
    {
        $resource = new \BusinessCentral\API\Resources\Customer($customer);

        // If we have a reference then try to update.
        if ($this->referenceRepository->getReference(new CustomerBusinessCentral(['customer_id' => $customer->id]))) {
            return $this->update($customer);
        }

        $response = $this->client->post(
            config('business-central.endpoint').'companies('.config('business-central.companyId').')/customers', $resource->resolve()
        );

        $this->storeReference(new CustomerBusinessCentral([
            'customer_id' => $customer->id,
            'business_central_id' => $response->id,
        ]));

        return $response;
    }

    /**
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function update(Customer $customer): ?\stdClass
    {
        $resource = new \BusinessCentral\API\Resources\Customer($customer);

        // Unset displayName because we don't have 2 way sync yet and BC changes are leading.
        $customerResource = $resource->resolve();
        unset($customerResource['displayName']);

        /** @var CustomerBusinessCentral $reference */
        $reference = $this->referenceRepository->getReference(new CustomerBusinessCentral(['customer_id' => $customer->id]));

        if ($reference) {
            return $this->client->patch(
                config('business-central.endpoint').'companies('.config('business-central.companyId').')/customers('.$reference->business_central_id.')', $customerResource
            );
        }

        return null;
    }

    /**
     * @return null
     *
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function delete(Customer $customer)
    {
        /** @var CustomerBusinessCentral $reference */
        $reference = $this->referenceRepository->getReference(new CustomerBusinessCentral(['customer_id' => $customer->id]));

        return $this->client->delete(
            config('business-central.endpoint').'companies('.config('business-central.companyId').')/customers('.$reference->business_central_id.')'
        );
    }

    /**
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function pullReferences(PullFromBusinessCentral $command, int $top = 20000, int $skip = 0): ?\stdClass
    {
        $response = $this->client->get(
            config('business-central.endpoint').'companies('.config('business-central.companyId').')/customers?$top='.$top.'&$skip='.$skip
        );

        foreach ($response->value as $item) {
            $customer = Customer::find($item->number);
            if ($customer) {
                if (! $this->referenceRepository->getReference(new CustomerBusinessCentral(['customer_id' => $customer->id]))) {
                    $this->storeReference(new CustomerBusinessCentral([
                        'customer_id' => $customer->id,
                        'business_central_id' => $item->id,
                    ]));
                }
            } else {
                $command->error('Customer not found: '.$item->number);
            }
        }

        return $response;
    }

    /**
     * @param $guid
     *
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function get($guid): ?\stdClass
    {
        return $this->client->get(
            config('business-central.endpoint').'companies('.config('business-central.companyId').')/customers('.$guid.')'
        );
    }
}
