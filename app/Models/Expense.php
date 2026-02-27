<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'description',
        'amount',
        'expense_date',
        'expense_category_id',
    ];

    protected $appends = ['title'];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function getTitleAttribute(): ?string
    {
        return $this->description;
    }

    public function setTitleAttribute($value): void
    {
        $this->attributes['description'] = $value;
    }
}
