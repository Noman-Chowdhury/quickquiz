<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionApiResource;
use App\Http\Resources\ResultApiResource;
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
        if (User::find(2)->todaysQuestions()->count() > 0) {
            return response()->json(['success'=>false, 'message' => 'You cannot get more quiz question today. Please come again tomorrow!'],404);
        }
        $user_prev_ques = User::find(2)->submittedQuestions()->pluck('question_id')->toArray();
        $questions = Question::with(['options', 'quiz'])->whereHas('quiz', function ($q){
            return $q->whereDate('publish_at','<=', Carbon::now())->whereDate('expired_at','>=', Carbon::now());
        })->whereNotIn('id', $user_prev_ques)->inRandomOrder()->take(10)->get();
        foreach ($questions as $ns) {
            $submit = new UserQuestion();
            $submit->user_id = 2;
            $submit->question_id = $ns->id;
            $submit->answer_time = Carbon::now();
            $submit->is_correct = false;
            $submit->save();
        }
        return response()->json(['success' => true, 'questions' => QuestionApiResource::collection($questions), 'time' => $questions->count() * 20, 'point' => $questions->sum('point')],);

    }

    public function submitAnswers(Request $request)
    {
        DB::beginTransaction();
        try {
            if (sizeof($request->answers) > 0) {
                foreach ($request->answers as $answer) {
                    $ques = UserQuestion::where(['user_id'=>2,'question_id'=>$answer['question_id']])->whereDay('answer_time', Carbon::now())->first();
                    $ques->is_correct = QuestionOption::find($answer['answer_id'])->is_correct_option;
                    $ques->save();

//                    $submit = new UserQuestion();
//                    $submit->user_id = 2;
//                    $submit->question_id = $answer['question_id'];
//                    $submit->answer_time = Carbon::now();
//                    $submit->is_correct = QuestionOption::find($answer['answer_id'])->is_correct_option;
//                    $submit->save();

                    $ans = new UserQuestionAnswer();
                    $ans->user_question_id = $ques->id;
                    $ans->option_id = $answer['answer_id'];
                    $ans->save();
                }
            }
//            if (sizeof($request->not_submitted) > 0){
//                foreach ($request->not_submitted as $ns) {
//                    $submit = new UserQuestion();
//                    $submit->user_id = 2;
//                    $submit->question_id = $ns['id'];
//                    $submit->answer_time = Carbon::now();
//                    $submit->is_correct = false;
//                    $submit->save();
//                }
//            }
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

    public function getSubmittedQuestions()
    {
        $submitted_questions = User::find(2)->todaysQuestions;

        $quiz_questions = Question::whereIn('id', $submitted_questions->pluck('question_id')->toArray())->get();

        return response()->json(['success' => true, 'questions' => ResultApiResource::collection($submitted_questions)],);
    }
}
