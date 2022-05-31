<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\quizStoreReq;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{

    public function index()
    {
        //
    }


    public function store(quizStoreReq $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        return $this->tryCatch(Quiz::class, $validated, 'create','Quiz Stored Successfully');
    }


    public function show(Quiz $quiz)
    {
        return $quiz;
    }


    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => ['string']
        ]);
        return $this->tryCatch($quiz, $validated, 'update','Quiz Updated Successfully');
    }


    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        return $this->tryCatch(Quiz::class, $id, 'destroy','Quiz Deleted Successfully');
    }
}
