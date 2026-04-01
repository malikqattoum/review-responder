<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;

class SubscriptionController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'subscription' => [
                'tier' => $user->subscription_tier,
                'status' => $user->subscription_status,
                'is_pro' => $user->isPro(),
            ],
        ]);
    }

    public function checkout(Request $request)
    {
        $user = $request->user();
        $apiKey = config('services.stripe.secret');

        if (!$apiKey || strpos($apiKey, 'your_stripe_secret') !== false) {
            return response()->json([
                'error' => 'Stripe not configured',
                'message' => 'Stripe is not configured on this server.',
            ], 503);
        }

        try {
            Stripe::setApiKey($apiKey);

            // Create or get Stripe customer
            if (!$user->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            }

            // Create checkout session
            $session = Session::create([
                'customer' => $user->stripe_customer_id,
                'mode' => 'subscription',
                'line_items' => [
                    [
                        'price' => config('services.stripe.pro_price_id'), // Set this in config
                        'quantity' => 1,
                    ],
                ],
                'success_url' => config('app.url') . '/billing?success=true',
                'cancel_url' => config('app.url') . '/billing?canceled=true',
            ]);

            return response()->json([
                'checkout_url' => $session->url,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Checkout failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        if (!$webhookSecret || strpos($webhookSecret, 'whsec_') === false) {
            return response()->json(['error' => 'Webhook not configured'], 400);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $webhookSecret
            );

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $user = User::where('stripe_customer_id', $session->customer)->first();
                    if ($user) {
                        $user->update([
                            'subscription_tier' => 'pro',
                            'subscription_status' => 'active',
                        ]);
                    }
                    break;

                case 'customer.subscription.deleted':
                    $subscription = $event->data->object;
                    $user = User::where('stripe_customer_id', $subscription->customer)->first();
                    if ($user) {
                        $user->update([
                            'subscription_tier' => 'free',
                            'subscription_status' => 'canceled',
                        ]);
                    }
                    break;
            }

            return response()->json(['received' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook failed'], 400);
        }
    }
}
