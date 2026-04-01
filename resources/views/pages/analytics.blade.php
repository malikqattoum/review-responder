@extends('layouts.app')
@section('title', 'Analytics - Review Responder Pro')

@section('content')
<div class="app-layout">
    @include('layouts.partials.sidebar')
    
    <main class="main-content">
        <header class="header">
            <div class="header-left">
                <h1 class="page-title">📊 Analytics</h1>
            </div>
            <div class="header-right">
                <div class="business-selector">
                    <select id="analytics-business-select">
                        <option value="">All Businesses</option>
                    </select>
                </div>
                <select id="date-range" class="form-select" style="width: auto;">
                    <option value="7">Last 7 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                <button class="btn btn-primary" onclick="loadAnalytics()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <polyline points="1 20 1 14 7 14"></polyline>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </header>
        
        <div class="content">
            <!-- Summary Cards -->
            <div class="stats-grid" style="margin-bottom: 32px;">
                <div class="stat-card">
                    <div class="stat-label">Total Reviews</div>
                    <div class="stat-value" id="stat-total">-</div>
                    <div class="stat-change" id="stat-total-change"></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Average Rating</div>
                    <div class="stat-value" id="stat-avg-rating">-</div>
                    <div class="stat-change" id="stat-rating-change"></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Response Rate</div>
                    <div class="stat-value" id="stat-response-rate">-</div>
                    <div class="stat-change" id="stat-response-change"></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Sentiment Score</div>
                    <div class="stat-value" id="stat-sentiment">-</div>
                    <div class="stat-change" id="stat-sentiment-change"></div>
                </div>
            </div>
            
            <!-- Insights -->
            <div id="insights-container" class="card mb-4" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">💡 Insights</h3>
                </div>
                <div id="insights-list"></div>
            </div>
            
            <!-- Charts Row -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
                <!-- Sentiment Over Time -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sentiment Over Time</h3>
                    </div>
                    <div id="sentiment-chart" style="height: 250px; display: flex; align-items: flex-end; gap: 8px; padding: 20px 0;">
                        <!-- Chart will be rendered here -->
                    </div>
                </div>
                
                <!-- Rating Distribution -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Rating Distribution</h3>
                    </div>
                    <div id="rating-chart" style="padding: 20px 0;">
                        <!-- Chart will be rendered here -->
                    </div>
                </div>
            </div>
            
            <!-- Metrics Row -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">🏆 Sentiment Breakdown</h3>
                    </div>
                    <div style="padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--sentiment-positive-bg); display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--sentiment-positive); font-size: 1.5rem; font-weight: 700;" id="positive-count">-</span>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--sentiment-positive);">Positive</div>
                                <div style="font-size: 0.875rem; color: var(--text-muted);" id="positive-percent">0%</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--sentiment-neutral-bg); display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--sentiment-neutral); font-size: 1.5rem; font-weight: 700;" id="neutral-count">-</span>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--sentiment-neutral);">Neutral</div>
                                <div style="font-size: 0.875rem; color: var(--text-muted);" id="neutral-percent">0%</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--sentiment-negative-bg); display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--sentiment-negative); font-size: 1.5rem; font-weight: 700;" id="negative-count">-</span>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--sentiment-negative);">Negative</div>
                                <div style="font-size: 0.875rem; color: var(--text-muted);" id="negative-percent">0%</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">⏱️ Response Speed</h3>
                    </div>
                    <div style="padding: 20px;">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <div style="font-size: 3rem; font-weight: 700; color: var(--primary);" id="avg-response-hours">-</div>
                            <div style="color: var(--text-secondary);">hours average</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--text-secondary);">Within 1 hour</span>
                            <span style="font-weight: 600;" id="within-1-hour">-</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--text-secondary);">Within 24 hours</span>
                            <span style="font-weight: 600;" id="within-24-hours">-</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                            <span style="color: var(--text-secondary);">Within 1 week</span>
                            <span style="font-weight: 600;" id="within-week">-</span>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📝 Response Status</h3>
                    </div>
                    <div style="padding: 20px;">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <div style="font-size: 3rem; font-weight: 700; color: var(--secondary);" id="responded-count">-</div>
                            <div style="color: var(--text-secondary);">responded</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--text-secondary);">Responded</span>
                            <span style="font-weight: 600; color: var(--secondary);" id="responded-actual">-</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                            <span style="color: var(--text-secondary);">Pending</span>
                            <span style="font-weight: 600; color: var(--accent);" id="unresponded-actual">-</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Comparison -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📅 Month-over-Month Comparison</h3>
                </div>
                <div id="monthly-comparison" style="padding: 20px;">
                    <!-- Will be populated by JS -->
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
let analyticsData = null;

document.addEventListener('DOMContentLoaded', function() {
    loadBusinessSelector();
    loadAnalytics();
    
    // Change handlers
    $('#analytics-business-select, #date-range').on('change', loadAnalytics);
});

function loadBusinessSelector() {
    apiRequest('GET', '/businesses')
        .then(data => {
            const currentId = $('#analytics-business-select').val();
            let options = '<option value="">All Businesses</option>';
            data.businesses.forEach(b => {
                options += `<option value="${b.id}">${b.name}</option>`;
            });
            $('#analytics-business-select').html(options);
        })
        .catch(() => {});
}

function loadAnalytics() {
    const businessId = $('#analytics-business-select').val();
    const days = $('#date-range').val();
    
    const endDate = new Date().toISOString().split('T')[0];
    const startDate = new Date(Date.now() - days * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    
    let url = `/analytics?start_date=${startDate}&end_date=${endDate}`;
    if (businessId) {
        url += `&business_id=${businessId}`;
    }
    
    apiRequest('GET', url)
        .then(data => {
            analyticsData = data;
            renderAnalytics(data);
        })
        .catch(err => {
            showToast('Failed to load analytics', 'error');
        });
}

function renderAnalytics(data) {
    const summary = data.summary;
    const monthly = data.monthly_comparison;
    
    // Summary cards
    $('#stat-total').text(summary.total_reviews);
    $('#stat-avg-rating').text(summary.average_rating + ' ⭐');
    $('#stat-response-rate').text(summary.response_rate + '%');
    
    // Sentiment score (positive %)
    const sentimentScore = summary.total_reviews > 0 
        ? Math.round((summary.positive_reviews / summary.total_reviews) * 100) + '%'
        : 'N/A';
    $('#stat-sentiment').text(sentimentScore);
    
    // Monthly comparison changes
    if (monthly.current && monthly.previous) {
        const totalChange = monthly.current.total - monthly.previous.total;
        const ratingChange = (monthly.current.avg_rating - monthly.previous.avg_rating).toFixed(2);
        const responseChange = (monthly.current.response_rate - monthly.previous.response_rate).toFixed(1);
        
        $('#stat-total-change').html(formatChange(totalChange, 'reviews'));
        $('#stat-rating-change').html(formatChange(ratingChange, 'rating'));
        $('#stat-response-change').html(formatChange(responseChange, '%'));
    }
    
    // Sentiment breakdown
    const total = summary.total_reviews || 1;
    $('#positive-count').text(summary.positive_reviews);
    $('#positive-percent').text(Math.round(summary.positive_reviews / total * 100) + '%');
    $('#neutral-count').text(summary.neutral_reviews);
    $('#neutral-percent').text(Math.round(summary.neutral_reviews / total * 100) + '%');
    $('#negative-count').text(summary.negative_reviews);
    $('#negative-percent').text(Math.round(summary.negative_reviews / total * 100) + '%');
    
    // Response metrics
    const metrics = data.response_metrics;
    $('#avg-response-hours').text(metrics.average_hours || 'N/A');
    $('#within-1-hour').text(metrics.within_1_hour || 0);
    $('#within-24-hours').text(metrics.within_24_hours || 0);
    $('#within-week').text(metrics.within_week || 0);
    
    // Response status
    $('#responded-count').text(summary.responded_reviews);
    $('#responded-actual').text(summary.responded_reviews + ' (' + summary.response_rate + '%)');
    $('#unresponded-actual').text(summary.unresponded_reviews);
    
    // Render charts
    renderSentimentChart(data.sentiment_by_week);
    renderRatingChart(data.rating_distribution);
    renderMonthlyComparison(monthly);
    renderInsights(data.insights);
}

function renderSentimentChart(data) {
    const container = $('#sentiment-chart');
    if (!data || data.length === 0) {
        container.html('<p style="text-align: center; color: var(--text-muted); width: 100%;">No data available</p>');
        return;
    }
    
    let html = '';
    const maxTotal = Math.max(...data.map(d => d.total), 1);
    
    data.forEach(week => {
        const height = (week.total / maxTotal) * 200;
        const positiveHeight = (week.positive / maxTotal) * 200;
        const neutralHeight = (week.neutral / maxTotal) * 200;
        const negativeHeight = (week.negative / maxTotal) * 200;
        
        html += `
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; gap: 4px;">
                <div style="display: flex; gap: 2px; align-items: flex-end;">
                    <div style="width: 16px; background: var(--sentiment-positive); border-radius: 2px 2px 0 0;" height="${positiveHeight}px"></div>
                    <div style="width: 16px; background: var(--sentiment-neutral); border-radius: 2px 2px 0 0;" height="${neutralHeight}px"></div>
                    <div style="width: 16px; background: var(--sentiment-negative); border-radius: 2px 2px 0 0;" height="${negativeHeight}px"></div>
                </div>
                <div style="font-size: 0.625rem; color: var(--text-muted); writing-mode: vertical-rl; transform: rotate(180deg);">${week.week}</div>
            </div>
        `;
    });
    
    container.html(html);
}

function renderRatingChart(distribution) {
    const container = $('#rating-chart');
    if (!distribution) {
        container.html('<p style="text-align: center; color: var(--text-muted);">No data available</p>');
        return;
    }
    
    const total = Object.values(distribution).reduce((a, b) => a + b, 0);
    const maxCount = Math.max(...Object.values(distribution), 1);
    
    let html = '';
    [5, 4, 3, 2, 1].forEach(rating => {
        const count = distribution[rating] || 0;
        const width = (count / maxCount) * 100;
        const percent = total > 0 ? Math.round((count / total) * 100) : 0;
        
        html += `
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 24px; text-align: right; font-weight: 600;">${rating} ⭐</div>
                <div style="flex: 1; background: var(--border-light); border-radius: 4px; height: 24px; overflow: hidden;">
                    <div style="width: ${width}%; height: 100%; background: #FBBF24; border-radius: 4px;"></div>
                </div>
                <div style="width: 60px; font-size: 0.875rem; color: var(--text-secondary);">${count} (${percent}%)</div>
            </div>
        `;
    });
    
    container.html(html);
}

function renderMonthlyComparison(data) {
    const container = $('#monthly-comparison');
    
    if (!data.current) {
        container.html('<p style="text-align: center; color: var(--text-muted);">No data available</p>');
        return;
    }
    
    const current = data.current;
    const previous = data.previous;
    
    let html = '<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; text-align: center;">';
    
    // Current month
    html += `
        <div>
            <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 8px;">${current.month}</div>
            <div style="font-size: 2rem; font-weight: 700;">${current.total}</div>
            <div style="font-size: 0.8125rem; color: var(--text-secondary);">Total Reviews</div>
        </div>
    `;
    
    // Previous month
    if (previous) {
        const change = current.total - previous.total;
        html += `
            <div>
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 8px;">${previous.month}</div>
                <div style="font-size: 2rem; font-weight: 700;">${previous.total}</div>
                <div style="font-size: 0.8125rem; color: var(--text-secondary);">Total Reviews</div>
                <div style="font-size: 0.75rem; margin-top: 4px; ${change >= 0 ? 'color: var(--secondary)' : 'color: var(--danger)'}">
                    ${change >= 0 ? '+' : ''}${change} vs prev
                </div>
            </div>
        `;
    }
    
    // Avg Rating
    html += `
        <div>
            <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 8px;">This Month</div>
            <div style="font-size: 2rem; font-weight: 700;">${current.avg_rating || 0} ⭐</div>
            <div style="font-size: 0.8125rem; color: var(--text-secondary);">Avg Rating</div>
        </div>
    `;
    
    // Response Rate
    html += `
        <div>
            <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 8px;">This Month</div>
            <div style="font-size: 2rem; font-weight: 700;">${current.response_rate}%</div>
            <div style="font-size: 0.8125rem; color: var(--text-secondary);">Response Rate</div>
        </div>
    `;
    
    html += '</div>';
    
    container.html(html);
}

function renderInsights(insights) {
    const container = $('#insights-container');
    const list = $('#insights-list');
    
    if (!insights || insights.length === 0) {
        container.hide();
        return;
    }
    
    container.show();
    
    let html = '<div style="display: grid; gap: 12px;">';
    insights.forEach(insight => {
        const color = insight.type === 'success' ? 'var(--secondary)' : 
                      insight.type === 'warning' ? 'var(--accent)' : 
                      'var(--danger)';
        const bg = insight.type === 'success' ? 'rgba(16, 185, 129, 0.1)' : 
                   insight.type === 'warning' ? 'rgba(245, 158, 11, 0.1)' : 
                   'rgba(239, 68, 68, 0.1)';
        
        html += `
            <div style="padding: 16px; background: ${bg}; border-radius: 8px; border-left: 4px solid ${color};">
                <div style="font-weight: 600; color: ${color}; margin-bottom: 4px;">${insight.message}</div>
                ${insight.action ? `<div style="font-size: 0.875rem; color: var(--text-secondary);">${insight.action}</div>` : ''}
            </div>
        `;
    });
    html += '</div>';
    
    list.html(html);
}

function formatChange(value, label) {
    const num = parseFloat(value);
    if (isNaN(num)) return '';
    const sign = num >= 0 ? '+' : '';
    const color = num >= 0 ? 'var(--secondary)' : 'var(--danger)';
    return `<span style="color: ${color}; font-size: 0.875rem;">${sign}${value} ${label}</span>`;
}
</script>
@endsection
