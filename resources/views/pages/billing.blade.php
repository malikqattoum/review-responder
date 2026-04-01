@extends('layouts.app')
@section('title', 'Billing - Review Responder Pro')

@section('content')
<div class="app-layout">
    @include('layouts.partials.sidebar')
    
    <main class="main-content">
        <header class="header">
            <div class="header-left">
                <h1 class="page-title">Billing</h1>
            </div>
        </header>
        
        <div class="content">
            <!-- Current Plan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Current Plan</h3>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span id="current-plan-name" class="badge badge-free" style="font-size: 1rem; padding: 8px 16px;">Free Plan</span>
                        <p class="text-muted mt-4" id="current-plan-desc">10 AI responses per month</p>
                    </div>
                    <div>
                        <button id="upgrade-btn" class="btn btn-primary" onclick="upgradeToPro()">
                            Upgrade to Pro
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Usage -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">This Month's Usage</h3>
                </div>
                <div id="usage-display">
                    <div class="loading" style="margin: 20px auto;"></div>
                </div>
            </div>
            
            <!-- Pricing -->
            <div class="pricing-grid" style="max-width: 800px;">
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
                    </ul>
                    <button class="btn btn-outline" disabled>Current Plan</button>
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
                    <button class="btn btn-primary" onclick="upgradeToPro()">Upgrade Now</button>
                </div>
            </div>
            
            <!-- FAQ -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Frequently Asked Questions</h3>
                </div>
                <div style="display: grid; gap: 20px;">
                    <div>
                        <h4 style="margin-bottom: 8px;">Can I cancel anytime?</h4>
                        <p class="text-muted">Yes, you can cancel your subscription at any time. You'll continue to have access until the end of your billing period.</p>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 8px;">What happens to my data if I downgrade?</h4>
                        <p class="text-muted">Your reviews and responses are always saved. You can still view and manage them, but AI generation will be limited to your free tier quota.</p>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 8px;">Do unused responses roll over?</h4>
                        <p class="text-muted">No, your monthly quota resets each month. We recommend upgrading if you consistently need more responses.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadBillingInfo();
});

function loadBillingInfo() {
    Promise.all([
        apiRequest('GET', '/subscription'),
        apiRequest('GET', '/usage'),
        apiRequest('GET', '/businesses')
    ]).then(([subData, usageData, businessesData]) => {
        const sub = subData.subscription;
        const usage = usageData.usage;
        const businessCount = businessesData.businesses.length;
        
        if (sub.is_pro) {
            $('#current-plan-name').attr('class', 'badge badge-pro').text('Pro Plan');
            $('#current-plan-desc').text('Unlimited AI responses');
            $('#upgrade-btn').text('Manage Subscription').attr('onclick', 'manageSubscription()');
        } else {
            $('#current-plan-name').attr('class', 'badge badge-free').text('Free Plan');
            $('#upgrade-btn').show();
        }
        
        let usageHtml = '';
        if (usage.is_pro) {
            usageHtml = `
                <div style="display: flex; align-items: center; gap: 12px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2" width="24" height="24">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span style="color: var(--secondary); font-weight: 500;">Unlimited responses this month</span>
                </div>
            `;
        } else {
            usageHtml = `
                <div style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span>${usage.reviews_used} of ${usage.reviews_limit} responses used</span>
                        <span>${usage.remaining} remaining</span>
                    </div>
                    <div style="background: var(--border-light); border-radius: 9999px; height: 8px; overflow: hidden;">
                        <div style="background: ${usage.is_limit_reached ? 'var(--danger)' : 'var(--primary)'}; height: 100%; width: ${Math.min(100, (usage.reviews_used / usage.reviews_limit) * 100)}%; transition: width 0.3s;"></div>
                    </div>
                </div>
                ${usage.is_limit_reached ? '<p class="text-danger">Limit reached. Upgrade to Pro for unlimited responses.</p>' : ''}
            `;
        }
        $('#usage-display').html(usageHtml);
    });
}

function upgradeToPro() {
    apiRequest('POST', '/subscription/checkout')
        .then(data => {
            if (data.checkout_url) {
                window.location.href = data.checkout_url;
            } else {
                showToast('Stripe not configured yet. Contact support.', 'info');
            }
        })
        .catch(err => {
            showToast('Unable to start checkout. Please try again.', 'error');
        });
}

function manageSubscription() {
    showToast('Subscription management coming soon', 'info');
}
</script>
@endsection
