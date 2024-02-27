<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Show messages for the authenticated user
    public function index()
{
    $user = Auth::user();

    // Assuming 'messages' is a relation in your User model that fetches all messages
    // related to the user, both sent and received.
    $conversations = $user->messages()
        ->with(['sender', 'receiver']) // Assuming sender and receiver are relations in your Message model
        ->get()
        ->map(function($message) use ($user) {
            // Determine the other party in each message
            return $message->from_user_id == $user->id ? $message->receiver : $message->sender;
        })
        ->unique('id') // Ensure unique users
        ->values(); // Reset keys after filtering

        return view('messages.index', compact('conversations'));
    }
    
    public function create(User $user)
    {
        
        $users = User::where('id', '!=', Auth::id())->get();
        return view('messages.create', compact('users', 'user'));
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);

        $message = Message::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $request->to_user_id,
            'body' => $request->body,
        ]);

      
        broadcast(new MessageSent($message))->toOthers();

        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }

    // AJAX STUFF!!!!!!
    public function show($userId)
    {
        $messages = Message::where(function($query) use ($userId) {
            $query->where('from_user_id', Auth::id())->where('to_user_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('from_user_id', $userId)->where('to_user_id', Auth::id());
        })->latest()->get();

        $otherUser = User::findOrFail($userId);

        return view('messages.show', compact('messages', 'otherUser'));
    }

    // Fetch4 AJAX 
    public function fetchMessages($userId)
    {
        $messages = Message::where(function($query) use ($userId) {
            $query->where('from_user_id', Auth::id())->where('to_user_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('from_user_id', $userId)->where('to_user_id', Auth::id());
        })->latest()->get();

        return response()->json($messages);
    }

    public function compose(User $user)
    {
        return view('messages.compose', compact('user'));
    }
}