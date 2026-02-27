<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Sale extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['total', 'status', 'client_id']) // Liste des champs à surveiller
            ->logOnlyDirty() // Enregistre seulement si une donnée a changé
            ->useLogName('vente'); // Un nom pour filtrer plus facilement
    }
    // Dans app/Models/Sale.php
    protected $fillable = [
        'client_id',
        'sales_employee_id',
        'total',
        'paid_amount', // Indispensable !
        'status',      // Indispensable (completed, credit, etc.)
        'client_order_id' // Ajout pour lier à la commande d'origine
    ];

    public function sales_employee()
    {
        return $this->belongsTo(User::class, 'sales_employee_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Une vente peut avoir plusieurs paiements
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Relation inverse vers la commande client
    public function clientOrder()
    {
        return $this->belongsTo(ClientOrder::class);
    }
}
