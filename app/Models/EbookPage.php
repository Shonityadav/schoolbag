<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EbookPage extends Model
{
    use SoftDeletes;

    protected $table = 'ebook_pages';

    protected $fillable = [
        'ebook_id', 'url', 'title', 'position', 'caption',
        'created_by', 'updated_by',
    ];

    public function ebook()
    {
        return $this->belongsTo(Ebook::class);
    }
}
