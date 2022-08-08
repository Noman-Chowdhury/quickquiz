<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $user_question_id
 * @property mixed $option_id
 */
class UserQuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'question_id',
      'option_id',
      'is_correct_option',
      'answer_time'
    ];

    public function userQuestion()
    {
        return $this->belongsTo(UserQuestion::class,'user_question_id');
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class,'option_id');
    }
}
