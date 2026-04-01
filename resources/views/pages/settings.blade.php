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
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Business Locations</h3>
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
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">API Configuration</h3>
                </div>
                <div class="form-group">
                    <label class="form-label">OpenAI API Key</label>
                    <input type="password" id="openai-api-key" class="form-input" placeholder="sk-...">
                    <p class="form-hint">Required for AI response generation. Get your key from <a href="https://platform.openai.com" target="_blank">OpenAI</a></p>
                </div>
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
        showToast('Settings saved (demo mode)', 'success');
    });
    
    // OpenAI key form
    $('#openai-api-key').on('change', function() {
        showToast('API key saved (demo mode)', 'success');
    });
});

function loadSettings() {
    const user = getUser();
    if (user) {
        $('#settings-name').val(user.name);
        $('#settings-email').val(user.email);
    }
    
    apiRequest('GET', '/businesses')
        .then(data => {
            renderBusinessesList(data.businesses);
        });
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
