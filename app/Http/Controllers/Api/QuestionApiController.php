<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionApiResource;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\UserQuestion;
use App\Models\UserQuestionAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionApiController extends Controller
{
    public function getTodaysQuestions()
    {
        Quiz::where('publish_at', date('Y-m-d'))->update(['expired_at' => Carbon::now()]);
        $questions = Question::with(['options', 'quiz'])->whereHas('quiz', function ($query) {
            return $query->where('publish_at', date('Y-m-d'));
        })->get();

        return response()->json(['questions' => QuestionApiResource::collection($questions), 'total_time' => $questions->count() * 1.5],);
    }

    public function submitAnswers(Request $request)
    {
        DB::beginTransaction();
        try {

            foreach ($request->answers as $answer) {
                $submit = new UserQuestion();
                $submit->user_id = 2;
                $submit->question_id = $answer['q_id'];
                $submit->answer_time = Carbon::now();
                $submit->is_correct = true;
                $submit->save();
                if ($answer['a_type'] === 'radio') {
                    $ans = new UserQuestionAnswer();
                    $ans->user_question_id = $submit->id;
                    $ans->option_id = $answer['a_id'];
                    $ans->save();
                } else if ($answer['a_type'] === 'checkbox') {
                    foreach ($answer['a_id'] as $id) {
                        $ans = new UserQuestionAnswer();
                        $ans->user_question_id = $submit->id;
                        $ans->option_id = $id;
                        $ans->save();
                    }
                }
            }
            DB::commit();
        } catch (\Exception $err) {
            DB::rollBack();
            return $err->getMessage();
        }

        return 88;
    }
}
