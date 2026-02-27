<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_manager_id', 'delivery_date', 'status'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_manager_id');
    }
}

