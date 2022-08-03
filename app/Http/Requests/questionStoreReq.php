<?php

namespace App\Http\Requests;

use App\Models\Question;
use App\Models\Quiz;
use App\Rules\AtLeastOneRequired;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class questionStoreReq extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'quiz_id' => ['required', 'numeric', 'exists:quizzes,id'],
            'question' => ['required', 'string'],
            'question_type' => ['string', 'in:radio,checkbox', 'select'],
            'point' => ['required', 'numeric'],
            'options' => ['required',new AtLeastOneRequired()],
            'feedback' => ['string'],
            'options.*.option' => ['required'],
        ];
    }

    public function prepareForValidation()
    {
        $quiz = Quiz::latest()->first();
        if (!$quiz) {
            $quiz = $this->QuizCreate(1, Carbon::today());
        }
        $total_quiz = Question::where('quiz_id', $quiz->id)->count();

        if ($total_quiz < 70) {
            $quiz_id = $quiz->id;
        } else {
            $week_id = str_replace('Week ', '', $quiz->title) + 1;
            $quiz = $this->QuizCreate($week_id, Carbon::parse($quiz->expired_at)->addDay()->format('Y-m-d'));
            $quiz_id = $quiz->id;
        }
        $this->merge([
            'quiz_id' => $quiz_id,
        ]);
    }

    public function QuizCreate(int $week, $data)
    {
        $quiz = new Quiz();
        $quiz->title = 'Week ' . $week;
        $quiz->publish_at = $data;
        $quiz->expired_at = Carbon::parse($data)->addDays(7);
        $quiz->save();
        return $quiz;
    }
}
