<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $rating = str_repeat('⭐', $this->review->rating);
        $sentimentEmoji = $this->review->sentiment === 'negative' ? '🚨' : 
                          ($this->review->sentiment === 'positive' ? '🎉' : '📝');

        $subject = "{$sentimentEmoji} New {$this->review->sentiment} review on {$this->businessName}";

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hi {$notifiable->name},")
            ->line("You have received a new review!")
            ->line("**Business:** {$this->businessName}")
            ->line("**Rating:** {$rating} ({$this->review->rating}/5)")
            ->line("**Sentiment:** " . ucfirst($this->review->sentiment))
            ->line("**Author:** {$this->review->author_name}")
            ->line("**Review:**")
            ->line("> " . ($this->review->text ?? 'No review text'))
            ->action('Respond Now', url('/reviews'))
            ->line('This review was automatically imported to your Review Responder Pro account.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'business_id' => $this->review->business_id,
            'sentiment' => $this->review->sentiment,
            'rating' => $this->review->rating,
            'author_name' => $this->review->author_name,
        ];
    }
}
