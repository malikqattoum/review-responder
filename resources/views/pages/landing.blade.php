@extends('layouts.app')
@section('title', 'Review Responder Pro - AI-Powered Review Responses')

@section('styles')
<style>
/* Landing Page - Premium Design */
.landing {
    overflow-x: hidden;
}

/* Animated Gradient Background */
.hero-gradient {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1e1b4b 100%);
    background-size: 200% 200%;
    animation: gradientShift 15s ease infinite;
    z-index: 0;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Floating Orbs */
.hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.4;
    animation: float 8s ease-in-out infinite;
}

.hero-orb-1 {
    width: 500px;
    height: 500px;
    background: rgba(99, 102, 241, 0.5);
    top: -150px;
    right: -100px;
    animation-delay: 0s;
}

.hero-orb-2 {
    width: 400px;
    height: 400px;
    background: rgba(79, 70, 229, 0.4);
    bottom: -100px;
    left: -50px;
    animation-delay: -4s;
}

.hero-orb-3 {
    width: 300px;
    height: 300px;
    background: rgba(139, 92, 246, 0.3);
    top: 30%;
    left: 20%;
    animation-delay: -2s;
}

@keyframes float {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-30px) scale(1.05); }
}

/* Glass Morphism Card */
.glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
}

/* Navigation */
.landing-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    padding: 20px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(15, 15, 35, 0.8);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.landing-nav.scrolled {
    padding: 15px 40px;
    background: rgba(15, 15, 35, 0.95);
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 700;
    font-size: 1.25rem;
    color: white;
}

.logo-icon {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, #4F46E5, #10B981);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.25rem;
    box-shadow: 0 4px 20px rgba(79, 70, 229, 0.4);
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 32px;
}

.nav-links a {
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
    font-size: 0.9375rem;
    transition: color 0.3s ease;
}

.nav-links a:hover {
    color: white;
}

.nav-cta {
    display: flex;
    gap: 12px;
}

/* Hero Section */
.landing-hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 120px 40px 80px;
    overflow: hidden;
}

.hero-content {
    position: relative;
    z-index: 10;
    text-align: center;
    max-width: 900px;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(139, 92, 246, 0.2);
    border: 1px solid rgba(139, 92, 246, 0.3);
    border-radius: 50px;
    color: #c4b5fd;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 24px;
    animation: fadeInUp 0.8s ease;
}

.hero-badge-dot {
    width: 8px;
    height: 8px;
    background: #a78bfa;
    border-radius: 50%;
    animation: pulse 2s ease infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.hero-title {
    font-size: 4rem;
    font-weight: 800;
    line-height: 1.1;
    color: white;
    margin-bottom: 24px;
    animation: fadeInUp 0.8s ease 0.1s backwards;
}

.hero-title-gradient {
    background: linear-gradient(135deg, #a5b4fc, #818cf8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.375rem;
    color: rgba(255, 255, 255, 0.7);
    max-width: 600px;
    margin: 0 auto 40px;
    line-height: 1.7;
    animation: fadeInUp 0.8s ease 0.2s backwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-cta {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
    animation: fadeInUp 0.8s ease 0.3s backwards;
}

.hero-cta .btn {
    padding: 16px 32px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 12px;
}

.btn-primary-gradient {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
    transition: all 0.3s ease;
}

.btn-primary-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(99, 102, 241, 0.5);
}

.btn-glass {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    backdrop-filter: blur(10px);
}

.btn-glass:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
}

/* Stats Bar */
.stats-bar {
    position: relative;
    z-index: 10;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    max-width: 1000px;
    margin: 0 auto;
    padding: 40px;
    animation: fadeInUp 0.8s ease 0.5s backwards;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 4px;
}

.stat-number-gradient {
    background: linear-gradient(135deg, #4F46E5, #10B981);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 500;
}

/* How It Works */
.how-it-works {
    padding: 120px 40px;
    background: #0a0a1a;
}

.section-header {
    text-align: center;
    margin-bottom: 60px;
}

.section-label {
    display: inline-block;
    padding: 6px 16px;
    background: rgba(79, 70, 229, 0.1);
    border: 1px solid rgba(79, 70, 229, 0.2);
    border-radius: 50px;
    color: #818cf8;
    font-size: 0.8125rem;
    font-weight: 600;
    text-uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 16px;
}

.section-title {
    font-size: 2.75rem;
    font-weight: 800;
    color: white;
    margin-bottom: 16px;
}

.section-subtitle {
    font-size: 1.125rem;
    color: rgba(255, 255, 255, 0.6);
    max-width: 600px;
    margin: 0 auto;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
    max-width: 1200px;
    margin: 0 auto;
}

.step-card {
    padding: 40px;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    transition: all 0.4s ease;
    position: relative;
}

.step-card:hover {
    transform: translateY(-8px);
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(79, 70, 229, 0.3);
}

.step-number {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #4F46E5, #6366f1);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(79, 70, 229, 0.3);
}

.step-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
    margin-bottom: 12px;
}

.step-description {
    color: rgba(255, 255, 255, 0.6);
    line-height: 1.7;
}

/* Features Section */
.landing-features {
    padding: 120px 40px;
    background: linear-gradient(180deg, #0a0a1a 0%, #0f0f2a 100%);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    max-width: 1200px;
    margin: 0 auto;
}

.feature-card {
    padding: 32px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.06);
    transition: all 0.4s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(79, 70, 229, 0.2);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.feature-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.2), rgba(16, 185, 129, 0.2));
    border: 1px solid rgba(79, 70, 229, 0.3);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    color: #818cf8;
}

.feature-icon svg {
    width: 28px;
    height: 28px;
}

.feature-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: white;
    margin-bottom: 10px;
}

.feature-description {
    color: rgba(255, 255, 255, 0.55);
    font-size: 0.9375rem;
    line-height: 1.7;
}

/* Integration Logos */
.integration-logos {
    display: flex;
    gap: 24px;
    justify-content: center;
    margin-top: 20px;
    flex-wrap: wrap;
}

.integration-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}

.integration-logo svg {
    width: 24px;
    height: 24px;
}

/* Testimonials */
.testimonials {
    padding: 120px 40px;
    background: #0a0a1a;
}

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    max-width: 1200px;
    margin: 0 auto;
}

.testimonial-card {
    padding: 32px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
}

.testimonial-stars {
    color: #FBBF24;
    font-size: 1rem;
    margin-bottom: 16px;
    letter-spacing: 4px;
}

.testimonial-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    line-height: 1.8;
    margin-bottom: 24px;
    font-style: italic;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 12px;
}

.testimonial-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #4F46E5, #10B981);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
}

.testimonial-info h4 {
    color: white;
    font-weight: 600;
    margin-bottom: 2px;
}

.testimonial-info p {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.8125rem;
}

/* Pricing Section */
.landing-pricing {
    padding: 120px 40px;
    background: linear-gradient(180deg, #0f0f2a 0%, #0a0a1a 100%);
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 32px;
    max-width: 900px;
    margin: 0 auto;
}

.pricing-card {
    padding: 48px;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.08);
    text-align: center;
    position: relative;
    transition: all 0.4s ease;
}

.pricing-card:hover {
    transform: translateY(-5px);
}

.pricing-card.popular {
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.15), rgba(16, 185, 129, 0.1));
    border-color: rgba(79, 70, 229, 0.3);
}

.pricing-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    padding: 6px 20px;
    background: linear-gradient(135deg, #4F46E5, #6366f1);
    border-radius: 50px;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.pricing-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
    margin-bottom: 8px;
}

.pricing-price {
    font-size: 3.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 4px;
}

.pricing-price span {
    font-size: 1rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.5);
}

.pricing-description {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.875rem;
    margin-bottom: 32px;
}

.pricing-features {
    list-style: none;
    margin: 0 0 32px;
    text-align: left;
}

.pricing-features li {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    color: rgba(255, 255, 255, 0.75);
    font-size: 0.9375rem;
}

.pricing-features li svg {
    width: 20px;
    height: 20px;
    color: #10B981;
    flex-shrink: 0;
}

.pricing-features li.disabled {
    color: rgba(255, 255, 255, 0.3);
}

.pricing-features li.disabled svg {
    color: rgba(255, 255, 255, 0.3);
}

.pricing-cta {
    width: 100%;
    padding: 16px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

/* FAQ Section */
.faq-section {
    padding: 120px 40px;
    background: #0a0a1a;
}

.faq-grid {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.faq-question {
    width: 100%;
    padding: 24px 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: none;
    border: none;
    color: white;
    font-size: 1.0625rem;
    font-weight: 600;
    text-align: left;
    cursor: pointer;
    transition: color 0.3s ease;
}

.faq-question:hover {
    color: #818cf8;
}

.faq-icon {
    width: 24px;
    height: 24px;
    color: rgba(255, 255, 255, 0.4);
    transition: transform 0.3s ease;
}

.faq-item.active .faq-icon {
    transform: rotate(45deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.faq-item.active .faq-answer {
    max-height: 200px;
}

.faq-answer p {
    padding-bottom: 24px;
    color: rgba(255, 255, 255, 0.6);
    line-height: 1.8;
}

/* CTA Section */
.cta-section {
    padding: 120px 40px;
    background: linear-gradient(135deg, #1a1a3e 0%, #2d1f4e 50%, #1a1a3e 100%);
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(79, 70, 229, 0.15) 0%, transparent 50%);
    animation: rotate 30s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.cta-content {
    position: relative;
    z-index: 10;
    text-align: center;
    max-width: 700px;
    margin: 0 auto;
}

.cta-title {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    margin-bottom: 20px;
}

.cta-subtitle {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 40px;
}

/* Footer */
.landing-footer {
    padding: 60px 40px 40px;
    background: #0a0a1a;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 60px;
    margin-bottom: 40px;
}

.footer-brand p {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.9375rem;
    line-height: 1.7;
    margin-top: 16px;
    max-width: 300px;
}

.footer-column h4 {
    color: white;
    font-weight: 600;
    margin-bottom: 20px;
}

.footer-column a {
    display: block;
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.9375rem;
    padding: 6px 0;
    transition: color 0.3s ease;
}

.footer-column a:hover {
    color: #818cf8;
}

.footer-bottom {
    max-width: 1200px;
    margin: 0 auto;
    padding-top: 40px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-bottom p {
    color: rgba(255, 255, 255, 0.4);
    font-size: 0.875rem;
}

.footer-links {
    display: flex;
    gap: 24px;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.4);
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

.footer-links a:hover {
    color: white;
}

/* Responsive */
@media (max-width: 1024px) {
    .features-grid,
    .steps-grid,
    .testimonials-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .footer-content {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .stats-bar {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .features-grid,
    .steps-grid,
    .testimonials-grid,
    .pricing-grid {
        grid-template-columns: 1fr;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .footer-bottom {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .nav-links {
        display: none;
    }
}
</style>
@endsection

@section('content')
<div class="landing">
    <!-- Navigation -->
    <nav class="landing-nav" id="navbar">
        <a href="/" class="logo">
            <div class="logo-icon">R</div>
            <span>ReviewResponder</span>
        </a>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#integrations">Integrations</a>
            <a href="#pricing">Pricing</a>
            <a href="#faq">FAQ</a>
        </div>
        <div class="nav-cta">
            <a href="/login" class="btn btn-glass">Login</a>
            <a href="/register" class="btn btn-primary-gradient">Start Free</a>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="landing-hero">
        <div class="hero-gradient"></div>
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>
        
        <div class="hero-content">
            <div class="hero-badge">
                <span class="hero-badge-dot"></span>
                AI-Powered Review Management
            </div>
            <h1 class="hero-title">
                Respond to Every Review<br>
                <span class="hero-title-gradient">in Seconds, Not Minutes</span>
            </h1>
            <p class="hero-subtitle">
                Stop losing customers to unanswered reviews. Our AI generates human-like responses that sound professional, friendly, and personal — saving you hours every week.
            </p>
            <div class="hero-cta">
                <a href="/register" class="btn btn-primary-gradient">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                    Start Free Trial
                </a>
                <a href="#how-it-works" class="btn btn-glass">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <circle cx="12" cy="12" r="10"/>
                        <polygon points="10 8 16 12 10 16 10 8"/>
                    </svg>
                    Watch Demo
                </a>
            </div>
            
            <div class="integration-logos">
                <div class="integration-logo">
                    <svg viewBox="0 0 24 24" fill="#4285F4"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Google
                </div>
                <div class="integration-logo">
                    <svg viewBox="0 0 24 24" fill="#FF1A1A"><path d="M8.6 2.6A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h-2V4H8v16h4v-4h2v4a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V8H6v2h2z"/></svg>
                    Yelp
                </div>
                <div class="integration-logo">
                    <svg viewBox="0 0 24 24" fill="#10B981"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    OpenRouter
                </div>
            </div>
        </div>
    </section>
    
    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-number stat-number-gradient">10x</div>
            <div class="stat-label">Faster Responses</div>
        </div>
        <div class="stat-item">
            <div class="stat-number stat-number-gradient">500+</div>
            <div class="stat-label">Businesses Trust Us</div>
        </div>
        <div class="stat-item">
            <div class="stat-number stat-number-gradient">50K+</div>
            <div class="stat-label">Reviews Processed</div>
        </div>
        <div class="stat-item">
            <div class="stat-number stat-number-gradient">4.9★</div>
            <div class="stat-label">Customer Rating</div>
        </div>
    </div>
    
    <!-- How It Works -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-header">
            <span class="section-label">How It Works</span>
            <h2 class="section-title">Three Simple Steps</h2>
            <p class="section-subtitle">Get started in minutes and start responding to reviews 10x faster</p>
        </div>
        
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3 class="step-title">Sync Your Reviews</h3>
                <p class="step-description">Connect Google or Yelp for automatic syncing, or import via CSV. Your reviews appear instantly in your dashboard.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">2</div>
                <h3 class="step-title">AI Generates Responses</h3>
                <p class="step-description">Click any review and our AI creates a personalized response in seconds. Choose your tone — professional, friendly, or apologetic.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">3</div>
                <h3 class="step-title">Copy, Edit & Publish</h3>
                <p class="step-description">Review the AI response, make any edits, then copy to clipboard or publish directly. It's that simple.</p>
            </div>
        </div>
    </section>
    
    <!-- Features -->
    <section class="landing-features" id="features">
        <div class="section-header">
            <span class="section-label">Features</span>
            <h2 class="section-title">Everything You Need to Succeed</h2>
            <p class="section-subtitle">Powerful tools to manage your online reputation with ease</p>
        </div>
        
        <div class="features-grid">
            <!-- AI Response Generation -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <h3 class="feature-title">🤖 AI Response Generation</h3>
                <p class="feature-description">Generate human-like responses in seconds. Choose from GPT-4o, Claude, Gemini, or open-source models via OpenRouter.</p>
            </div>
            
            <!-- Multi AI Providers -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12a9 9 0 0 1-9 9m9-9a9 9 0 0 0-9-9m9 9H3m9 9a9 9 0 0 1-9-9m9 9c1.66 0 3-4.03 3-9s-1.34-9-3-9m0 18c-1.66 0-3-4.03-3-9s1.34-9 3-9"/>
                    </svg>
                </div>
                <h3 class="feature-title">🔄 Multiple AI Providers</h3>
                <p class="feature-description">Use OpenAI, Anthropic Claude, Google Gemini, Meta Llama, Mistral, or MiniMax. Switch anytime based on your needs.</p>
            </div>
            
            <!-- Google & Yelp Sync -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"/>
                        <polyline points="1 20 1 14 7 14"/>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                    </svg>
                </div>
                <h3 class="feature-title">🔗 Google & Yelp Sync</h3>
                <p class="feature-description">Automatically sync reviews from Google Business and Yelp. Real-time updates when new reviews come in.</p>
            </div>
            
            <!-- Email Notifications -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <h3 class="feature-title">📧 Smart Notifications</h3>
                <p class="feature-description">Get email alerts for new reviews and urgent alerts for negative reviews. Never miss a customer complaint.</p>
            </div>
            
            <!-- Review Request Automation -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </div>
                <h3 class="feature-title">📩 Review Request Emails</h3>
                <p class="feature-description">Automatically request reviews from happy customers. Grow your review count effortlessly with pre-built email templates.</p>
            </div>
            
            <!-- Sentiment Analysis -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                        <line x1="9" y1="9" x2="9.01" y2="9"/>
                        <line x1="15" y1="9" x2="15.01" y2="9"/>
                    </svg>
                </div>
                <h3 class="feature-title">🎯 Sentiment Analysis</h3>
                <p class="feature-description">Automatically detects positive, neutral, and negative reviews. AI adapts the response tone accordingly.</p>
            </div>
            
            <!-- Analytics Dashboard -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                </div>
                <h3 class="feature-title">📊 Analytics Dashboard</h3>
                <p class="feature-description">Track review trends, sentiment over time, rating distribution, and response metrics. Make data-driven decisions.</p>
            </div>
            
            <!-- Multi-Location -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                </div>
                <h3 class="feature-title">🏢 Multi-Location Support</h3>
                <p class="feature-description">Manage reviews for all your locations from one dashboard. Perfect for franchises and agencies.</p>
            </div>
            
            <!-- Team Management -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3 class="feature-title">👥 Team Management</h3>
                <p class="feature-description">Invite team members with role-based permissions. Admins, managers, and viewers — collaborate efficiently.</p>
            </div>
            
            <!-- Response Templates -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <h3 class="feature-title">📝 Response Templates</h3>
                <p class="feature-description">Fallback templates work even without AI configured. Professional responses for every sentiment and tone.</p>
            </div>
            
            <!-- Usage Tracking -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h3 class="feature-title">📈 Usage Tracking</h3>
                <p class="feature-description">Track your monthly AI usage with clear limits. Know exactly how many responses you've generated.</p>
            </div>
            
            <!-- Secure & Private -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h3 class="feature-title">🔒 Secure & Private</h3>
                <p class="feature-description">Your data is encrypted and secure. API keys stored safely. GDPR compliant data handling.</p>
            </div>
        </div>
    </section>
    
    <!-- Integrations -->
    <section class="how-it-works" id="integrations">
        <div class="section-header">
            <span class="section-label">Integrations</span>
            <h2 class="section-title">Connect Your Favorite Tools</h2>
            <p class="section-subtitle">Seamlessly integrate with the platforms you already use</p>
        </div>
        
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">🔍</div>
                <h3 class="step-title">Google Places</h3>
                <p class="step-description">Search and sync reviews from Google Business Profile. Automatic detection of your business locations.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">⭐</div>
                <h3 class="step-title">Yelp Fusion</h3>
                <p class="step-description">Connect your Yelp Business account to sync reviews directly. Import historical reviews with one click.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">🤖</div>
                <h3 class="step-title">AI Models</h3>
                <p class="step-description">OpenRouter unifies GPT-4, Claude, Gemini, Llama, Mistral, and MiniMax. Choose the best model for your budget.</p>
            </div>
        </div>
    </section>
    
    <!-- Testimonials -->
    <section class="testimonials">
        <div class="section-header">
            <span class="section-label">Testimonials</span>
            <h2 class="section-title">Loved by Businesses</h2>
            <p class="section-subtitle">See what our customers have to say about us</p>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"This tool has saved me countless hours every week. The AI responses sound natural and professional. My Google rating has improved significantly since I started using it."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">M</div>
                    <div class="testimonial-info">
                        <h4>Michael Chen</h4>
                        <p>Restaurant Owner, NYC</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Managing reviews across 5 locations used to be a nightmare. Now I handle everything from one dashboard in minutes. The team management feature is a game changer for our franchise."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">S</div>
                    <div class="testimonial-info">
                        <h4>Sarah Johnson</h4>
                        <p>Operations Director, CleanPro</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"The sentiment analysis is spot-on. It automatically knows when I need to be apologetic versus celebratory. The email alerts for negative reviews have helped us recover customers."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">D</div>
                    <div class="testimonial-info">
                        <h4>Dr. David Park</h4>
                        <p>Park Dental Clinic</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pricing -->
    <section class="landing-pricing" id="pricing">
        <div class="section-header">
            <span class="section-label">Pricing</span>
            <h2 class="section-title">Simple, Transparent Pricing</h2>
            <p class="section-subtitle">Start free, upgrade when you need more</p>
        </div>
        
        <div class="pricing-grid">
            <div class="pricing-card">
                <h3 class="pricing-name">Free</h3>
                <div class="pricing-price">$0 <span>/ month</span></div>
                <p class="pricing-description">Perfect for getting started</p>
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
                        CSV import only
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Basic sentiment analysis
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Analytics dashboard
                    </li>
                    <li class="disabled">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Google/Yelp sync
                    </li>
                    <li class="disabled">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Team management
                    </li>
                </ul>
                <a href="/register" class="btn btn-glass pricing-cta">Get Started</a>
            </div>
            
            <div class="pricing-card popular">
                <span class="pricing-badge">Most Popular</span>
                <h3 class="pricing-name">Pro</h3>
                <div class="pricing-price">$29 <span>/ location / month</span></div>
                <p class="pricing-description">For growing businesses</p>
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
                        Google & Yelp auto-sync
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Email notifications
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Review request emails
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Team management
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Priority support
                    </li>
                </ul>
                <a href="/register" class="btn btn-primary-gradient pricing-cta">Start 14-Day Trial</a>
            </div>
        </div>
    </section>
    
    <!-- FAQ -->
    <section class="faq-section" id="faq">
        <div class="section-header">
            <span class="section-label">FAQ</span>
            <h2 class="section-title">Common Questions</h2>
            <p class="section-subtitle">Everything you need to know about Review Responder Pro</p>
        </div>
        
        <div class="faq-grid">
            <div class="faq-item active">
                <button class="faq-question">
                    Which AI models can I use?
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>You can use any model through OpenRouter: GPT-4o, GPT-4o Mini, Claude 3.5 Sonnet, Gemini Pro 1.5, Llama 3, Mistral 7B, and MiniMax models. Choose based on your budget and needs. You can also use MiniMax directly for even lower costs.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button class="faq-question">
                    How does the Google and Yelp sync work?
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>On the Pro plan, you connect your Google Business Profile and Yelp account using API keys. We then automatically sync all your reviews every few hours. New reviews appear in your dashboard instantly with notifications.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button class="faq-question">
                    Can I edit the AI-generated responses?
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Absolutely! Every AI-generated response is fully editable before you use it. You can adjust the tone, add personal touches, or completely rewrite it. We believe you should always have final control over your brand voice.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button class="faq-question">
                    How does team management work?
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Invite team members and assign roles: Admins get full access, Managers can handle reviews and responses, and Viewers can see everything but can't make changes. Perfect for agencies managing multiple clients.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button class="faq-question">
                    What are review request emails?
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Pro users can send review request emails to their customers asking for reviews. We provide pre-built email templates that you can customize. This helps grow your review count organically.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Transform Your Review Management?</h2>
            <p class="cta-subtitle">Join 500+ businesses already using Review Responder Pro. Start free today — no credit card required.</p>
            <div class="hero-cta">
                <a href="/register" class="btn btn-primary-gradient">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                    Get Started Free
                </a>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="landing-footer">
        <div class="footer-content">
            <div class="footer-brand">
                <a href="/" class="logo">
                    <div class="logo-icon">R</div>
                    <span>ReviewResponder</span>
                </a>
                <p>AI-powered review response software that saves you time and helps your business shine online.</p>
            </div>
            
            <div class="footer-column">
                <h4>Product</h4>
                <a href="#features">Features</a>
                <a href="#pricing">Pricing</a>
                <a href="#faq">FAQ</a>
            </div>
            
            <div class="footer-column">
                <h4>Company</h4>
                <a href="#">About Us</a>
                <a href="#">Blog</a>
                <a href="#">Careers</a>
            </div>
            
            <div class="footer-column">
                <h4>Legal</h4>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2026 Review Responder Pro. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Twitter</a>
                <a href="#">LinkedIn</a>
                <a href="#">GitHub</a>
            </div>
        </div>
    </footer>
</div>

<script>
// FAQ Accordion
document.querySelectorAll('.faq-question').forEach(button => {
    button.addEventListener('click', () => {
        const item = button.parentElement;
        item.classList.toggle('active');
    });
});

// Navbar scroll effect
window.addEventListener('scroll', () => {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
@endsection