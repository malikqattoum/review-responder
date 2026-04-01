@extends('layouts.app')
@section('title', 'Login - Review Responder Pro')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo" style="justify-content: center; margin-bottom: 20px;">
                <div class="logo-icon">R</div>
            </div>
            <h1>Welcome Back</h1>
            <p>Sign in to manage your reviews</p>
        </div>
        
        <form id="login-form" class="auth-form">
            <div class="form-group">
                <label class="form-label" for="login-email">Email</label>
                <input type="email" id="login-email" class="form-input" placeholder="you@example.com" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="login-password">Password</label>
                <input type="password" id="login-password" class="form-input" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
        </form>
        
        <div class="auth-footer">
            Don't have an account? <a href="/register">Sign up</a>
        </div>
    </div>
</div>
@endsection
