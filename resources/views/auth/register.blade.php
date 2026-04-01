@extends('layouts.app')
@section('title', 'Register - Review Responder Pro')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo" style="justify-content: center; margin-bottom: 20px;">
                <div class="logo-icon">R</div>
            </div>
            <h1>Create Account</h1>
            <p>Start responding to reviews in seconds</p>
        </div>
        
        <form id="register-form" class="auth-form">
            <div class="form-group">
                <label class="form-label" for="register-name">Your Name</label>
                <input type="text" id="register-name" class="form-input" placeholder="John Smith" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="register-email">Email</label>
                <input type="email" id="register-email" class="form-input" placeholder="you@example.com" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="register-password">Password</label>
                <input type="password" id="register-password" class="form-input" placeholder="••••••••" minlength="8" required>
                <p class="form-hint">At least 8 characters</p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="register-password_confirmation">Confirm Password</label>
                <input type="password" id="register-password_confirmation" class="form-input" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
        </form>
        
        <div class="auth-footer">
            Already have an account? <a href="/login">Sign in</a>
        </div>
    </div>
</div>
@endsection
