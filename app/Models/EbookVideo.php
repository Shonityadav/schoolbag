<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use App\Series;

class EbookVideo extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = [
         'id', 'ebook_id', 'url', 'title', 'ebook_page_id', 'description', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'Youtube_Embeded'
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

    public static function que_type($chapter_num,$ebook_id)
    {
        $data = Series::all();
        
            
        foreach ($data as $key => $value) {
            $value->ques = PaperGenerator::select('id', 'question', 'que_type')->where(['ebook_id' => $ebook_id, 'chapter_num' => $chapter_num, 'que_type' => $value->que_type])->get();
        }

        return $data;
    }

}