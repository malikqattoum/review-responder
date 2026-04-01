@extends('layouts.app')
@section('title', 'Review Responder Pro - AI-Powered Review Responses')

@section('content')
<div class="landing">
    <nav class="landing-nav">
        <div class="logo">
            <div class="logo-icon">R</div>
            <span>Review Responder Pro</span>
        </div>
        <div class="flex gap-4">
            <a href="/login" class="btn btn-outline">Login</a>
            <a href="/register" class="btn btn-primary">Start Free</a>
        </div>
    </nav>
    
    <section class="landing-hero">
        <h1>Respond to Every Review in Seconds, Not Minutes</h1>
        <p>AI-powered responses that sound human, save time, and help your business stand out. Stop losing customers to bad reviews.</p>
        <div class="flex gap-4 justify-center">
            <a href="/register" class="btn btn-primary btn-lg">Get Started Free</a>
            <a href="#features" class="btn btn-outline btn-lg">Learn More</a>
        </div>
    </section>
    
    <section class="landing-features" id="features">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <h3>10x Faster</h3>
                <p>Generate professional responses in seconds. What took 10 minutes now takes 10 seconds.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a10 10 0 1 0 10 10H12V2z"/>
                        <path d="M21 12a9 9 0 0 0-9-9v9l9 9"/>
                    </svg>
                </div>
                <h3>AI-Powered</h3>
                <p>Smart responses that match your tone, acknowledge feedback, and invite customers back.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <h3>Sentiment Analysis</h3>
                <p>Automatically detects positive, neutral, and negative reviews. Respond appropriately every time.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                </div>
                <h3>Multi-Location</h3>
                <p>Manage reviews for all your locations from one dashboard. Perfect for franchises.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <h3>CSV Import</h3>
                <p>Easily import reviews from Google and Yelp. Get started in minutes.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <h3>Free Tier</h3>
                <p>Start free with 10 AI responses per month. Upgrade when you need more.</p>
            </div>
        </div>
    </section>
    
    <section class="landing-pricing" id="pricing">
        <h2 style="text-align: center; margin-bottom: 40px;">Simple, Transparent Pricing</h2>
        <div class="pricing-grid">
            <div class="pricing-card">
                <h3>Free</h3>
                <div class="pricing-price">$0 <span>/ month</span></div>
                <ul class="pricing-features">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        10 AI responses / month
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        1 business location
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        CSV import
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Basic sentiment analysis
                    </li>
                </ul>
                <a href="/register" class="btn btn-outline" style="width: 100%;">Get Started</a>
            </div>
            
            <div class="pricing-card popular">
                <h3>Pro</h3>
                <div class="pricing-price">$29 <span>/ location / month</span></div>
                <ul class="pricing-features">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Unlimited AI responses
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Unlimited locations
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Google & Yelp import
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Priority support
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Export reports
                    </li>
                </ul>
                <a href="/register" class="btn btn-primary" style="width: 100%;">Start 14-Day Trial</a>
            </div>
        </div>
    </section>
    
    <footer style="padding: 40px; text-align: center; color: var(--text-secondary); border-top: 1px solid var(--border);">
        <p>&copy; 2026 Review Responder Pro. All rights reserved.</p>
    </footer>
</div>
@endsection
