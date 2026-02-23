<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use App\Events\MessageSent;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'conversation_id' => ['required', 'exists:conversations,id'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $conversation = Conversation::findOrFail($data['conversation_id']);
        $user = Auth::user();

        // Check if user is part of this conversation
        if (!in_array($user->id, [$conversation->student_id, $conversation->customer_id])) {
            return response()->json(['error' => 'You are not authorized to send messages in this conversation.'], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $data['message'],
        ]);

        // Load sender relationship for broadcasting
        $message->load('sender');

        // Broadcast the message to all participants in the conversation
        broadcast(new MessageSent($message));

        // Send notification to the other user
        $otherUserId = $conversation->student_id === $user->id 
            ? $conversation->customer_id 
            : $conversation->student_id;
        
        broadcast(new \App\Events\NewMessageNotification($message, $otherUserId));

        // Update conversation timestamp
        $conversation->touch();

        return response()->json([
            'message' => $message,
            'success' => true
        ], 201);
    }

    public function typing(Request $request): JsonResponse
    {
        $data = $request->validate([
            'conversation_id' => ['required', 'exists:conversations,id'],
            'is_typing' => ['required', 'boolean'],
        ]);

        $conversation = Conversation::findOrFail($data['conversation_id']);
        $user = Auth::user();

        // Check if user is part of this conversation
        if (!in_array($user->id, [$conversation->student_id, $conversation->customer_id])) {
            return response()->json(['error' => 'You are not authorized to access this conversation.'], 403);
        }

        // Broadcast typing status
        broadcast(new UserTyping(
            $conversation->id,
            $user->id,
            $user->name,
            $data['is_typing']
        ));

        return response()->json(['success' => true]);
    }
}