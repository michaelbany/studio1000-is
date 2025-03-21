<?php

namespace App\Http\Controllers;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function edit(Request $request)
    {
        Gate::authorize('edit', User::class);

        return inertia('settings/Users', [
            'users' => User::all(),
            'roles' => RolesEnum::cases(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('update', $user);

        $user->update($request->validate([
            'role' => ['required', Rule::enum(RolesEnum::class)],
        ]));

        return redirect()->back();
    }
}
