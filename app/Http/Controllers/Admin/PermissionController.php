<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::where('guard_name', 'admin')->get();

        return view('admin.permissions.index', [
            'permissions' => $permissions,
        ]);
    }

    public function create()
    {
        return view('admin.permissions.form', [
            'permission' => new Permission(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'admin',
        ]);

        ActivityLog::record('permission.created', $request->user('admin'), null, [
            'name' => $validated['name'],
        ]);

        return redirect()->route('admin.permissions.index')->with('status', 'Permission created.');
    }

    public function edit(Permission $permission)
    {
        if ($permission->guard_name !== 'admin') {
            abort(404);
        }

        return view('admin.permissions.form', [
            'permission' => $permission,
        ]);
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        if ($permission->guard_name !== 'admin') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $permission->update([
            'name' => $validated['name'],
        ]);

        ActivityLog::record('permission.updated', $request->user('admin'), $permission);

        return redirect()->route('admin.permissions.index')->with('status', 'Permission updated.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        if ($permission->guard_name !== 'admin') {
            abort(404);
        }

        ActivityLog::record('permission.deleted', request()->user('admin'), $permission);
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('status', 'Permission deleted.');
    }
}
