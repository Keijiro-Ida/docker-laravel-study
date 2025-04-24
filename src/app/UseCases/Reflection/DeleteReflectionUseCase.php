<?php

namespace App\UseCases\Reflection;

use App\Models\Reflection;
use App\Models\User;
use App\Dto\Reflection\ReflectionData;

class DeleteReflectionUseCase
{
    public function handle(User $user, Reflection $reflection): void
    {
        if($reflection->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $reflection->delete();
    }
}
