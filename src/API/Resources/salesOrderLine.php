<?php

declare(strict_types=1);

namespace Daalder\BusinessCentral\API\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class salesOrderLine
 *
 * @package BusinessCentral\API\Resources
 *
 * @mixin \Pionect\Backoffice\Models\Order\Orderrow
 */
class salesOrderLine extends Resource
{
    protected $rowDescriptionOverwrite;

    /**
     * salesOrderLine constructor.
     *
     * @param $resource
     * @param $rowDescriptionOverwrite
     */
    public function __construct($resource, $rowDescriptionOverwrite)
    {
        parent::__construct($resource);
        $this->overwriteRowDescription = $rowDescriptionOverwrite;
    }

    /**
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request, ?string $rowDescription = null): array
    {
        $array = [
            'lineType' => 'Item',
            'quantity' => $this->amount,
            'unitPrice' => (float) $this->price,
            //'currencyCode' => "EURO",
            //'paymentTerms' => "30 DAGEN",
        ];

        if ($this->overwriteRowDescription) {
            $array['description'] = $this->overwriteRowDescription;
        }

        return $array;
    }
}
