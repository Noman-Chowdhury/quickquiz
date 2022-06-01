<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int|mixed $user_id
 * @property mixed $question_id
 * @property Carbon|mixed $answer_time
 * @property mixed $is_correct
 */
class UserQuestion extends Model
{
    use HasFactory;

    public function answers()
    {
        return $this->hasMany(UserAnswer::class,'user_question_id');
    }

    public function answer()
    {
        return $this->hasOne(UserAnswer::class,'user_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function point()
    {
        return $this->question()->point;
    }
}
