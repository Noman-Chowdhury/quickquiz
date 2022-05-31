<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionApiResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'feedback' => $this->when($this->feedback!==null, $this->feedback),
            'point' => $this->point,
            'answer_type' => $this->question_type,
            'answers' => AnswerApiResource::collection($this->options)
        ];
    }
}
