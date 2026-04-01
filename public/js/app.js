/**
 * Review Responder Pro - JavaScript Application
 */

const API_BASE = '/api';

// ============================================
// Utility Functions
// ============================================

function getToken() {
    return localStorage.getItem('auth_token');
}

function setToken(token) {
    localStorage.setItem('auth_token', token);
}

function removeToken() {
    localStorage.removeItem('auth_token');
}

function getUser() {
    const user = localStorage.getItem('user');
    if (!user || user === 'undefined') return null;
    try {
        return JSON.parse(user);
    } catch (e) {
        return null;
    }
}

function setUser(user) {
    localStorage.setItem('user', JSON.stringify(user));
}

function removeUser() {
    localStorage.removeItem('user');
}

function isLoggedIn() {
    return !!getToken();
}

function apiHeaders() {
    const token = getToken();
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': token ? `Bearer ${token}` : ''
    };
}

function apiRequest(method, endpoint, data = null) {
    const options = {
        method,
        headers: apiHeaders()
    };
    
    if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
        options.body = JSON.stringify(data);
    }
    
    return fetch(`${API_BASE}${endpoint}`, options)
        .then(async response => {
            if (response.status === 401) {
                logout();
                window.location.href = '/login';
                throw new Error('Unauthorized');
            }
            const contentType = response.headers.get('content-type');
            let data;
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
            }
            
            if (!response.ok) {
                const errorMsg = data?.message || data?.errors?.email?.[0] || data?.errors?.password?.[0] || `API Error: ${response.status}`;
                throw new Error(errorMsg);
            }
            
            return data || { success: true };
        });
}

// ============================================
// Toast Notifications
// ============================================

function showToast(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <span class="toast-message">${message}</span>
        <button class="toast-close">&times;</button>
    `;
    
    container.appendChild(toast);
    
    toast.querySelector('.toast-close').addEventListener('click', () => {
        toast.remove();
    });
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// ============================================
// Star Rating
// ============================================

function renderStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += i <= rating ? '★' : '☆';
    }
    return stars;
}

function getSentimentBadge(sentiment) {
    const badges = {
        'positive': '<span class="badge badge-positive">Positive</span>',
        'neutral': '<span class="badge badge-neutral">Neutral</span>',
        'negative': '<span class="badge badge-negative">Negative</span>'
    };
    return badges[sentiment] || '';
}

function getSourceBadge(source) {
    const badges = {
        'google': '<span class="badge badge-google">Google</span>',
        'yelp': '<span class="badge badge-yelp">Yelp</span>',
        'manual': '<span class="badge">Manual</span>'
    };
    return badges[source] || '';
}

// ============================================
// Auth Functions
// ============================================

function login(email, password) {
    return apiRequest('POST', '/login', { email, password })
        .then(data => {
            setToken(data.token);
            setUser(data.user);
            showToast('Login successful!', 'success');
            window.location.href = '/dashboard';
        });
}

function register(name, email, password, password_confirmation) {
    return apiRequest('POST', '/register', { name, email, password, password_confirmation })
        .then(data => {
            setToken(data.token);
            setUser(data.user);
            showToast('Registration successful!', 'success');
            window.location.href = '/dashboard';
        });
}

function logout() {
    return apiRequest('POST', '/logout')
        .catch(() => {})
        .finally(() => {
            removeToken();
            removeUser();
            window.location.href = '/login';
        });
}

function checkAuth() {
    if (!isLoggedIn()) {
        const publicPages = ['/', '/login', '/register'];
        if (!publicPages.includes(window.location.pathname)) {
            window.location.href = '/login';
        }
    } else {
        // Fetch user data
        apiRequest('GET', '/user')
            .then(data => setUser(data.user))
            .catch(() => logout());
    }
}

// ============================================
// Reviews Functions
// ============================================

let currentBusinessId = null;
let currentFilter = 'all';

function loadReviews(businessId = null, sentiment = 'all') {
    currentBusinessId = businessId;
    currentFilter = sentiment;
    
    let endpoint = '/reviews?';
    if (businessId) endpoint += `business_id=${businessId}&`;
    if (sentiment !== 'all') endpoint += `sentiment=${sentiment}&`;
    
    $('#reviews-list').html('<div class="loading" style="margin: 40px auto;"></div>');
    
    apiRequest('GET', endpoint)
        .then(data => {
            renderReviews(data.reviews);
        })
        .catch(err => {
            showToast('Failed to load reviews', 'error');
        });
}

function renderReviews(reviews) {
    const container = $('#reviews-list');
    
    if (!reviews || reviews.length === 0) {
        container.html(`
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <h3>No reviews yet</h3>
                <p>Import your first review or wait for customers to leave feedback.</p>
                <button class="btn btn-primary" onclick="showImportModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Import Reviews
                </button>
            </div>
        `);
        return;
    }
    
    let html = '';
    reviews.forEach(review => {
        const sentimentClass = review.sentiment === 'negative' ? 'negative' : 
                             review.sentiment === 'positive' ? 'positive' : '';
        
        html += `
            <div class="review-card ${sentimentClass}" data-id="${review.id}">
                <div class="review-header">
                    <div class="review-author">
                        <div class="review-avatar">${review.author_name.charAt(0).toUpperCase()}</div>
                        <div class="review-meta">
                            <span class="review-name">${review.author_name}</span>
                            <span class="review-date">${formatDate(review.review_date)}</span>
                        </div>
                    </div>
                    <div class="review-rating">
                        <span class="stars">${renderStars(review.rating)}</span>
                        <span class="review-sentiment">${getSentimentBadge(review.sentiment)}</span>
                        ${getSourceBadge(review.source)}
                    </div>
                </div>
                <div class="review-body">${review.text || 'No review text available.'}</div>
                <div class="review-footer">
                    <div class="review-status ${review.is_responded ? 'responded' : ''}">
                        ${review.is_responded ? 
                            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> Responded' : 
                            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> Needs response'}
                    </div>
                    <div class="review-actions">
                        <button class="btn btn-sm btn-primary" onclick="generateResponse(${review.id})" ${review.is_responded ? '' : ''}>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="M12 2a10 10 0 1 0 10 10H12V2z"></path>
                                <path d="M21 12a9 9 0 0 0-9-9v9l9 9"></path>
                            </svg>
                            ${review.is_responded ? 'Regenerate' : 'Generate'}
                        </button>
                        ${review.is_responded ? `
                            <button class="btn btn-sm btn-outline" onclick="showResponseHistory(${review.id})">
                                History
                            </button>
                        ` : ''}
                        <button class="btn btn-sm btn-ghost" onclick="deleteReview(${review.id})">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function formatDate(dateStr) {
    if (!dateStr) return 'No date';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function generateResponse(reviewId) {
    showResponseModal(reviewId);
}

function showResponseModal(reviewId) {
    apiRequest('GET', `/reviews/${reviewId}`)
        .then(data => {
            const review = data.review;
            $('#response-modal').addClass('active');
            $('#response-review-content').html(`
                <div class="review-body" style="margin-bottom: 16px;">
                    <strong>${review.author_name}</strong> (${renderStars(review.rating)})
                    <p style="margin-top: 8px;">${review.text || 'No review text'}</p>
                </div>
            `);
            $('#response-review-id').val(reviewId);
            $('#response-body').val('');
            $('#response-tone').val(review.sentiment === 'positive' ? 'friendly' : 
                                   review.sentiment === 'negative' ? 'apologetic' : 'professional');
            $('#generate-response-btn').data('review-id', reviewId);
        });
}

function submitGenerateResponse() {
    const reviewId = $('#response-review-id').val();
    const tone = $('#response-tone').val();
    
    $('#generate-response-btn').prop('disabled', true).html('<span class="loading"></span> Generating...');
    
    apiRequest('POST', `/reviews/${reviewId}/generate-response`, { tone })
        .then(data => {
            if (data.limit_reached) {
                showToast('Monthly limit reached. Upgrade to Pro for unlimited responses.', 'error');
                return;
            }
            $('#response-body').val(data.response.body);
            showToast('Response generated!', 'success');
        })
        .catch(err => {
            showToast('Failed to generate response', 'error');
        })
        .finally(() => {
            $('#generate-response-btn').prop('disabled', false).html('Generate');
        });
}

function regenerateResponse() {
    const reviewId = $('#response-review-id').val();
    const tone = $('#response-tone').val();
    const responseId = $('#generate-response-btn').data('response-id');
    
    if (!responseId) {
        // Use the review endpoint to regenerate
        submitGenerateResponse();
        return;
    }
    
    apiRequest('POST', `/responses/${responseId}/regenerate`, { tone })
        .then(data => {
            $('#response-body').val(data.response.body);
            showToast('Response regenerated!', 'success');
        })
        .catch(err => {
            showToast('Failed to regenerate response', 'error');
        });
}

function copyResponse() {
    const text = $('#response-body').val();
    navigator.clipboard.writeText(text).then(() => {
        showToast('Response copied to clipboard!', 'success');
    }).catch(() => {
        showToast('Failed to copy', 'error');
    });
}

function closeModal() {
    $('.modal-overlay').removeClass('active');
}

function showResponseHistory(reviewId) {
    apiRequest('GET', `/reviews/${reviewId}/responses`)
        .then(data => {
            let html = '<div style="max-height: 300px; overflow-y: auto;">';
            if (data.responses && data.responses.length > 0) {
                data.responses.forEach((resp, i) => {
                    html += `
                        <div style="padding: 12px; border-bottom: 1px solid var(--border); ${i === 0 ? 'background: var(--primary-light);' : ''}">
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">
                                ${resp.tone} • ${formatDate(resp.created_at)}
                            </div>
                            <p style="font-size: 0.875rem;">${resp.body}</p>
                            ${i === 0 ? '<button class="btn btn-sm btn-outline mt-4" onclick="loadResponseToModal(' + resp.id + ', ' + reviewId + ')">Use this</button>' : ''}
                        </div>
                    `;
                });
            } else {
                html += '<p style="padding: 20px; text-align: center; color: var(--text-muted);">No response history</p>';
            }
            html += '</div>';
            $('#history-modal .modal-body').html(html);
            $('#history-modal').addClass('active');
        });
}

function deleteReview(reviewId) {
    if (!confirm('Are you sure you want to delete this review?')) return;
    
    apiRequest('DELETE', `/reviews/${reviewId}`)
        .then(() => {
            showToast('Review deleted', 'success');
            loadReviews(currentBusinessId, currentFilter);
        })
        .catch(() => {
            showToast('Failed to delete review', 'error');
        });
}

function showImportModal() {
    $('#import-modal').addClass('active');
}

function submitImport() {
    const businessId = $('#import-business-select').val() || currentBusinessId;
    const csvData = $('#import-csv').val();
    
    if (!businessId) {
        showToast('Please select a business', 'error');
        return;
    }
    
    if (!csvData.trim()) {
        showToast('Please paste CSV data', 'error');
        return;
    }
    
    // Parse CSV (simple parser)
    const lines = csvData.trim().split('\n');
    const reviews = [];
    
    // Skip header row if present
    const startIndex = lines[0].toLowerCase().includes('author') ? 1 : 0;
    
    for (let i = startIndex; i < lines.length; i++) {
        const cols = lines[i].split(',').map(c => c.trim().replace(/^"|"$/g, ''));
        if (cols.length >= 4) {
            reviews.push({
                external_id: cols[0] || `review-${i}`,
                source: cols[1]?.toLowerCase() || 'manual',
                author_name: cols[2] || 'Anonymous',
                rating: parseInt(cols[3]) || 3,
                text: cols[4] || '',
                review_date: cols[5] || new Date().toISOString().split('T')[0]
            });
        }
    }
    
    if (reviews.length === 0) {
        showToast('No valid reviews found in CSV', 'error');
        return;
    }
    
    $('#import-submit-btn').prop('disabled', true).html('<span class="loading"></span> Importing...');
    
    apiRequest('POST', '/reviews/import', { business_id: parseInt(businessId), reviews })
        .then(data => {
            showToast(data.message, 'success');
            closeModal();
            loadReviews(currentBusinessId, currentFilter);
        })
        .catch(err => {
            showToast('Import failed', 'error');
        })
        .finally(() => {
            $('#import-submit-btn').prop('disabled', false).html('Import Reviews');
        });
}

// ============================================
// Business Functions
// ============================================

function loadBusinesses() {
    apiRequest('GET', '/businesses')
        .then(data => {
            renderBusinessSelector(data.businesses);
            if (data.businesses.length > 0) {
                currentBusinessId = data.businesses[0].id;
                loadReviews(currentBusinessId, currentFilter);
            }
        });
}

function renderBusinessSelector(businesses) {
    let options = businesses.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    $('#business-select').html(options);
    $('#import-business-select').html(options);
    
    if (currentBusinessId) {
        $('#business-select').val(currentBusinessId);
    }
}

// ============================================
// Dashboard Stats
// ============================================

function loadDashboardStats() {
    Promise.all([
        apiRequest('GET', '/usage'),
        apiRequest('GET', '/reviews'),
        apiRequest('GET', '/businesses')
    ]).then(([usageData, reviewsData, businessesData]) => {
        const reviews = reviewsData.reviews || [];
        const total = reviews.length;
        const positive = reviews.filter(r => r.sentiment === 'positive').length;
        const negative = reviews.filter(r => r.sentiment === 'negative').length;
        const unresponded = reviews.filter(r => !r.is_responded).length;
        
        $('#stat-total').text(total);
        $('#stat-positive').text(positive);
        $('#stat-negative').text(negative);
        $('#stat-unresponded').text(unresponded);
        
        const usage = usageData.usage;
        if (!usage.is_pro) {
            $('#usage-bar-container').removeClass('hidden');
            const percentage = (usage.reviews_used / usage.reviews_limit) * 100;
            $('#usage-bar').css('width', `${percentage}%`);
            $('#usage-text').text(`${usage.reviews_used} / ${usage.reviews_limit} responses used`);
        } else {
            $('#usage-bar-container').addClass('hidden');
        }
    });
}

// ============================================
// Sidebar Navigation
// ============================================

function setupSidebar() {
    const currentPath = window.location.pathname;
    $('.nav-item').removeClass('active');
    $(`.nav-item[href="${currentPath}"]`).addClass('active');
    
    $(document).on('click', '.nav-item[data-href]', function() {
        const href = $(this).data('href');
        window.location.href = href;
    });
}

// ============================================
// Event Listeners
// ============================================

$(document).ready(function() {
    // Modal close buttons
    $(document).on('click', '.modal-close, .modal-overlay', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // Escape key closes modals
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
    
    // Business selector change
    $(document).on('change', '#business-select', function() {
        currentBusinessId = $(this).val();
        loadReviews(currentBusinessId, currentFilter);
    });
    
    // Filter buttons
    $(document).on('click', '.filter-btn', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        const filter = $(this).data('filter');
        loadReviews(currentBusinessId, filter);
    });
    
    // Generate response button
    $(document).on('click', '#generate-response-btn', submitGenerateResponse);
    
    // Copy response button
    $(document).on('click', '#copy-response-btn', copyResponse);
    
    // Regenerate button
    $(document).on('click', '#regenerate-response-btn', regenerateResponse);
    
    // Import submit
    $(document).on('click', '#import-submit-btn', submitImport);
    
    // Create business form
    $(document).on('submit', '#create-business-form', function(e) {
        e.preventDefault();
        const formData = {
            name: $('#business-name').val(),
            address: $('#business-address').val()
        };
        
        apiRequest('POST', '/businesses', formData)
            .then(() => {
                showToast('Business created!', 'success');
                closeModal();
                loadBusinesses();
            })
            .catch(() => {
                showToast('Failed to create business', 'error');
            });
    });
    
    // Logout button
    $(document).on('click', '#logout-btn', logout);
});

// Page-specific initialization
$(document).ready(function() {
    const path = window.location.pathname;
    
    if (path === '/dashboard') {
        if (!isLoggedIn()) { window.location.href = '/login'; return; }
        loadBusinesses();
        loadDashboardStats();
        setupSidebar();
    } else if (path === '/reviews') {
        if (!isLoggedIn()) { window.location.href = '/login'; return; }
        loadBusinesses();
        setupSidebar();
    } else if (path === '/login' || path === '/register') {
        if (isLoggedIn()) { window.location.href = '/dashboard'; }
    } else if (path === '/settings' || path === '/billing') {
        if (!isLoggedIn()) { window.location.href = '/login'; return; }
        setupSidebar();
    }
    
    // Auth forms
    if (path === '/login') {
        $(document).on('submit', '#login-form', function(e) {
            e.preventDefault();
            login($('#login-email').val(), $('#login-password').val());
        });
    }
    
    if (path === '/register') {
        $(document).on('submit', '#register-form', function(e) {
            e.preventDefault();
            register(
                $('#register-name').val(),
                $('#register-email').val(),
                $('#register-password').val(),
                $('#register-password_confirmation').val()
            );
        });
    }
});
