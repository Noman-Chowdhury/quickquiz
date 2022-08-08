<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DailyResource;
use App\Http\Resources\WeeklyResource;
use App\Models\Quiz;
use App\Models\UserQuestion;
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

    public function myDailyHistory($week)
    {
         $quizzes = auth('sanctum')->user()->submittedQuestions()->where('quiz_id','=',decrypt($week))->get();

        return response()->json([
            'success' => true,
            'histories' => DailyResource::collection($quizzes),
            'my_mark' => Quiz::find(decrypt($week))->questions()
                ->whereIn('id',auth('sanctum')->user()->submittedQuestions()->where('is_correct', true)->pluck('question_id'))
                ->sum('point'),
            'total_marks'=> Quiz::find(decrypt($week))->questions()->sum('point')
        ]);
    }
}
