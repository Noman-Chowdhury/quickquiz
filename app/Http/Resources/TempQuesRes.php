<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TempQuesRes extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => encrypt($this->id),
            'question' => $this->question,
            'answers' => json_decode($this->options),
            'correct_ans' => $this->correct_ans,
            'reference' => $this->reference,
            'contributor_name' => $this->user->name,
            'contributor_number' => $this->user->phone_number,
            'date' => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }
}
