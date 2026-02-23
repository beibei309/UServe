<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ChatRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get user's conversations
        $conversations = Conversation::where(function($query) use ($user) {
            $query->where('student_id', $user->id)
                  ->orWhere('customer_id', $user->id);
        })
        ->with(['student', 'customer', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
        ->orderBy('updated_at', 'desc')
        ->get();

        // Add helper method to get the other user in conversation
        $conversations->each(function($conversation) use ($user) {
            $conversation->otherUser = $conversation->student_id === $user->id 
                ? $conversation->customer 
                : $conversation->student;
            $conversation->lastMessage = $conversation->messages->first();
        });

        // Get pending chat requests for students
        $pendingRequests = collect();
        if ($user->role === 'helper') {
            $pendingRequests = ChatRequest::where('recipient_id', $user->id)
                ->where('status', 'pending')
                ->with('requester')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Get sent requests for community users
        $sentRequests = collect();
        if ($user->role === 'community') {
            $sentRequests = ChatRequest::where('requester_id', $user->id)
                ->where('status', 'pending')
                ->with('recipient')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('chat.index', compact('conversations', 'pendingRequests', 'sentRequests'));
    }

    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        
        // Check if user is part of this conversation
        if (!in_array($user->id, [$conversation->student_id, $conversation->customer_id])) {
            abort(403, 'You are not authorized to view this conversation.');
        }

        // Load messages with sender information
        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();
        
        // Get the other user in the conversation
        $otherUser = $user->id === $conversation->student_id ? $conversation->customer : $conversation->student;
        
        // If the other user is a student, get their services for the application modal
        $providerServices = [];
        if ($otherUser && $otherUser->role === 'student') {
            $providerServices = $otherUser->studentServices()->where('status', 'available')->get();
        }

        // Get service applications for this conversation
        $serviceApplications = \App\Models\ServiceApplication::where('conversation_id', $conversation->id)
            ->with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('chat.show', compact('conversation', 'messages', 'otherUser', 'providerServices', 'serviceApplications'));
    }
}