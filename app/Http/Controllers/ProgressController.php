<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeaderBoardResource;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuestion;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function daily()
    {
        return User::where('email', '=', NULL)->select('name', 'phone_number')->addSelect(['marks' => UserQuestion::whereDay('answer_time', today())->selectRaw('sum(marks) as total')
            ->whereColumn('user_id', 'users.id')
            ->groupBy('user_id')
        ])
            ->orderBy('marks', 'DESC')
            ->get()
            ->toArray();
    }

    public function weekly()
    {
        $now = date('Y-m-d H:i:s');
        $quiz_of_this_week = Quiz::where('publish_at', '<=', $now)->where('expired_at', '>=', $now)->first();
        return User::where('email', '=', NULL)->select('name', 'phone_number')->addSelect(['marks' => UserQuestion::where('quiz_id', $quiz_of_this_week->id)->selectRaw('sum(marks) as total')
            ->whereColumn('user_id', 'users.id')
            ->groupBy('user_id')
        ])
            ->orderBy('marks', 'DESC')
            ->get()
            ->toArray();
    }

    public function overall()
    {
        return User::where('email', '=', NULL)->select('name', 'phone_number')->addSelect(['marks' => UserQuestion::selectRaw('sum(marks) as total')
            ->whereColumn('user_id', 'users.id')
            ->groupBy('user_id')
        ])
            ->orderBy('marks', 'DESC')
            ->get()
            ->toArray();
    }
}
