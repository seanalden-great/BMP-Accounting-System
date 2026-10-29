<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'date',
        'reference_no',
        'description'
    ];

    public function entries()
    {
        return $this->hasMany(JournalEntry::class, 'journal_id', 'id');
    }
}