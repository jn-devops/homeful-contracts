<?php

namespace App\Models;

use Homeful\Properties\Models\Property as BaseProperty;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends BaseProperty
{
    protected $connection = 'properties-pgsql';
    protected $table = 'properties';

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'sku', 'sku', 'product');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_code', 'code', 'projects');
    }
}
