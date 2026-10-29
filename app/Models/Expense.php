<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'date',
        'reference_no',
        'contact_id',
        'account_code',
        'payment_code',
        'amount',
        'description'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_code', 'code');
    }

    public function paymentAccount()
    {
        return $this->belongsTo(Account::class, 'payment_code', 'code');
    }
}