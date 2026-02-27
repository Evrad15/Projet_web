<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    // S'ils ne sont pas là, Client::create ne renverra pas d'ID !
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    protected $casts = [
        'phone' => 'encrypted',
        'address' => 'encrypted',
    ];
    
    /**
     * Get all of the sales for the Client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
