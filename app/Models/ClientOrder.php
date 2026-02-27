<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientOrder extends Model
{
    protected $fillable = [
        'client_id',
        'order_number',
        'total_amount',
        'status',
        'assigned_to',
        'notes'
    ];

    // Relation avec le Client
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Relation avec les produits de la commande
    public function items(): HasMany
    {
        return $this->hasMany(ClientOrderItem::class);
    }

    // Relation avec l'employé (Commercial) qui traite la commande
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relation vers la vente (facture) associée
    public function sale()
    {
        return $this->hasOne(Sale::class);
    }
}
