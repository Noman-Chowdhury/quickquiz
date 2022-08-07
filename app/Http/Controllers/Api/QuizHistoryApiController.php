<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WeeklyResource;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizHistoryApiController extends Controller
{
    public function myWeeklyHistory()
    {
//        return auth('sanctum')->user()->submittedQuestions->pluck('question_id')->toArray();
        $quizzes =  Quiz::with('questions')->whereHas('questions', function ($q){
            return $q->whereIn('id',auth('sanctum')->user()->submittedQuestions()->pluck('question_id')->toArray());
        })->get();

        return response()->json([
           'success' => true,
           'histories' => WeeklyResource::collection($quizzes)
        ]);
    }
}
