<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->hu_id === (int) $id;
<<<<<<< HEAD
});
<<<<<<< HEAD

// Private channel for conversation messages
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }
    
    // Check if user is part of this conversation
    return in_array($user->hu_id, [$conversation->student_id, $conversation->customer_id]);
=======
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
});
=======
>>>>>>> develop
