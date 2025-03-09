<?php

namespace App\Models\Shop;

use App\Models\Shop\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supply extends Model
{
    protected $guarded = [];

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class , 'product_id');
    }

    public function supplier(): BelongsTo {
        return $this->belongsTo(User::class , 'supplier_id');
    }
}
