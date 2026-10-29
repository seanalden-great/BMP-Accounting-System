<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    // Tabel otomatis yang digenerate GORM biasanya berbentuk jamak
    // (Misal: journal_entries). Laravel bisa mendeteksi ini otomatis.
    public $timestamps = false;

    protected $fillable = [
        'journal_id',
        'account_code',
        'debit',
        'credit'
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_code', 'code');
    }
}