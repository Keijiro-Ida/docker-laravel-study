<?php

namespace App\UseCases\Reflection;

use App\Models\Reflection;
use App\Models\User;
use App\Dto\Reflection\ReflectionData;

class UpdateReflectionUseCase
{
    public function handle(User $user, Reflection $reflection, ReflectionData $data): Reflection
    {
        if($reflection->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $reflection->update([
            'quote' => $data->quote,
            'response' => $data->response,
        ]);

        return $reflection;

    }
}
