<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResponseController extends Controller
{
    public function generate(Request $request, Review $review)
    {
        $user = $request->user();

        // Check usage limits
        if (!$user->canGenerateResponse()) {
            return response()->json([
                'error' => 'Monthly limit reached',
                'message' => 'You have reached your monthly AI response limit. Upgrade to Pro for unlimited responses.',
                'limit_reached' => true,
            ], 403);
        }

        $validated = $request->validate([
            'tone' => 'sometimes|in:professional,friendly,apologetic',
        ]);

        $tone = $validated['tone'] ?? $this->getDefaultTone($review);

        $responseText = $this->generateAIResponse($review, $tone);

        if (!$responseText) {
            return response()->json([
                'error' => 'AI generation failed',
                'message' => 'Failed to generate response. Please try again.',
            ], 500);
        }

        // Save the response
        $response = $review->responses()->create([
            'body' => $responseText,
            'tone' => $tone,
        ]);

        // Mark review as responded if this is the first response
        if ($review->responses()->count() === 1) {
            $review->update(['is_responded' => true]);
        }

        // Increment usage
        $user->incrementUsage();

        return response()->json([
            'response' => $response,
            'message' => 'Response generated successfully',
        ]);
    }

    public function regenerate(Request $request, Response $response)
    {
        $user = $request->user();

        if (!$user->canGenerateResponse()) {
            return response()->json([
                'error' => 'Monthly limit reached',
                'message' => 'You have reached your monthly AI response limit. Upgrade to Pro for unlimited responses.',
                'limit_reached' => true,
            ], 403);
        }

        $validated = $request->validate([
            'tone' => 'sometimes|in:professional,friendly,apologetic',
        ]);

        $tone = $validated['tone'] ?? $response->tone;
        $review = $response->review;

        $responseText = $this->generateAIResponse($review, $tone);

        if (!$responseText) {
            return response()->json([
                'error' => 'AI generation failed',
                'message' => 'Failed to generate response. Please try again.',
            ], 500);
        }

        $response->update([
            'body' => $responseText,
            'tone' => $tone,
        ]);

        $user->incrementUsage();

        return response()->json([
            'response' => $response,
            'message' => 'Response regenerated successfully',
        ]);
    }

    public function history(Review $review)
    {
        $responses = $review->responses()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'responses' => $responses,
        ]);
    }

    private function getDefaultTone(Review $review): string
    {
        return match ($review->sentiment) {
            'positive' => 'friendly',
            'negative' => 'apologetic',
            default => 'professional',
        };
    }

    private function generateAIResponse(Review $review, string $tone): ?string
    {
        $apiKey = config('services.openai.api_key');

        if (!$apiKey || $apiKey === 'your_openai_api_key_here') {
            // Return template response if no API key configured
            return $this->getTemplateResponse($review, $tone);
        }

        try {
            $systemPrompt = $this->buildSystemPrompt($tone);
            $userPrompt = $this->buildUserPrompt($review);

            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'max_tokens' => 200,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            Log::error('OpenAI API error', ['status' => $response->status(), 'body' => $response->body()]);
            return $this->getTemplateResponse($review, $tone);

        } catch (\Exception $e) {
            Log::error('OpenAI request failed', ['error' => $e->getMessage()]);
            return $this->getTemplateResponse($review, $tone);
        }
    }

    private function buildSystemPrompt(string $tone): string
    {
        $toneInstructions = match ($tone) {
            'friendly' => 'Use a warm, conversational tone. Show genuine enthusiasm and personality.',
            'apologetic' => 'Express sincere apologies. Take responsibility and offer to make things right.',
            default => 'Be professional and courteous. Keep it business-appropriate but personable.',
        };

        return "You are a professional reputation management assistant for local businesses. {$toneInstructions}

Guidelines:
- Keep responses to 2-4 sentences max
- Never make up facts about the business
- If the review mentions specific issues, acknowledge them
- End with an invitation for the customer to return or reach out
- Do NOT include placeholders like [Business Name] - use contextual references instead
- Do NOT use ALL CAPS or excessive punctuation";
    }

    private function buildUserPrompt(Review $review): string
    {
        $sentiment = strtoupper($review->sentiment);
        $reviewText = $review->text ?? 'No text provided in this review.';

        return "Review: {$reviewText}
Rating: {$review->rating} stars
Author: {$review->author_name}
Sentiment: {$sentiment}

Generate a response:";
    }

    private function getTemplateResponse(Review $review, string $tone): string
    {
        $templates = [
            'positive' => [
                'friendly' => "Hi {$review->author_name}, thank you so much for this wonderful review! We're thrilled to hear about your positive experience. Your kind words mean the world to us, and we can't wait to see you again soon!",
                'professional' => "Dear {$review->author_name}, thank you for taking the time to share your feedback. We greatly appreciate your kind words and look forward to serving you again.",
                'apologetic' => "Dear {$review->author_name}, thank you for your feedback. We're so glad you had a great experience, and we truly appreciate your support. Please don't hesitate to reach out if you ever need anything!",
            ],
            'negative' => [
                'apologetic' => "Dear {$review->author_name}, we're truly sorry to hear about your experience. This is not the standard of service we strive to provide. We'd love the opportunity to make this right - please contact us directly so we can address your concerns.",
                'professional' => "Dear {$review->author_name}, thank you for bringing this to our attention. We take all feedback seriously and are committed to improving. Please reach out to us directly so we can discuss this further.",
                'friendly' => "Hi {$review->author_name}, I'm sorry to hear your experience wasn't what you expected. That's not the service we want to deliver. Let us make it up to you - please get in touch so we can personally address your concerns.",
            ],
            'neutral' => [
                'friendly' => "Hi {$review->author_name}, thank you for your feedback! We appreciate you taking the time to share your thoughts. If there's anything we can do to improve your experience in the future, please let us know.",
                'professional' => "Dear {$review->author_name}, thank you for your review. We value all feedback and are always looking for ways to improve. We hope to have the opportunity to serve you again.",
                'apologetic' => "Dear {$review->author_name}, thank you for your feedback. We apologize if your experience wasn't fully satisfactory. We'd love to hear more about your thoughts and how we can better serve you in the future.",
            ],
        ];

        $sentimentTemplates = $templates[$review->sentiment ?? 'neutral'] ?? $templates['neutral'];
        return $sentimentTemplates[$tone] ?? $sentimentTemplates['professional'];
    }
}
