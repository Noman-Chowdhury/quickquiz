<?php

namespace App\Http\Resources;

use App\Models\UserQuestionAnswer;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'question' => $this->question->question,
            'feedback' => $this->when($this->question->feedback!==null, $this->question->feedback),
            'point' => $this->question->point,
            'answers' => AnswerApiResource::collection($this->question->options),
            'correct' => $this->is_correct,
            'answered' =>  $this->answer()->exists(),
        ];
    }
}
