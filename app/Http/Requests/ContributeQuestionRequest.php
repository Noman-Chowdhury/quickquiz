<?php

namespace App\Http\Requests;

use App\Rules\AtLeastOneRequired;
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
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'question' => ['required', 'string'],
            'correct_ans' => ['required'],
            'reference' => ['string'],
            'options.*.option' => ['required'],
            'options' => ['required',new AtLeastOneRequired()]
        ];
    }

    protected function prepareForValidation()
    {
        foreach ($this->options as $option){
            if ($option['is_correct_option']){
                $answer = $option['option'];
            }
        }
        $this->merge([
           'reference'=>$this->feedback,
            'correct_ans'=>$answer
        ]);
    }
}
