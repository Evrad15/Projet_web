<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientOrderItem extends Model
{
    protected $fillable = [
        'client_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal'
    ];

    // Retourner à la commande parente
    public function order(): BelongsTo
    {
        return $this->belongsTo(ClientOrder::class, 'client_order_id');
    }

    // Accéder aux détails du produit (nom, image, etc.)
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}