<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(User $user): View
    {
        $recentTopics = $user->topics()
            ->with('category')
            ->latest()
            ->limit(10)
            ->get();

        $recentPosts = $user->posts()
            ->with('topic')
            ->latest()
            ->limit(10)
            ->get();

        return view('profile.show', compact('user', 'recentTopics', 'recentPosts'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            
            // Handle profile settings
            $user->name = $validated['name'];
            $user->signature = $validated['signature'] ?? null;

            if ($request->hasFile('avatar')) {
                try {
                    $file = $request->file('avatar');
                    
                    // Debug information
                    Log::info('Starting avatar upload process', [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'error' => $file->getError(),
                        'storage_path' => storage_path('app/public/avatars'),
                        'is_writable' => is_writable(storage_path('app/public/avatars')),
                    ]);

                    // Check if storage directory exists and is writable
                    if (!file_exists(storage_path('app/public/avatars'))) {
                        mkdir(storage_path('app/public/avatars'), 0775, true);
                    }

                    // Validate file
                    if (!$file->isValid()) {
                        throw new \Exception('Invalid file upload: ' . $file->getErrorMessage());
                    }

                    // Delete old avatar if exists
                    if ($user->avatar) {
                        $oldPath = str_replace('storage/', '', $user->avatar);
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                    
                    // Generate unique filename
                    $filename = uniqid('avatar_') . '_' . time() . '.' . $file->getClientOriginalExtension();
                    
                    // Store the file
                    $path = $file->storeAs('avatars', $filename, 'public');
                    
                    if (!$path) {
                        throw new \Exception('Failed to store avatar file');
                    }

                    // Verify file was actually saved
                    if (!Storage::disk('public')->exists($path)) {
                        throw new \Exception('File was not saved to storage');
                    }

                    Log::info('Avatar uploaded successfully', [
                        'path' => $path,
                        'full_path' => Storage::disk('public')->path($path),
                        'url' => Storage::disk('public')->url($path)
                    ]);
                    
                    $validated['avatar'] = $path;
                    
                } catch (\Exception $e) {
                    Log::error('Avatar upload failed', [
                        'exception' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'user_id' => $user->id
                    ]);
                    return back()->withErrors(['avatar' => 'Failed to upload avatar: ' . $e->getMessage()]);
                }
            }

            // Handle avatar removal
            if ($request->has('remove_avatar') && $request->remove_avatar) {
                try {
                    if ($user->avatar) {
                        $path = str_replace('storage/', '', $user->avatar);
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                    $validated['avatar'] = null;
                } catch (\Exception $e) {
                    Log::error('Avatar deletion failed', [
                        'exception' => $e->getMessage(),
                        'user_id' => $user->id
                    ]);
                    return back()->withErrors(['avatar' => 'Failed to delete avatar']);
                }
            }

            $user->fill($validated);

            $user->save();

            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['general' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Проверяем, что это не единственный админ
        if ($user->isAdmin()) {
            $adminCount = User::where('is_admin', true)->count();
            if ($adminCount <= 1) {
                return back()->withErrors([
                    'userDeletion' => [
                        'password' => __('Невозможно удалить единственного администратора.')
                    ]
                ]);
            }
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
