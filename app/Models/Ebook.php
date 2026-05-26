<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EbookPage;

class Ebook extends Model
{
    protected $table = 'ebooks';

    protected $fillable = [
        'name', 'publication', 'series', 'author',
        'standard', 'subject', 'youtube_channel',
        'ref_id', 'key_link', 'key_code', 'group_id', 'uid', 'price',
    ];

    public function pages()
    {
        return $this->hasMany(EbookPage::class)->orderBy('position');
    }
}
