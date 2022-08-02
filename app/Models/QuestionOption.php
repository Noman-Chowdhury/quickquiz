<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
      'question_id',
      'option',
      'is_correct_option'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class,'question_id');
    }

}
