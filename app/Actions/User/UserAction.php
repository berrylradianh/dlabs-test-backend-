<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserAction
{
    public function getAllUsers(int $page, int $size)
    {
        return User::paginate($size, ['*'], 'page', $page);
    }



    public function getUser(int $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            throw new ModelNotFoundException('User not found');
        }

        return $user;
    }

    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function updateUser(int $userId, array $data)
    {
        $user = User::find($userId);

        if (!$user) {
            throw new ModelNotFoundException('User not found');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user;
    }

    public function deleteUser(int $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            throw new ModelNotFoundException('User not found');
        }

        $user->delete();
    }
}
