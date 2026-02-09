<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class AdminAccountController extends Controller
{
    public function index()
    {
        $admins = Admin::with('roles')->orderBy('name')->paginate(20);

        return view('admin.admins.index', [
            'admins' => $admins,
        ]);
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->get();

        return view('admin.admins.form', [
            'admin' => new Admin(),
            'roles' => $roles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:admins,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['array'],
            'roles.*' => ['string'],
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $admin->syncRoles($validated['roles'] ?? []);

        ActivityLog::record('admin.created', $request->user('admin'), $admin, [
            'roles' => $validated['roles'] ?? [],
        ]);

        return redirect()->route('admin.admins.index')->with('status', 'Admin created.');
    }

    public function edit(Admin $admin)
    {
        $roles = Role::where('guard_name', 'admin')->get();

        return view('admin.admins.form', [
            'admin' => $admin,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:admins,email,'.$admin->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => ['array'],
            'roles.*' => ['string'],
        ]);

        $admin->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();
        $admin->syncRoles($validated['roles'] ?? []);

        ActivityLog::record('admin.updated', $request->user('admin'), $admin, [
            'roles' => $validated['roles'] ?? [],
        ]);

        return redirect()->route('admin.admins.index')->with('status', 'Admin updated.');
    }

    public function destroy(Request $request, Admin $admin): RedirectResponse
    {
        if ($request->user('admin')->id === $admin->id) {
            return back()->withErrors(['admin' => 'You cannot delete your own account.']);
        }

        ActivityLog::record('admin.deleted', $request->user('admin'), $admin);
        $admin->delete();

        return redirect()->route('admin.admins.index')->with('status', 'Admin deleted.');
    }
}
