@extends('layouts.app')
@section('title', 'Dashboard - Review Responder Pro')

@section('content')
<div class="app-layout">
    @include('layouts.partials.sidebar')
    
    <main class="main-content">
        <header class="header">
            <div class="header-left">
                <h1 class="page-title">Dashboard</h1>
            </div>
            <div class="header-right">
                <div class="business-selector">
                    <select id="business-select">
                        <option value="">Loading...</option>
                    </select>
                </div>
                <button class="btn btn-primary" onclick="showImportModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Import
                </button>
            </div>
        </header>
        
        <div class="content">
            <!-- Usage Bar -->
            <div id="usage-bar-container" class="card mb-4 hidden">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 0.875rem; color: var(--text-secondary);">Monthly AI Responses</span>
                    <a href="/billing" style="font-size: 0.875rem;">Upgrade to Pro</a>
                </div>
                <div style="background: var(--border-light); border-radius: 9999px; height: 8px; overflow: hidden;">
                    <div id="usage-bar" style="background: var(--primary); height: 100%; width: 0%; transition: width 0.3s ease;"></div>
                </div>
                <p id="usage-text" style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 8px;"></p>
            </div>
            
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Reviews</div>
                    <div class="stat-value" id="stat-total">-</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Positive</div>
                    <div class="stat-value" id="stat-positive" style="color: var(--sentiment-positive);">-</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Negative</div>
                    <div class="stat-value" id="stat-negative" style="color: var(--sentiment-negative);">-</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Needs Response</div>
                    <div class="stat-value" id="stat-unresponded" style="color: var(--accent);">-</div>
                </div>
            </div>
            
            <!-- Recent Reviews -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Reviews</h3>
                    <a href="/reviews" class="btn btn-outline btn-sm">View All</a>
                </div>
                <div id="reviews-list">
                    <div class="loading" style="margin: 40px auto;"></div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Import Modal -->
<div id="import-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Import Reviews</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Business Location</label>
                <select id="import-business-select" class="form-select">
                    <option value="">Select business...</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">CSV Data</label>
                <textarea id="import-csv" class="form-textarea" rows="10" placeholder="Paste your CSV data here...&#10;Format: external_id, source, author_name, rating, text, date"></textarea>
                <p class="form-hint">Export from Google Business or Yelp in CSV format</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
            <button id="import-submit-btn" class="btn btn-primary">Import Reviews</button>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div id="response-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Generate Response</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="response-review-id">
            <div id="response-review-content"></div>
            
            <div class="form-group">
                <label class="form-label">Response Tone</label>
                <select id="response-tone" class="form-select">
                    <option value="professional">Professional</option>
                    <option value="friendly">Friendly</option>
                    <option value="apologetic">Apologetic</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Generated Response</label>
                <textarea id="response-body" class="form-textarea" rows="4" placeholder="Click 'Generate' to create a response..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button id="regenerate-response-btn" class="btn btn-outline" onclick="regenerateResponse()">Regenerate</button>
            <button id="copy-response-btn" class="btn btn-outline" onclick="copyResponse()">Copy</button>
            <button id="generate-response-btn" class="btn btn-primary">Generate</button>
        </div>
    </div>
</div>
@endsection
