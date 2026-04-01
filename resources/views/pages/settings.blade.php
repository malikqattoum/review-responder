@extends('layouts.app')
@section('title', 'Settings - Review Responder Pro')

@section('content')
<div class="app-layout">
    @include('layouts.partials.sidebar')
    
    <main class="main-content">
        <header class="header">
            <div class="header-left">
                <h1 class="page-title">Settings</h1>
            </div>
        </header>
        
        <div class="content">
            <!-- Notifications Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">🔔 Notification Preferences</h3>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 16px; padding: 12px 0;">
                    <input type="checkbox" id="notify-new-reviews" checked style="width: 20px; height: 20px;">
                    <div>
                        <label for="notify-new-reviews" style="font-weight: 600; cursor: pointer;">New Review Alerts</label>
                        <p class="form-hint" style="margin: 0;">Get notified when new reviews are imported</p>
                    </div>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 16px; padding: 12px 0; border-top: 1px solid var(--border);">
                    <input type="checkbox" id="notify-negative-reviews" checked style="width: 20px; height: 20px;">
                    <div>
                        <label for="notify-negative-reviews" style="font-weight: 600; cursor: pointer;">🚨 Negative Review Alerts</label>
                        <p class="form-hint" style="margin: 0;">Get urgent alerts for 1-2 star reviews</p>
                    </div>
                </div>
                <div class="form-group" style="border-top: 1px solid var(--border); padding-top: 16px;">
                    <label class="form-label">Notification Email</label>
                    <input type="email" id="notification-email" class="form-input" placeholder="notifications@example.com (optional)">
                    <p class="form-hint">Leave empty to use your account email</p>
                </div>
                <button class="btn btn-primary" onclick="saveNotificationSettings()">Save Notification Settings</button>
            </div>
            
            <!-- Account Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Account Settings</h3>
                </div>
                <form id="account-form">
                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <input type="text" id="settings-name" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" id="settings-email" class="form-input" disabled>
                        <p class="form-hint">Email cannot be changed</p>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Account Changes</button>
                </form>
            </div>
            
            <!-- Business Locations -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">🏢 Business Locations</h3>
                    <button class="btn btn-primary btn-sm" onclick="showCreateBusinessModal()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add Location
                    </button>
                </div>
                <div id="businesses-list">
                    <div class="loading" style="margin: 20px auto;"></div>
                </div>
            </div>
            
            <!-- API Configuration -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">⚙️ API Configuration</h3>
                </div>
                <div class="form-group">
                    <label class="form-label">OpenAI API Key</label>
                    <input type="password" id="openai-api-key" class="form-input" placeholder="sk-...">
                    <p class="form-hint">Required for AI response generation. Get your key from <a href="https://platform.openai.com" target="_blank">OpenAI</a></p>
                </div>
                <button class="btn btn-primary" onclick="saveApiKey()">Save API Key</button>
            </div>
        </div>
    </main>
</div>

<!-- Create Business Modal -->
<div id="create-business-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add Business Location</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="create-business-form">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Business Name</label>
                    <input type="text" id="business-name" class="form-input" placeholder="Downtown Restaurant" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" id="business-address" class="form-input" placeholder="123 Main St, City, State">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
    
    // Account form
    $('#account-form').on('submit', function(e) {
        e.preventDefault();
        saveAccountSettings();
    });
});

function loadSettings() {
    const user = getUser();
    if (user) {
        $('#settings-name').val(user.name);
        $('#settings-email').val(user.email);
        
        // Load notification settings from user object
        if (user.notify_new_reviews !== undefined) {
            $('#notify-new-reviews').prop('checked', user.notify_new_reviews);
        }
        if (user.notify_negative_reviews !== undefined) {
            $('#notify-negative-reviews').prop('checked', user.notify_negative_reviews);
        }
        if (user.notification_email) {
            $('#notification-email').val(user.notification_email);
        }
    }
    
    apiRequest('GET', '/businesses')
        .then(data => {
            renderBusinessesList(data.businesses);
        })
        .catch(() => {});
}

function saveNotificationSettings() {
    const notifyNew = $('#notify-new-reviews').is(':checked');
    const notifyNegative = $('#notify-negative-reviews').is(':checked');
    const notifyEmail = $('#notification-email').val().trim();
    
    apiRequest('PUT', '/user/notification-settings', {
        notify_new_reviews: notifyNew,
        notify_negative_reviews: notifyNegative,
        notification_email: notifyEmail || null
    })
    .then(data => {
        showToast('Notification settings saved!', 'success');
        // Update local user object
        const user = getUser();
        user.notify_new_reviews = notifyNew;
        user.notify_negative_reviews = notifyNegative;
        user.notification_email = notifyEmail || null;
        setUser(user);
    })
    .catch(err => {
        showToast('Failed to save settings', 'error');
    });
}

function saveAccountSettings() {
    const name = $('#settings-name').val().trim();
    
    if (!name) {
        showToast('Name is required', 'error');
        return;
    }
    
    apiRequest('PUT', '/user', { name })
        .then(data => {
            showToast('Account settings saved!', 'success');
            setUser(data.user);
        })
        .catch(err => {
            showToast('Failed to save settings', 'error');
        });
}

function saveApiKey() {
    const apiKey = $('#openai-api-key').val().trim();
    
    if (!apiKey) {
        showToast('API key is required', 'error');
        return;
    }
    
    // Save to localStorage for now (in production, this should be stored server-side)
    localStorage.setItem('openai_api_key', apiKey);
    showToast('API key saved!', 'success');
}

function renderBusinessesList(businesses) {
    if (businesses.length === 0) {
        $('#businesses-list').html(`
            <p class="text-muted text-center" style="padding: 20px;">
                No business locations yet. Add one to get started.
            </p>
        `);
        return;
    }
    
    let html = '<table class="table"><thead><tr><th>Name</th><th>Address</th><th>Reviews</th><th>Actions</th></tr></thead><tbody>';
    businesses.forEach(b => {
        html += `
            <tr>
                <td><strong>${b.name}</strong></td>
                <td>${b.address || '-'}</td>
                <td>-</td>
                <td>
                    <button class="btn btn-sm btn-ghost" onclick="editBusiness(${b.id})">Edit</button>
                    <button class="btn btn-sm btn-ghost text-danger" onclick="deleteBusiness(${b.id})">Delete</button>
                </td>
            </tr>
        `;
    });
    html += '</tbody></table>';
    $('#businesses-list').html(html);
}

function showCreateBusinessModal() {
    $('#create-business-modal').addClass('active');
}

function deleteBusiness(id) {
    if (!confirm('Delete this business location?')) return;
    apiRequest('DELETE', `/businesses/${id}`)
        .then(() => {
            showToast('Business deleted', 'success');
            loadSettings();
        })
        .catch(() => {
            showToast('Failed to delete', 'error');
        });
}
</script>
@endsection
