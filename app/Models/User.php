<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser; // <--- AJOUTE ÇA
use Filament\Panel; // <--- AJOUTE ÇA
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements FilamentUser // <--- AJOUTE L'INTERFACE ICI
{
    use HasFactory, Notifiable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'phone']) // Seuls ces champs seront suivis
            ->logOnlyDirty() // N'enregistre que si ça change
            ->dontSubmitEmptyLogs(); // Évite les logs inutiles
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',      // INDISPENSABLE
        'client_id', // INDISPENSABLE
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cette méthode décide qui peut entrer dans l'admin.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // On autorise si le rôle est 'admin' (ou true pour tester si tu n'as pas encore de rôles)
        return $this->role === 'admin';
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
