<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of all users.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = User::latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:user,admin'],
            'subdomain_limit' => ['nullable', 'integer', 'min:3'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'subdomain_limit' => $request->subdomain_limit,
            'email_verified_at' => now(),
        ]);

        // Log activity
        $limitText = $request->subdomain_limit ? "limit: {$request->subdomain_limit}" : "unlimited";
        activity_log(auth()->id(), 'user_created', "Created user: {$user->email} (role: {$user->role}, $limitText)", request()->ip());

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:user,admin'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'subdomain_limit' => ['nullable', 'integer', 'min:3'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'email', 'role', 'subdomain_limit']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $oldRole = $user->role;
        $oldLimit = $user->subdomain_limit;
        $user->update($data);

        // Log activity
        $changes = [];
        if ($oldRole !== $user->role) {
            $changes[] = "role: $oldRole → {$user->role}";
        }
        if ($oldLimit !== $user->subdomain_limit) {
            $oldLimitText = $oldLimit ? $oldLimit : 'unlimited';
            $newLimitText = $user->subdomain_limit ? $user->subdomain_limit : 'unlimited';
            $changes[] = "subdomain_limit: $oldLimitText → $newLimitText";
        }
        
        if (!empty($changes)) {
            activity_log(auth()->id(), 'user_updated', "Updated user: {$user->email} (" . implode(', ', $changes) . ")", request()->ip());
        } else {
            activity_log(auth()->id(), 'user_updated', "Updated user: {$user->email}", request()->ip());
        }

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Delete the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $email = $user->email;
        $user->delete();

        // Log activity
        activity_log(auth()->id(), 'user_deleted', "Deleted user: {$email}", request()->ip());

        return back()->with('success', "User '{$email}' deleted successfully.");
    }

    /**
     * Toggle user role between user and admin.
     */
    public function toggleRole(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot change your own role.');
        }

        $oldRole = $user->role;
        $newRole = $user->role === 'admin' ? 'user' : 'admin';
        $user->update(['role' => $newRole]);

        // Log activity
        activity_log(auth()->id(), 'user_role_changed', "Updated user role: {$user->email} ($oldRole → $newRole)", request()->ip());

        return back()->with('success', "User role updated: {$oldRole} → {$newRole}");
    }
}
