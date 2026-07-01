<?php

namespace App\Http\Controllers\Api\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatMessage;
use App\Services\Chatbot\ChatbotContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    protected $contextService;

    public function __construct(ChatbotContextService $contextService)
    {
        $this->contextService = $contextService;
    }

    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = auth('api')->user();
        $userName = $user ? $user->name : 'Customer';

        // 1. Fetch dynamic data from the Context Service
        $dynamicContext = $this->contextService->getContextForMessage($request->message, $user);

        // 2. Fetch conversation history (last 6 messages to optimize token usage and server speed)
        $history = ChatMessage::where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get()
            ->reverse(); // Reverse to maintain chronological order (oldest to newest)

        $contents = [];

        // 3. Map conversation history to the format required by Google Gemini
        foreach ($history as $chat) {
            $contents[] = [
                'role'  => $chat->role, // 'user' or 'model'
                'parts' => [['text' => $chat->message]]
            ];
        }

        // 4. Prepare the system prompt embedded with dynamic context and the new user message
        $systemPrompt = "You are an AI assistant for the Sell-io platform.
Username: {$userName}.

Live system information (use this only if helpful for the response):
{$dynamicContext}

Current user question: " . $request->message;

        // Add the current message to the array
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $systemPrompt]]
        ];

        // 5. Send the entire conversation history and context to Google Gemini API
        $response = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
            'contents' => $contents
        ]);

        if ($response->successful()) {
            $reply = $response->json('candidates.0.content.parts.0.text');

            // 6. Save the conversation exchange in the database for future context
            ChatMessage::create([
                'user_id' => $user->id,
                'role'    => 'user',
                'message' => $request->message // Save clean user message without the prompt context
            ]);

            ChatMessage::create([
                'user_id' => $user->id,
                'role'    => 'model',
                'message' => $reply // Save AI assistant response
            ]);

            return response()->json([
                'status' => true,
                'data'   => [
                    'reply' => $reply
                ]
            ], 200);
        }

        // Handle Rate Limit (Error 429)
        if ($response->status() === 429) {
            return response()->json([
                'status'  => false,
                'message' => 'The AI assistant is experiencing high traffic right now. Please wait a minute and try again.'
            ], 429);
        }

        // Handle other errors
        return response()->json([
            'status'  => false,
            'message' => 'The AI assistant is currently unavailable.',
            'google_error' => $response->json()
        ], 500);
    }

    public function getHistory()
    {
        $history = ChatMessage::where('user_id', auth('api')->id())
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['status' => true, 'data' => $history]);
    }
}
