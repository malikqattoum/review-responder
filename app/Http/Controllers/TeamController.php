<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\TeamMember;
use App\Models\User;
use App\Notifications\ReviewRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * List team members for a business
     */
    public function index(Request $request, Business $business)
    {
        if (!Gate::allows('manageTeam', $business)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $members = $business->teamMembers()
            ->with('user:id,name,email')
            ->get();

        return response()->json([
            'members' => $members,
        ]);
    }

    /**
     * Invite a new team member
     */
    public function invite(Request $request, Business $business)
    {
        if (!Gate::allows('manageTeam', $business)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'role' => 'required|in:admin,manager,viewer',
        ]);

        // Check if already a member or invited
        $existing = TeamMember::where('business_id', $business->id)
            ->where(function ($q) use ($validated) {
                $q->where('invite_email', $validated['email'])
                  ->orWhereHas('user', function ($q) use ($validated) {
                      $q->where('email', $validated['email']);
                  });
            })
            ->first();

        if ($existing) {
            return response()->json([
                'error' => 'This person is already a team member or has a pending invitation.',
            ], 409);
        }

        // Create pending invitation
        $member = TeamMember::create([
            'business_id' => $business->id,
            'user_id' => null,
            'role' => $validated['role'],
            'invite_email' => $validated['email'],
            'invite_token' => Str::random(32),
            'invited_at' => now(),
        ]);

        // Check if user already exists
        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser) {
            // Auto-accept for existing users
            $member->accept($existingUser->id);
            return response()->json([
                'message' => "{$existingUser->name} has been added to the team!",
                'member' => $member->fresh()->load('user'),
            ]);
        }

        return response()->json([
            'message' => 'Invitation sent! They will be added when they create an account.',
            'member' => $member,
        ]);
    }

    /**
     * Update member role
     */
    public function updateRole(Request $request, Business $business, TeamMember $member)
    {
        if (!Gate::allows('manageTeam', $business)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,manager,viewer',
        ]);

        if ($member->business_id !== $business->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $member->update(['role' => $validated['role']]);

        return response()->json([
            'message' => 'Role updated',
            'member' => $member->fresh()->load('user'),
        ]);
    }

    /**
     * Remove team member
     */
    public function remove(Business $business, TeamMember $member)
    {
        if (!Gate::allows('manageTeam', $business)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($member->business_id !== $business->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        // Can't remove the business owner
        if ($member->user_id === $business->user_id) {
            return response()->json(['error' => 'Cannot remove the business owner'], 400);
        }

        $member->delete();

        return response()->json([
            'message' => 'Team member removed',
        ]);
    }

    /**
     * Send review request to customer
     */
    public function sendReviewRequest(Request $request, Business $business)
    {
        if (!Gate::allows('manageTeam', $business)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'review_link' => 'nullable|url|max:500',
            'source' => 'nullable|in:google,yelp,facebook,manual',
        ]);

        try {
            $business->user->notify(new ReviewRequestNotification(
                $business->name,
                $validated['customer_name'],
                $validated['review_link'] ?? '',
                $validated['source'] ?? 'google'
            ));

            return response()->json([
                'message' => 'Review request sent successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send email. Make sure your mail server is configured.',
            ], 500);
        }
    }

    /**
     * Get team permissions for a user
     */
    public function getPermissions(Request $request, Business $business)
    {
        $user = $request->user();

        // Business owner has all permissions
        if ($business->user_id === $user->id) {
            return response()->json([
                'role' => 'owner',
                'permissions' => [
                    'manage_business' => true,
                    'manage_reviews' => true,
                    'generate_responses' => true,
                    'manage_team' => true,
                    'billing' => true,
                ],
            ]);
        }

        // Check team membership
        $member = TeamMember::where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$member || !$member->isAccepted()) {
            return response()->json([
                'role' => null,
                'permissions' => [],
            ]);
        }

        return response()->json([
            'role' => $member->role,
            'permissions' => $this->getRolePermissions($member->role),
        ]);
    }

    protected function getRolePermissions(string $role): array
    {
        return match ($role) {
            'admin' => [
                'manage_business' => true,
                'manage_reviews' => true,
                'generate_responses' => true,
                'manage_team' => true,
                'billing' => false,
            ],
            'manager' => [
                'manage_business' => true,
                'manage_reviews' => true,
                'generate_responses' => true,
                'manage_team' => false,
                'billing' => false,
            ],
            'viewer' => [
                'manage_business' => false,
                'manage_reviews' => false,
                'generate_responses' => true,
                'manage_team' => false,
                'billing' => false,
            ],
            default => [],
        };
    }
}
