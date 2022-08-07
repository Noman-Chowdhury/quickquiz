<?php

namespace App\Http\Requests;

use App\Rules\AtLeastOneRequired;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class ContributeQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('sanctum')->check() && auth('sanctum')->user()->todayTempQuestions()->count() < 5;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => ['numeric'],
            'question' => ['required', 'string', 'max:200'],
            'correct_ans' => ['nullable'],
            'reference' => ['nullable'],
            'options.*.option' => ['required'],
            'options' => ['required', new AtLeastOneRequired()],
            'feedback' => ['required']
        ];
    }

    protected function prepareForValidation()
    {

        $answer = null;
        foreach ($this->options as $option) {
            if ($option['is_correct_option']) {
                $answer = $option['option'];
            }
        }
        $this->merge([
            'reference' => $this->feedback,
            'correct_ans' => $answer,
            'user_id' => auth('sanctum')->user()->id
        ]);
    }

    public function messages()
    {
        return [
            'correct_ans.required' => 'Please Select one correct answer',
            'question.required' => 'Please Enter Your Question. It cannot be empty',
            'question.max' => 'You cannot make a question more than 200 characters.',
            'options.*.option.required' => 'Please ensure 4 options are filled',
            'feedback.required' => 'Please give us the source of question, so that wer can verify. If a common question, Just put Common on box',
        ];
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException('You can submit 5 questions a day');
    }
}
