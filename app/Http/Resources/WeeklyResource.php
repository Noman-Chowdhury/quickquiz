<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WeeklyResource extends JsonResource
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
            'id' => encrypt($this->id),
            'name' => $this->title,
            'total_marks' => $this->questions()->sum('point'),
            'total_questions' => $this->questions()->count(),
            'answered' => $this->questions()->whereIn('id',auth('sanctum')->user()->submittedQuestions()->pluck('question_id'))->count(),
            'marks_got'=> $this->questions()
                ->whereIn('id',auth('sanctum')->user()->submittedQuestions()->where('is_correct', true)->pluck('question_id'))
                ->sum('point'),
            'exam_date' => auth('sanctum')->user()->submittedQuestions()->where('quiz_id', $this->id)->first()->answer_time,
            'status' => $this->expired_at < today() ? 'Expired' : 'Ongoing'
        ];
    }
}
