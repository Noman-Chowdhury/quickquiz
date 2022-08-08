<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DailyResource extends JsonResource
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
            'my_answer'=> $this->answer ?  $this->answer->option->option : false,
            'correct_answer'=> $this->question->options()->where('is_correct_option', '=', true)->first() ? $this->question->options()->where('is_correct_option', '=', true)->first()->option : 0 ,
        ];
    }
}
