<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    // Pastikan Laravel membaca nama tabel dengan benar. 
    // GORM mungkin membuat tabel "faqs".
    protected $table = 'faqs';
    public $timestamps = false;

    protected $fillable = [
        'category',
        'question',
        'answer'
    ];
}