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
                <button class="btn btn-outline" onclick="showSyncModal()" style="margin-right: 8px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <polyline points="1 20 1 14 7 14"></polyline>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                    </svg>
                    Sync
                </button>
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

<!-- Sync Modal -->
<div id="sync-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">🔗 Sync Reviews</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p style="color: var(--text-secondary); margin-bottom: 20px;">
                Automatically sync reviews from Google or Yelp. Make sure you've configured your API keys in Settings first.
            </p>
            
            <div id="sync-business-info" style="margin-bottom: 20px;"></div>
            
            <div style="display: grid; gap: 16px;">
                <button class="btn btn-outline" onclick="syncGoogle()" style="width: 100%; padding: 16px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" style="margin-right: 8px;">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Sync from Google
                </button>
                
                <button class="btn btn-outline" onclick="syncYelp()" style="width: 100%; padding: 16px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" style="margin-right: 8px;">
                        <path fill="#FF1A1A" d="M8.6 2.6A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h-2V4H8v16h4v-4h2v4a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V8H6v2h2z"/>
                    </svg>
                    Sync from Yelp
                </button>
            </div>
            
            <div id="sync-status" style="margin-top: 20px;"></div>
        </div>
    </div>
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

@section('scripts')
<script>
function showImportModal() {
    $('#import-modal').addClass('active');
}

function showSyncModal() {
    const businessId = $('#business-select').val();
    const businessName = $('#business-select option:selected').text();
    
    if (!businessId) {
        showToast('Please select a business first', 'error');
        return;
    }
    
    $('#sync-business-info').html('<strong>Syncing:</strong> ' + businessName);
    $('#sync-status').html('');
    $('#sync-modal').addClass('active');
}

function syncGoogle() {
    const businessId = $('#business-select').val();
    
    $('#sync-status').html('<div class="loading"></div> Syncing from Google...');
    
    apiRequest('GET', '/integrations/status')
        .then(data => {
            if (!data.google_has_real_key) {
                $('#sync-status').html('<p style="color: var(--danger);">⚠️ Google API key not configured. Please add your key in Settings.</p>');
                return;
            }
            
            const businessName = $('#business-select option:selected').text();
            
            apiRequest('POST', '/integrations/google/search', {
                name: businessName,
                address: ''
            })
            .then(searchResult => {
                if (searchResult.error) {
                    $('#sync-status').html('<p style="color: var(--danger);">' + searchResult.error + '</p>');
                    return;
                }
                
                return apiRequest('POST', '/integrations/google/sync', {
                    business_id: parseInt(businessId),
                    place_id: searchResult.business.place_id
                });
            })
            .then(syncResult => {
                if (syncResult) {
                    let statusHtml = '<div style="color: var(--secondary);"><strong>✅ Sync Complete!</strong><br>Imported: ' + syncResult.imported + ' reviews<br>Skipped (duplicates): ' + syncResult.skipped;
                    if (syncResult.new_negative_reviews > 0) {
                        statusHtml += '<br><br>⚠️ ' + syncResult.new_negative_reviews + ' negative reviews - check your email!';
                    }
                    statusHtml += '</div>';
                    $('#sync-status').html(statusHtml);
                    showToast('Synced ' + syncResult.imported + ' reviews from Google!', 'success');
                    loadReviews(currentBusinessId, currentFilter);
                    loadDashboardStats();
                }
            })
            .catch(err => {
                $('#sync-status').html('<p style="color: var(--danger);">Sync failed: ' + err.message + '</p>');
            });
        })
        .catch(err => {
            showToast('Failed to check integration status', 'error');
        });
}

function syncYelp() {
    const businessId = $('#business-select').val();
    
    $('#sync-status').html('<div class="loading"></div> Syncing from Yelp...');
    
    apiRequest('GET', '/integrations/status')
        .then(data => {
            if (!data.yelp_has_real_key) {
                $('#sync-status').html('<p style="color: var(--danger);">⚠️ Yelp API key not configured. Please add your key in Settings.</p>');
                return;
            }
            
            const businessName = $('#business-select option:selected').text();
            
            apiRequest('POST', '/integrations/yelp/search', {
                name: businessName,
                address: ''
            })
            .then(searchResult => {
                if (searchResult.error) {
                    $('#sync-status').html('<p style="color: var(--danger);">' + searchResult.error + '</p>');
                    return;
                }
                
                return apiRequest('POST', '/integrations/yelp/sync', {
                    business_id: parseInt(businessId),
                    yelp_id: searchResult.business.yelp_id
                });
            })
            .then(syncResult => {
                if (syncResult) {
                    let statusHtml = '<div style="color: var(--secondary);"><strong>✅ Sync Complete!</strong><br>Imported: ' + syncResult.imported + ' reviews<br>Skipped (duplicates): ' + syncResult.skipped;
                    if (syncResult.new_negative_reviews > 0) {
                        statusHtml += '<br><br>⚠️ ' + syncResult.new_negative_reviews + ' negative reviews - check your email!';
                    }
                    statusHtml += '</div>';
                    $('#sync-status').html(statusHtml);
                    showToast('Synced ' + syncResult.imported + ' reviews from Yelp!', 'success');
                    loadReviews(currentBusinessId, currentFilter);
                    loadDashboardStats();
                }
            })
            .catch(err => {
                $('#sync-status').html('<p style="color: var(--danger);">Sync failed: ' + err.message + '</p>');
            });
        })
        .catch(err => {
            showToast('Failed to check integration status', 'error');
        });
}
</script>
@endsection
