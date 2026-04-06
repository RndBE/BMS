<?php

namespace App\Http\Controllers;

use App\Mail\NewAccountMail;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'user');
        $search = $request->get('search', '');

        $users       = collect();
        $roles       = collect();
        $permissions = collect();
        $allRoles    = collect();
        $allPerms    = collect();

        if ($tab === 'user') {
            $users = User::with('roles')
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                                            ->orWhere('email', 'like', "%{$search}%"))
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString();

            $allRoles = Role::orderBy('name')->get(['id', 'name']);
        }

        if ($tab === 'role') {
            $roles = Role::withCount('permissions', 'users')
                ->with('permissions')
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString();

            $allPerms = Permission::orderBy('name')->get(['id', 'name']);
        }

        if ($tab === 'permission') {
            $permissions = Permission::withCount('roles')
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString();
        }

        return view('pengaturan.pengguna', compact(
            'tab', 'search', 'users', 'roles', 'permissions', 'allRoles', 'allPerms'
        ));
    }

    // ── User CRUD ─────────────────────────────────────────────────────────────

    public function userStore(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)],
            'roles'    => 'nullable|array',
            'roles.*'  => 'exists:roles,name',
        ]);

        // Simpan password mentah sebelum di-hash untuk dikirim via email
        $plainPassword = $data['password'];

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($plainPassword),
        ]);

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        // Kirim email notifikasi akun baru ke pengguna
        try {
            Mail::to($user->email)->queue(new NewAccountMail($user, $plainPassword));
        } catch (\Exception $e) {
            // Jika email gagal terkirim, akun tetap berhasil dibuat
            \Log::warning('Gagal mengirim email akun baru ke ' . $user->email . ': ' . $e->getMessage());
        }

        AuditLog::record('create', 'User', $user->id, "Menambah pengguna baru: {$user->name}", null, $user->toArray());

        return response()->json(['success' => true, 'user' => $user->load('roles')]);
    }

    public function userUpdate(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', Password::min(8)],
            'roles'    => 'nullable|array',
            'roles.*'  => 'exists:roles,name',
        ]);

        $oldData = $user->toArray();
        $oldData['roles'] = $user->roles->pluck('name')->toArray();

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            ...(!empty($data['password']) ? ['password' => Hash::make($data['password'])] : []),
        ]);

        $user->syncRoles($data['roles'] ?? []);

        $newData = $user->fresh()->toArray();
        $newData['roles'] = $user->roles->pluck('name')->toArray();

        AuditLog::record('update', 'User', $user->id, "Mengubah data pengguna: {$user->name}", $oldData, $newData);

        return response()->json(['success' => true, 'user' => $user->load('roles')]);
    }

    public function userDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri.'], 422);
        }
        
        $oldData = $user->toArray();
        $oldData['roles'] = $user->roles->pluck('name')->toArray();
        $userName = $user->name;
        
        $user->delete();
        
        AuditLog::record('delete', 'User', $oldData['id'], "Menghapus pengguna: {$userName}", $oldData, null);
        
        return response()->json(['success' => true]);
    }

    // ── Role CRUD ─────────────────────────────────────────────────────────────

    public function roleStore(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        $newData = $role->toArray();
        $newData['permissions'] = $role->permissions->pluck('name')->toArray();

        AuditLog::record('create', 'Role', $role->id, "Menambah peran baru: {$role->name}", null, $newData);

        return response()->json(['success' => true, 'role' => $role]);
    }

    public function roleUpdate(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $oldData = $role->toArray();
        $oldData['permissions'] = $role->permissions->pluck('name')->toArray();

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        $newData = $role->fresh()->toArray();
        $newData['permissions'] = $role->permissions->pluck('name')->toArray();

        AuditLog::record('update', 'Role', $role->id, "Mengubah peran: {$role->name}", $oldData, $newData);

        return response()->json(['success' => true, 'role' => $role]);
    }

    public function roleDestroy(Role $role)
    {
        $oldData = $role->toArray();
        $roleName = $role->name;
        
        $role->delete();

        AuditLog::record('delete', 'Role', $oldData['id'], "Menghapus peran: {$roleName}", $oldData, null);

        return response()->json(['success' => true]);
    }

    // ── Permission CRUD ───────────────────────────────────────────────────────

    public function permissionStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $name = strtolower(str_replace(' ', '_', trim($data['name'])));

        // Unique check after normalization
        if (\Spatie\Permission\Models\Permission::where('name', $name)->exists()) {
            return response()->json(['errors' => ['name' => ['Permission sudah ada.']]], 422);
        }

        $permission = Permission::create(['name' => $name, 'guard_name' => 'web']);
        
        AuditLog::record('create', 'Permission', $permission->id, "Menambah permission baru: {$permission->name}", null, $permission->toArray());
        
        return response()->json(['success' => true, 'permission' => $permission]);
    }

    public function permissionUpdate(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $name = strtolower(str_replace(' ', '_', trim($data['name'])));

        // Unique check after normalization (excluding self)
        if (\Spatie\Permission\Models\Permission::where('name', $name)->where('id', '!=', $permission->id)->exists()) {
            return response()->json(['errors' => ['name' => ['Permission sudah ada.']]], 422);
        }

        $oldData = $permission->toArray();
        $permission->update(['name' => $name]);

        AuditLog::record('update', 'Permission', $permission->id, "Mengubah permission: {$permission->name}", $oldData, $permission->fresh()->toArray());

        return response()->json(['success' => true, 'permission' => $permission->fresh()]);
    }

    public function permissionDestroy(Permission $permission)
    {
        $oldData = $permission->toArray();
        $permName = $permission->name;

        $permission->delete();

        AuditLog::record('delete', 'Permission', $oldData['id'], "Menghapus permission: {$permName}", $oldData, null);

        return response()->json(['success' => true]);
    }
}
