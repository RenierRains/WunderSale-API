<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Item;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function items()
    {
        $items = Item::all();
        return view('admin.items', compact('items'));
    }

    public function destroyUser(User $user)
    {
        // maybe add checks for admin abuse (real funny)
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }
        
}
