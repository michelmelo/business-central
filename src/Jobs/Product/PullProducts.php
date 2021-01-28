<?php

namespace BusinessCentral\Jobs\Product;

use App\Models\Products\Product;
use BusinessCentral\API\HttpClient;
use BusinessCentral\Repositories\ProductRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

/**
 * Class PullProducts
 *
 * @package BusinessCentral\Jobs\Product
 */
class PullProducts implements ShouldQueue
{
    use Dispatchable, SerializesModels, Queueable, InteractsWithQueue;

    protected $productSku;

    public function __construct(string $productSku = null) {
        $this->productSku = $productSku;
    }

    public function handle()
    {
        $productRepository = app(ProductRepository::class);
        $productSku = $this->productSku;

        /**
         * @var HttpClient $client
         */
        $client = App::make(HttpClient::class);

        $skipToken = null;

        do {
            $response = (array) $client->item()->get($skipToken);
            $items    = $response['value'];
            $nextLink = array_get($response, '@odata.nextLink');
            preg_match('/skiptoken=([0-9a-zA-Z\-]+)/', $nextLink, $skipTokenMatch);
            $skipToken = array_get($skipTokenMatch, 1);

            // If the user passed a productSku to update
            if($productSku) {
                // Remove all items that do not match the sku
                $items = collect($items)->filter(function($item) use ($productSku) {
                    return $item->number === $productSku;
                })->values()->toArray();

                // Store business central id
                DB::table('product_business_central')->insert([
                    'product_id' => Product::where('sku', $productSku)->first()->id,
                    'business_central_id' => $items[0]->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // make $skipToken null so the loop breaks after this run
                $skipToken = null;
            }

            $productRepository->updateFromBusinessCentralApi($items);
        } while ($skipToken != null);
    }

    /**
     * @return array
     */
    public function tags()
    {
        return ['business-central', 'product'];
    }
}