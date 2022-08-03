<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempQuestions extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'options', 'correct_ans', 'reference'];
}
