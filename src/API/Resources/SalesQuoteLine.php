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
class SalesQuoteLine extends Resource
{
    /**
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        return [
            'lineType' => 'Item',
            'quantity' => $this->amount,
            'unitPrice' => (float) $this->price,
            //'currencyCode' => "EURO",
            //'paymentTerms' => "30 DAGEN",
        ];
    }
}
