<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Quiz;

class QuizPolicy
{
    public function view(User $user, Quiz $quiz): bool
    {
        return $quiz->is_published
            && $quiz->lesson
            && $quiz->lesson->is_published;
    }

    public function attempt(User $user, Quiz $quiz): bool
    {
        return $this->view($user, $quiz);
    }
}