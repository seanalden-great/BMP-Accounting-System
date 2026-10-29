<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    // Karena primary key GORM bukan 'id' auto-increment, melainkan 'code' string
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    // Nonaktifkan timestamps bawaan Laravel jika di Go tidak ada created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'is_header',
        'normal_balance',
        'level'
    ];

    // Relasi ke JournalEntry (Opsional, tapi mempermudah query)
    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'account_code', 'code');
    }
}