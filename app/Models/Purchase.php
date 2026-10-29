<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'date',
        'due_date',
        'reference_no',
        'contact_id',
        'status',
        'total',
        'notes',
        'payment_account_code'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id', 'id');
    }

    public function paymentAccount()
    {
        return $this->belongsTo(Account::class, 'payment_account_code', 'code');
    }
}