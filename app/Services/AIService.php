<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $provider;
    protected string $model;
    protected string $apiKey;
    protected ?string $apiBase;

    public function __construct()
    {
        $this->provider = config('services.ai.provider', 'openrouter');
        $this->model = $this->getModel();
        $this->apiKey = $this->getApiKey();
        $this->apiBase = $this->getApiBase();
    }

    /**
     * Get the appropriate model based on provider
     */
    protected function getModel(): string
    {
        return match ($this->provider) {
            'openrouter' => config('services.ai.openrouter_model', 'openai/gpt-4o-mini'),
            'minimax' => config('services.ai.minimax_model', 'minimax/-01'),
            default => 'openai/gpt-4o-mini',
        };
    }

    /**
     * Get API key based on provider
     */
    protected function getApiKey(): string
    {
        return match ($this->provider) {
            'openrouter' => config('services.ai.openrouter_api_key', ''),
            'minimax' => config('services.ai.minimax_api_key', ''),
            default => '',
        };
    }

    /**
     * Get API base URL based on provider
     */
    protected function getApiBase(): string
    {
        return match ($this->provider) {
            'openrouter' => 'https://openrouter.ai/api/v1',
            'minimax' => 'https://api.minimax.chat/v1',
            default => 'https://api.openai.com/v1',
        };
    }

    /**
     * Check if the service is configured with a real API key
     */
    public function isConfigured(): bool
    {
        if (empty($this->apiKey)) {
            return false;
        }
        
        // Check for placeholder values
        $placeholders = [
            'your_openrouter_api_key_here',
            'your_minimax_api_key_here',
            'sk-your-',
        ];
        
        foreach ($placeholders as $placeholder) {
            if (str_contains($this->apiKey, $placeholder)) {
                return false;
            }
        }
        
        return strlen($this->apiKey) > 10;
    }

    /**
     * Get the current provider name
     */
    public function getProviderName(): string
    {
        return match ($this->provider) {
            'openrouter' => 'OpenRouter',
            'minimax' => 'MiniMax',
            default => 'Unknown',
        };
    }

    /**
     * Get available models for the current provider
     */
    public function getAvailableModels(): array
    {
        return match ($this->provider) {
            'openrouter' => [
                'openai/gpt-4o' => 'OpenAI GPT-4o',
                'openai/gpt-4o-mini' => 'OpenAI GPT-4o Mini',
                'anthropic/claude-3.5-sonnet' => 'Anthropic Claude 3.5 Sonnet',
                'google/gemini-pro-1.5' => 'Google Gemini Pro 1.5',
                'meta-llama/llama-3-8b-instruct' => 'Meta Llama 3 8B',
                'mistralai/mistral-7b-instruct' => 'Mistral 7B',
                'minimax/ministral-8b' => 'MiniMax Ministral 8B',
            ],
            'minimax' => [
                'minimax/-01' => 'MiniMax-01',
                'minimax/chat' => 'MiniMax Chat',
            ],
            default => [],
        };
    }

    /**
     * Generate a chat completion
     */
    public function chat(array $messages, array $options = []): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('AI service not configured', ['provider' => $this->provider]);
            return null;
        }

        $maxTokens = $options['max_tokens'] ?? 200;
        $temperature = $options['temperature'] ?? 0.7;

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("{$this->apiBase}/chat/completions", [
                    'model' => $this->model,
                    'messages' => $messages,
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            Log::error('AI API error', [
                'provider' => $this->provider,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('AI request failed', [
                'provider' => $this->provider,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Generate a response with system and user prompts
     */
    public function generate(string $systemPrompt, string $userPrompt, array $options = []): ?string
    {
        return $this->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], $options);
    }
}
