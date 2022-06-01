<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    public function userQuestion()
    {
        return $this->belongsTo(UserQuestion::class,'user_question_id');
    }

    public function option()
    {
        return $this->hasOne(QuestionOption::class,'option_id');
    }
}
