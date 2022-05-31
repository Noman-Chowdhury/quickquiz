<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
      'quiz_id',
      'question',
      'is_active',
      'point',
      'feedback'
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class,'quiz_id');
    }
}
