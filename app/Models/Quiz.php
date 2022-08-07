<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'title',
        'publish_at',
        'expired_at'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class,'quiz_id','id');
    }

}
