<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';
    public $guarded = ['id'];


    public function category(): BelongsTo{
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function image():MorphOne{
        return $this->morphOne(Image::class,'imageable');
    }
}
