<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\ConversationParticipant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function getConversations()
    {
        $user = Auth::user();
        
        // 1. Get existing conversations
        $conversations = $user->conversations()
            ->with(['users', 'latestMessage'])
            ->get();

        $existingConversationUserIds = $conversations->flatMap(function($conv) use ($user) {
            return $conv->users->pluck('id');
        })->unique()->toArray();

        // 2. Get mutual followers and followed users
        $followedIds = $user->following()->pluck('users.id')->toArray();
        $followerIds = $user->followers()->pluck('users.id')->toArray();
        $mutualIds = array_intersect($followedIds, $followerIds);
        
        // Users to show who don't have conversations yet
        $contactIds = array_unique(array_merge($mutualIds, $followedIds));
        $contacts = User::whereIn('id', $contactIds)
            ->whereNotIn('id', $existingConversationUserIds)
            ->get();

        $formattedConversations = $conversations->map(function ($conv) use ($user) {
            $otherUser = $conv->users->where('id', '!=', $user->id)->first();
            return [
                'id' => $conv->id,
                'other_user' => $otherUser ? [
                    'id' => $otherUser->id,
                    'name' => $otherUser->full_name,
                    'avatar' => $otherUser->avatar_url,
                    'is_doctor' => $otherUser->isApprovedDoctor(),
                ] : null,
                'latest_message' => $conv->latestMessage,
                'unread_count' => 0,
                'is_conversation' => true
            ];
        });

        $formattedContacts = $contacts->map(function ($u) use ($mutualIds) {
            return [
                'id' => null, // No conversation yet
                'other_user' => [
                    'id' => $u->id,
                    'name' => $u->full_name,
                    'avatar' => $u->avatar_url,
                    'is_doctor' => $u->isApprovedDoctor(),
                    'is_mutual' => in_array($u->id, $mutualIds),
                ],
                'latest_message' => null,
                'unread_count' => 0,
                'is_conversation' => false
            ];
        });

        // Combine and sort: Conversations with messages first, then mutuals, then following
        $all = $formattedConversations->concat($formattedContacts);
        
        $sorted = $all->sort(function($a, $b) {
            // Conversations with latest message first
            if ($a['latest_message'] && !$b['latest_message']) return -1;
            if (!$a['latest_message'] && $b['latest_message']) return 1;
            
            // Then conversations without messages
            if ($a['is_conversation'] && !$b['is_conversation']) return -1;
            if (!$a['is_conversation'] && $b['is_conversation']) return 1;

            // Then mutuals
            $aMutual = $a['other_user']['is_mutual'] ?? false;
            $bMutual = $b['other_user']['is_mutual'] ?? false;
            if ($aMutual && !$bMutual) return -1;
            if (!$aMutual && $bMutual) return 1;

            return 0;
        })->values();

        return response()->json($sorted);
    }

    public function getMessages($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'nullable|exists:conversations,id',
            'receiver_id' => 'nullable|exists:users,id',
            'body' => 'required|string',
        ]);

        $senderId = Auth::id();
        $conversationId = $request->conversation_id;

        if (!$conversationId && $request->receiver_id) {
            // Check for existing direct conversation
            $conversation = Conversation::where('type', 'direct')
                ->whereHas('participants', function($q) use ($senderId) {
                    $q->where('user_id', $senderId);
                })
                ->whereHas('participants', function($q) use ($request) {
                    $q->where('user_id', $request->receiver_id);
                })
                ->first();

            if (!$conversation) {
                // Create new conversation
                $conversation = Conversation::create(['type' => 'direct']);
                ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $senderId]);
                ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $request->receiver_id]);
            }
            $conversationId = $conversation->id;
        }

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_user_id' => $senderId,
            'body' => $request->body,
            'message_type' => 'text',
        ]);

        return response()->json($message->load('sender'));
    }

    public function searchUsers(Request $request)
    {
        $query = $request->get('q');
        $currentUser = Auth::user();
        $followedIds = $currentUser->following()->pluck('users.id')->toArray();
        $followerIds = $currentUser->followers()->pluck('users.id')->toArray();
        $mutualIds = array_intersect($followedIds, $followerIds);

        $users = User::where('id', '!=', $currentUser->id)
            ->where(function($q) use ($query) {
                $q->where('fname', 'LIKE', "%{$query}%")
                  ->orWhere('lname', 'LIKE', "%{$query}%")
                  ->orWhere('username', 'LIKE', "%{$query}%");
            })
            ->get()
            ->map(function($u) use ($mutualIds, $followedIds) {
                $priority = 3; // Default
                if (in_array($u->id, $mutualIds)) {
                    $priority = 1; // Mutual
                } elseif (in_array($u->id, $followedIds)) {
                    $priority = 2; // Following only
                }

                return [
                    'id' => $u->id,
                    'name' => $u->full_name,
                    'avatar' => $u->avatar_url,
                    'is_doctor' => $u->isApprovedDoctor(),
                    'priority' => $priority
                ];
            })
            ->sortBy('priority')
            ->values()
            ->take(10);

        return response()->json($users);
    }
}
