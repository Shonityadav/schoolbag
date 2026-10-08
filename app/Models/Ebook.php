<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Ebook extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = [
        'name', 'publication', 'series', 'author', 'standard', 'subject', 'ref_id', 'uid', 'cost', 'remark', 'company_id', 'group_id', 'created_by', 'updated_by', 'key_link', 'key_code'
    ];

    public static function boot()
     {
        parent::boot();
        static::creating(function($model)
        {
            $model->created_by = Auth::id();
			$model->updated_by = Auth::id();
            // $model->user_ip = \Request::ip();
        });
        static::updating(function($model)
        {
            $model->updated_by = Auth::id(); 
            // $model->user_ip = \Request::ip();
        });
    }

    public function page()
    {
        return $this->hasOne('App\Models\EbookPage');
    }
    
    public function pages()
    {
        return $this->hasMany('App\Models\EbookPage')->orderBy('position');
    }
    
    public function videos()
    {
        return $this->hasMany('App\Models\EbookVideo')->orderBy('id');
    }
    
    public function ytVideos()
    {
        return $this->hasMany('App\Models\YoutubeData')->orderBy('title', 'asc');
    }

    public function firstPages()
    {
        return $this->hasOne('App\Models\EbookPage');
    }
    
     public function TP1Row()
    {
        return $this->hasOne('App\Models\PaperGenerator');
    }
    
    public function chapters()
    {
        return $this->hasMany(EbookChapter::class)
	    ->orderBy('chapter_order');
    }
    
    public function bIndex()
    {
        return $this->hasMany('App\Models\EbookIndex');
    }
    
    public static function report($series)
    {
        /**      
        SELECT id, name, subject, standard, 
        (SELECT COUNT(id) FROM ebook_pages WHERE ebook_id = ebooks.id) AS total_pages,
        (SELECT COUNT(id) FROM ebook_videos WHERE ebook_id = ebooks.id) AS total_videos, 
        (SELECT COUNT(DISTINCT(chapter)) FROM paper_generators WHERE ebook_id = ebooks.id) AS total_chap,
        (SELECT COUNT(id) FROM paper_generators WHERE ebook_id = ebooks.id) AS total_ques 
        FROM `ebooks` ORDER BY subject, standard;
        */
        $data = Ebook::selectRaw("id, name, subject, standard, key_link,
                                (SELECT COUNT(id) FROM ebook_pages WHERE ebook_id = ebooks.id) AS total_pages,
                                (SELECT COUNT(id) FROM ebook_videos WHERE ebook_id = ebooks.id) AS total_videos, 
                                (SELECT COUNT(DISTINCT(chapter)) FROM paper_generators WHERE ebook_id = ebooks.id) AS total_chap,
                                (SELECT COUNT(id) FROM paper_generators WHERE ebook_id = ebooks.id) AS total_ques"
                                )
                                ->where("series", $series)
                                // ->orderByRaw("subject", "standard")
                                ->get();
        return $data;
    }
}
