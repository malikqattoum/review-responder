# Review Responder Pro - Specification

## 1. Concept & Vision

**Review Responder Pro** is an AI-powered SaaS that helps local businesses respond to Google and Yelp reviews in seconds — not minutes. Instead of staring at a blank response box, businesses get AI-generated, personality-matched replies that sound human and professional.

**Core Value Prop:** "Stop losing customers to bad reviews. Respond in 10 seconds, not 10 minutes."

**Personality:** Confident, helpful, slightly witty. Not corporate — speaks like a smart friend who happens to be great at reputation management.

---

## 2. Design Language

### Color Palette
- **Primary:** `#4F46E5` (Indigo) — trust, professionalism
- **Secondary:** `#10B981` (Emerald) — success, positive reviews
- **Accent:** `#F59E0B` (Amber) — attention, warnings, negative reviews
- **Danger:** `#EF4444` (Red) — critical negative reviews
- **Background:** `#F9FAFB` (Light gray)
- **Surface:** `#FFFFFF` (White cards)
- **Text Primary:** `#111827`
- **Text Secondary:** `#6B7280`

### Typography
- **Headings:** Inter (700, 600)
- **Body:** Inter (400, 500)
- **Monospace:** JetBrains Mono (for review text snippets)

### Spacing System
- Base unit: 4px
- Card padding: 24px
- Section gaps: 32px
- Button padding: 12px 24px

---

## 3. Layout & Structure

### Pages

1. **Landing Page (`/`)** — Marketing page with hero, features, pricing, CTA
2. **Dashboard (`/dashboard`)** — Main hub after login
3. **Reviews (`/reviews`)** — List of all connected reviews
4. **Settings (`/settings`)** — Business profile, AI settings, integrations
5. **Billing (`/billing`)** — Subscription management (Stripe)
6. **Login (`/login`)** — Auth
7. **Register (`/register`)** — Onboarding

### Dashboard Layout
- **Sidebar:** Navigation (Dashboard, Reviews, Settings, Billing)
- **Header:** Business selector dropdown, notifications, profile
- **Main:** Context-dependent content

---

## 4. Features & Interactions

### Core Features

#### 4.1 Review Import
- Manual CSV import (Google/Yelp export format)
- Google Places API integration (future)
- Yelp Fusion API integration (future)
- Fields: review_text, rating (1-5), source (google/yelp), date, author_name, business_location

#### 4.2 AI Response Generation
- Click "Generate Response" on any review
- AI analyzes sentiment (positive/negative/neutral/mixed)
- Generates response that:
  - Matches the tone (professional, friendly, apologetic based on sentiment)
  - References specific points from the review
  - Includes appropriate closing
- Edit response before copying/publishing
- One-click copy to clipboard
- Response history stored

#### 4.3 Sentiment Analysis
- Auto-detect: Positive (4-5★), Neutral (3★), Negative (1-2★)
- Color-coded badges: 🟢 Positive, 🟡 Neutral, 🔴 Negative
- Filter reviews by sentiment

#### 4.4 Multi-Location Support
- Add multiple business locations
- Switch between locations in header
- Each location has its own review feed

#### 4.5 Response Templates (AI fallback)
- Pre-written templates for common scenarios
- Positive review response template
- Negative review acknowledgment template
- Neutral review engagement template

#### 4.6 Subscription & Billing
- Stripe integration
- Free tier: 10 reviews/month, 1 location
- Pro tier ($29/location/month): Unlimited reviews, unlimited locations
- Usage dashboard showing reviews used / remaining

### Interactions

| Action | Behavior |
|--------|----------|
| Click "Generate" | Show loading spinner → AI response appears in textarea |
| Hover review card | Subtle shadow lift, show action buttons |
| Click copy | Green checkmark flash, "Copied!" tooltip |
| Negative review | Red left border accent, urgent styling |
| Empty state | Illustration + "Import your first review" CTA |
| API error | Toast notification with retry option |

### Edge Cases
- **Empty review text:** Show "Review text not available" placeholder
- **API timeout:** "AI is taking longer than expected. Try again or use a template."
- **Rate limit:** "You've reached your monthly limit. Upgrade to Pro for unlimited."
- **No reviews:** Friendly empty state with import instructions

---

## 5. Component Inventory

### ReviewCard
- Author name, date, star rating, source badge (Google/Yelp)
- Review text (truncated at 200 chars, expandable)
- Sentiment badge
- Action buttons: Generate Response, Copy Last, View History
- States: default, hovered, loading, error

### AIResponseModal
- Original review display
- Generated response textarea (editable)
- Tone selector: Professional, Friendly, Apologetic
- Copy button, Regenerate button, Use Template button
- States: loading, ready, editing, error

### Sidebar Navigation
- Logo at top
- Nav items with icons
- Active state: indigo background, bold text
- User profile at bottom

### BusinessSelector (Header)
- Dropdown with location names
- "Add Location" option at bottom
- Current location highlighted

### PricingCard
- Plan name, price, billing period
- Feature list with checkmarks
- CTA button
- Popular badge on Pro tier

### Toast Notifications
- Success: green left border
- Error: red left border
- Info: blue left border
- Auto-dismiss after 5s

---

## 6. Technical Approach

### Stack
- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** jQuery + custom CSS (no heavy framework)
- **Database:** MySQL (existing server)
- **AI:** OpenRouter API (supports OpenAI GPT-4o-mini, Claude, Gemini, MiniMax, Llama, Mistral)
- **Auth:** Laravel Sanctum (token-based)
- **Billing:** Stripe (subscriptions)

### Database Schema

```
users
├── id
├── name
├── email
├── password (hashed)
├── stripe_customer_id
├── subscription_status (active/canceled/trialing)
├── subscription_tier (free/pro)
├── created_at
└── updated_at

businesses
├── id
├── user_id (foreign key)
├── name
├── address
├── location_slug (unique identifier)
├── google_place_id (nullable)
├── yelp_business_id (nullable)
├── created_at
└── updated_at

reviews
├── id
├── business_id (foreign key)
├── external_id (Google/Yelp review ID)
├── source (google/yelp)
├── author_name
├── rating (1-5)
├── text
├── sentiment (positive/neutral/negative)
├── review_date
├── is_responded (boolean)
├── created_at
└── updated_at

responses
├── id
├── review_id (foreign key)
├── body (generated response text)
├── tone (professional/friendly/apologetic)
├── created_at
└── updated_at

subscription_usage
├── id
├── user_id (foreign key)
├── month (YYYY-MM)
├── reviews_used
├── reviews_limit
└── updated_at
```

### API Endpoints

```
Auth:
POST   /api/register          - Create account
POST   /api/login             - Login, returns token
POST   /api/logout            - Revoke token

Businesses:
GET    /api/businesses        - List user's businesses
POST   /api/businesses        - Create business
GET    /api/businesses/{id}   - Get business details
PUT    /api/businesses/{id}   - Update business
DELETE /api/businesses/{id}   - Delete business

Reviews:
GET    /api/reviews           - List reviews (filter by business_id, sentiment)
POST   /api/reviews/import   - Import reviews from CSV
GET    /api/reviews/{id}     - Get single review
PUT    /api/reviews/{id}     - Update review (mark as responded)

AI Response:
POST   /api/reviews/{id}/generate-response  - Generate AI response
POST   /api/responses/{id}/regenerate       - Regenerate with different tone

Response History:
GET    /api/reviews/{id}/responses          - Get all responses for a review

Subscription:
GET    /api/subscription                     - Get current subscription status
POST   /api/subscription/checkout           - Create Stripe checkout session
POST   /api/webhooks/stripe                  - Stripe webhook handler

Usage:
GET    /api/usage                            - Get current month's usage stats
```

### File Structure

```
review-responder/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── BusinessController.php
│   │   │   ├── ReviewController.php
│   │   │   ├── ResponseController.php
│   │   │   ├── SubscriptionController.php
│   │   │   └── UsageController.php
│   │   ├── Middleware/
│   │   │   └── TokenAuth.php
│   └── Models/
│       ├── User.php
│       ├── Business.php
│       ├── Review.php
│       ├── Response.php
│       └── SubscriptionUsage.php
├── config/
│   └── stripe.php
├── database/
│   └── migrations/
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── pages/
│       │   ├── landing.blade.php
│       │   ├── dashboard.blade.php
│       │   ├── reviews.blade.php
│       │   ├── settings.blade.php
│       │   └── billing.blade.php
│       └── auth/
│           ├── login.blade.php
│           └── register.blade.php
├── routes/
│   └── web.php
└── public/
    ├── css/
    │   └── app.css
    ├── js/
    │   └── app.js
    └── images/
```

### AI Prompt Strategy

**System Prompt:**
```
You are a professional reputation management assistant. Generate a response to the following review that:
1. Acknowledges the customer's feedback specifically
2. Thanks them for positive feedback OR apologizes sincerely for negative experiences
3. Invites them to return or contact you offline for resolution
4. Sounds natural, friendly, and professional (NOT robotic)
5. Is appropriate for a [POSITIVE/NEGATIVE/NEUTRAL] review

Review: [review_text]
Rating: [rating] stars
Author: [author_name]

Response (2-4 sentences max):
```

### Security Considerations
- Rate limiting: 30 requests/minute for AI generation
- Input sanitization on all user inputs
- CSRF protection on all forms
- Stripe webhook signature verification
- Token revocation on logout

---

## 7. Pricing Tiers

### Free Tier
- **Price:** $0/month
- **Reviews:** 10/month
- **Locations:** 1
- **AI Responses:** Limited to free quota
- **Export:** No

### Pro Tier
- **Price:** $29/location/month
- **Reviews:** Unlimited
- **Locations:** Unlimited
- **AI Responses:** Unlimited
- **Export:** CSV/PDF
- **Priority Support:** Yes

---

## 8. MVP Scope (Phase 1)

For initial build, implement:

1. ✅ User auth (register/login/logout)
2. ✅ Dashboard with sidebar
3. ✅ Business CRUD (single location for MVP)
4. ✅ Review import (CSV only)
5. ✅ Review list with sentiment badges
6. ✅ AI response generation (OpenRouter / MiniMax)
7. ✅ Response copy/edit/regenerate
8. ✅ Response history
9. ✅ Free tier with 10 review limit
10. ✅ Landing page with pricing

**Deferred to Phase 2:**
- Stripe billing integration
- Multi-location management
- Google/Yelp API integration
- Email notifications
