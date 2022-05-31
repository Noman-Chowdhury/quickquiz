<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
