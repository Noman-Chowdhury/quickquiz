<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\questionStoreReq;
use App\Http\Resources\ResultApiResource;
use App\Http\Resources\TempQuesRes;
use App\Models\Question;
use App\Models\TempQuestions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{

    public function index()
    {
        //
    }


    public function store(questionStoreReq $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $question = Question::create($validated);

            foreach ($validated['options'] as $option) {
                $question->options()->create($option);
            }

            if ($request->temp_question){
                $id = decrypt($request->temp_question_id);
                TempQuestions::find($id)->delete();
            }
            DB::commit();
        } catch (\Exception $exception) {
            return $exception;
            DB::rollBack();
            return $this->basicErrorResponse($exception->getMessage());
        }
        return $this->basicSuccessResponse('Question added successfully');
    }


    public function show(Question $id)
    {
        //
    }


    public function update(Request $request, Question $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }

    public function contributedQuestions()
    {
        $questions =  TempQuestions::with('user')->latest()->get();

        return response()->json(['success' => true, 'questions' => TempQuesRes::collection($questions)],);
    }
}
