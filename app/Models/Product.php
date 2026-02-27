<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'price', 'stock', 'security_stock']) // Données sensibles
            ->logOnlyDirty() // N'enregistre que si la valeur a CHANGÉ
            ->dontSubmitEmptyLogs(); // Pas de log si rien n'a bougé
    }
    protected $fillable = ['name', 'description', 'price', 'quantity',];

    // Un produit appartient à un fournisseur
    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    // Un produit peut apparaître dans plusieurs lignes de vente
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    // Utilise l'ID pour créer un code formatté sur 3 chiffres (001, 002...)
    public function getFormattedIdAttribute()
    {
        return 'PRD-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}
