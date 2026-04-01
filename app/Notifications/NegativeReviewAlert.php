<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NegativeReviewAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected Review $review;
    protected string $businessName;

    public function __construct(Review $review, string $businessName)
    {
        $this->review = $review;
        $this->businessName = $businessName;
    }

    public function via(object $notifiable): array
    {
        // Urgent notifications via email
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $rating = str_repeat('⭐', $this->review->rating);

        return (new MailMessage)
            ->subject("🚨 URGENT: Negative review on {$this->businessName} - Action required!")
            ->greeting("Hi {$notifiable->name},")
            ->error()
            ->line("⚠️ **URGENT: A negative review requires your immediate attention!**")
            ->line("**Business:** {$this->businessName}")
            ->line("**Rating:** {$rating} ({$this->review->rating}/5)")
            ->line("**Author:** {$this->review->author_name}")
            ->line("**Review Text:**")
            ->line("> " . ($this->review->text ?? 'No review text'))
            ->line("")
            ->line("Negative reviews can damage your online reputation if not addressed quickly.")
            ->action('Respond Immediately', url('/reviews'))
            ->line("")
            ->line("💡 **Tips for responding to negative reviews:**")
            ->line("• Acknowledge the issue and apologize sincerely")
            ->line("• Take responsibility where appropriate")
            ->line("• Offer to make it right offline")
            ->line("• Keep your response professional and calm");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'business_id' => $this->review->business_id,
            'sentiment' => $this->review->sentiment,
            'rating' => $this->review->rating,
            'author_name' => $this->review->author_name,
            'is_urgent' => true,
        ];
    }
}
