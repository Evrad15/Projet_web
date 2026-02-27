<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'amount', 'payment_method', 'payment_date'];

    // Un paiement appartient à une vente
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Ajoute ceci dans ton fichier Payment.php, après la fonction sale()

    public function client()
    {
        // "Passe par la vente (Sale) pour trouver le Client"
        return $this->hasOneThrough(
            Client::class,
            Sale::class,
            'id',           // Clé étrangère sur Sales (id de la vente)
            'id',           // Clé étrangère sur Clients (id du client)
            'sale_id',      // Clé locale sur Payments
            'client_id'     // Clé locale sur Sales
        );
    }
}
