<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $users = User::orderBy('name')->get();

        return view('livewire.users.index', [
            'users' => $users,
        ]);
    }

    public function delete(User $user): void
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            session()->flash('error', __('You cannot delete your own account.'));
            return;
        }

        $user->delete();
    }
}

