<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserEditRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users (Admin only).
     */
    public function index()
    {
        try {
            $users = User::where('role', '!=', 'admin')->get();

            return response()->json([
                'success' => true,
                'users' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper to prevent actions on admin users.
     * Returns a JSON response when target is admin, otherwise null.
     */
    private function denyIfAdmin(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Action not allowed on admin users',
            ], 403);
        }

        return null;
    }

    /**
     * Get single user details (Admin only).
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent actions on admin users
            $deny = $this->denyIfAdmin($user);
            if ($deny) {
                return $deny;
            }

            return response()->json([
                'success' => true,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
    }

    /**
     * Update user details (Admin only).
     */
    public function update(UserEditRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent actions on admin users
            $deny = $this->denyIfAdmin($user);
            if ($deny) {
                return $deny;
            }

            $user->update($request->only(['name', 'email']));

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Block user (Admin only).
     */
    public function block($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent actions on admin users
            $deny = $this->denyIfAdmin($user);
            if ($deny) {
                return $deny;
            }

            $user->update(['status' => false]);

            return response()->json([
                'success' => true,
                'message' => 'User blocked successfully',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to block user: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Unblock user (Admin only).
     */
    public function unblock($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent actions on admin users
            $deny = $this->denyIfAdmin($user);
            if ($deny) {
                return $deny;
            }

            $user->update(['status' => true]);

            return response()->json([
                'success' => true,
                'message' => 'User unblocked successfully',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unblock user: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete user (Admin only).
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent actions on admin users
            $deny = $this->denyIfAdmin($user);
            if ($deny) {
                return $deny;
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage(),
            ], 422);
        }
    }
}
