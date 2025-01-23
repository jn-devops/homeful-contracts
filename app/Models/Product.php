<?php

namespace App\Models;

use Homeful\Products\Models\Product as BaseProduct;

class Product extends BaseProduct
{
    protected $connection = 'properties-pgsql';
    protected $table = 'products';
}
