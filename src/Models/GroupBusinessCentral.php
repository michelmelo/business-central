<?php

declare(strict_types=1);

namespace Daalder\BusinessCentral\Models;

use Pionect\Backoffice\Models\Product\Group;

/**
 * Class ProductBusinessCentral
 *
 * @package BusinessCentral\Models
 */
class GroupBusinessCentral extends ReferenceModel
{
    public $key = 'group_id';
    protected $table = 'group_business_central';
    protected $fillable = ['group_id', 'business_central_id'];
    protected $dates = ['created_at', 'updated_at'];

    public function set(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Group::class);
    }
}
