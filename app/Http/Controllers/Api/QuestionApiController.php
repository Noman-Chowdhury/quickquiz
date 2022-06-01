<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionApiResource;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuestion;
use App\Models\UserQuestionAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionApiController extends Controller
{
    public function getTodaysQuestions(): \Illuminate\Http\JsonResponse
    {
//        return today();
//        Quiz::where('publish_at', date('Y-m-d'))->update(['expired_at' => Carbon::now()]);
//        $questions = Question::with(['options', 'quiz'])->whereHas('quiz', function ($query) {
//            return $query->where('publish_at', date('Y-m-d'));
//        })->get();
        if (sizeof(User::find(2)->todaysQuestions) > 0) {
            return response()->json(['success'=>false, 'message' => 'You cannot get more quiz question today. Please come again tomorrow!']);
        }
        $user_prev_ques = User::find(2)->submittedQuestions()->pluck('question_id')->toArray();
        $questions = Question::with(['options', 'quiz'])->whereNotIn('id', $user_prev_ques)->inRandomOrder()->take(10)->get();
        return response()->json(['success' => true, 'questions' => QuestionApiResource::collection($questions), 'time' => $questions->count() * .5, 'point' => $questions->sum('point')],);

    }

    public function submitAnswers(Request $request): \Illuminate\Http\JsonResponse|string
    {
        DB::beginTransaction();
        try {
            foreach ($request->answers as $answer) {
                $submit = new UserQuestion();
                $submit->user_id = 2;
                $submit->question_id = $answer['question_id'];
                $submit->answer_time = Carbon::now();
                $submit->is_correct = QuestionOption::find($answer['answer_id'])->is_correct_option;
                $submit->save();

                $ans = new UserQuestionAnswer();
                $ans->user_question_id = $submit->id;
                $ans->option_id = $answer['answer_id'];
                $ans->save();
//                if ($answer['a_type'] === 'radio') {
//                    $ans = new UserQuestionAnswer();
//                    $ans->user_question_id = $submit->id;
//                    $ans->option_id = $answer['answer_id'];
//                    $ans->save();
//                } else if ($answer['a_type'] === 'checkbox') {
//                    foreach ($answer['answer_id'] as $id) {
//                        $ans = new UserQuestionAnswer();
//                        $ans->user_question_id = $submit->id;
//                        $ans->option_id = $id;
//                        $ans->save();
//                    }
//                }
            }
            DB::commit();
        } catch (\Exception $err) {
            DB::rollBack();
            return $err->getMessage();
        }

        $submitted_questions = User::find(2)->todaysQuestions;

        $quiz_questions = Question::whereIn('id', $submitted_questions->pluck('question_id')->toArray());

        $correct_answers = $submitted_questions->where('is_correct', true);

        $total_marks = $quiz_questions->sum('point');

        $result = $quiz_questions->whereIn('id', $correct_answers->pluck('question_id')->toArray())->sum('point');

        return response()->json(['marks' => $total_marks, 'result' => $result]);
    }
}
