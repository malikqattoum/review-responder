<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $businessName;
    protected string $customerName;
    protected string $reviewLink;
    protected string $source;

    public function __construct(
        string $businessName, 
        string $customerName, 
        string $reviewLink = '',
        string $source = 'google'
    ) {
        $this->businessName = $businessName;
        $this->customerName = $customerName;
        $this->reviewLink = $reviewLink;
        $this->source = $source;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $greeting = !empty($this->customerName) 
            ? "Hi {$this->customerName}," 
            : "Hi there,";

        $message = (new MailMessage)
            ->subject(" We'd love your feedback! - {$this->businessName}")
            ->greeting($greeting)
            ->line("We hope you enjoyed your recent experience with {$this->businessName}!")
            ->line("Your feedback means the world to us and helps other customers make informed decisions.")
            ->line("It only takes 30 seconds to share your thoughts!");

        if (!empty($this->reviewLink)) {
            $message->action('Leave a Review', $this->reviewLink);
        } else {
            $sourceLink = $this->getSourceLink();
            $message->action("Leave a Review on " . ucfirst($this->source), $sourceLink);
        }

        $message->line("Thank you for being a valued customer!")
            ->line("- The {$this->businessName} Team");

        return $message;
    }

    protected function getSourceLink(): string
    {
        return match($this->source) {
            'google' => 'https://search.google.com/local/reviews',
            'yelp' => 'https://www.yelp.com/writeareview',
            'facebook' => 'https://facebook.com',
            default => 'https://google.com',
        };
    }

    public function toArray(object $notifiable): array
    {
        return [
            'business_name' => $this->businessName,
            'customer_name' => $this->customerName,
            'review_link' => $this->reviewLink,
            'source' => $this->source,
        ];
    }
}
