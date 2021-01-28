<?php

namespace BusinessCentral\API\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class salesOrderLine
 *
 * @package BusinessCentral\API\Resources
 * @mixin \Pionect\Backoffice\Models\Order\Orderrow
 */
class salesOrderLine extends Resource
{
    protected $rowDescriptionOverwrite;

    /**
     * salesOrderLine constructor.
     * @param $resource
     * @param $rowDescriptionOverwrite
     */
    public function __construct($resource, $rowDescriptionOverwrite)
    {
        parent::__construct($resource);
        $this->overwriteRowDescription = $rowDescriptionOverwrite;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $rowDescription
     * @return array
     */
    public function toArray($request, string $rowDescription = null)
    {
        $array = [
            'lineType'  => 'Item',
            'quantity'  => $this->amount,
            'unitPrice' => (float) $this->price,
            //'currencyCode' => "EURO",
            //'paymentTerms' => "30 DAGEN",
        ];

        if($this->overwriteRowDescription) {
            $array['description'] = $this->overwriteRowDescription;
        }

        return $array;
    }
}
