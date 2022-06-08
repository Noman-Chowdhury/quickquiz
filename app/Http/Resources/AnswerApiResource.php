<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\UserQuestionAnswer;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerApiResource extends JsonResource
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
            'id' => $this->id,
            'option' => $this->option,
            'is_correct_answer' => $this->when($request->route()->uri() === 'api/user/submitted/questions', $this->is_correct_option),
            'is_user_selected' => $this->when($request->route()->uri() === 'api/user/submitted/questions', UserQuestionAnswer::whereHas('userQuestion', function ($q) {
                return $q->where('user_id', auth('sanctum')->user()->id);
            })->where(['option_id' => $this->id])->exists()),
        ];
    }
}
