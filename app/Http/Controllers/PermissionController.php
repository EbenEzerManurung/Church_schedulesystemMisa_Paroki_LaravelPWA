<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $menus = Menu::whereNull('parent_id')->with('children')->orderBy('order')->get();
        
        $permissions = [];
        foreach ($roles as $role) {
            foreach ($menus as $menu) {
                $permissions[$role->id][$menu->id] = [
                    'can_view' => $menu->hasAccess($role, 'view'),
                    'can_create' => $menu->hasAccess($role, 'create'),
                    'can_edit' => $menu->hasAccess($role, 'edit'),
                    'can_delete' => $menu->hasAccess($role, 'delete'),
                ];
                
                // Untuk child menu
                foreach ($menu->children as $child) {
                    $permissions[$role->id][$child->id] = [
                        'can_view' => $child->hasAccess($role, 'view'),
                        'can_create' => $child->hasAccess($role, 'create'),
                        'can_edit' => $child->hasAccess($role, 'edit'),
                        'can_delete' => $child->hasAccess($role, 'delete'),
                    ];
                }
            }
        }
        
        return view('permissions.index', compact('roles', 'menus', 'permissions'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'permissions' => 'required|array',
            'role_id' => 'required|exists:roles,id'
        ]);
        
        $role = Role::findById($request->role_id);
        
        foreach ($request->permissions as $menuId => $perms) {
            $menu = Menu::find($menuId);
            if ($menu) {
                $menu->roles()->syncWithoutDetaching([
                    $role->id => [
                        'can_view' => isset($perms['can_view']),
                        'can_create' => isset($perms['can_create']),
                        'can_edit' => isset($perms['can_edit']),
                        'can_delete' => isset($perms['can_delete']),
                        'updated_at' => now()
                    ]
                ]);
            }
        }
        
        return redirect()->route('permissions.index')
            ->with('success', 'Hak akses berhasil diupdate untuk role ' . ucfirst($role->name));
    }
    
    public function resetToDefault(Request $request)
    {
        $menus = Menu::all();
        $adminRole = Role::where('name', 'admin')->first();
        $pastorRole = Role::where('name', 'pastor')->first();
        $userRole = Role::where('name', 'user')->first();
        
        // Reset semua permissions untuk admin (full access)
        foreach ($menus as $menu) {
            $menu->roles()->syncWithoutDetaching([
                $adminRole->id => [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                    'updated_at' => now()
                ]
            ]);
        }
        
        // Reset untuk pastor (semua kecuali users & permissions)
        foreach ($menus as $menu) {
            if (!in_array($menu->url, ['/users', '/permissions'])) {
                $menu->roles()->syncWithoutDetaching([
                    $pastorRole->id => [
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'updated_at' => now()
                    ]
                ]);
            } else {
                $menu->roles()->syncWithoutDetaching([
                    $pastorRole->id => [
                        'can_view' => false,
                        'can_create' => false,
                        'can_edit' => false,
                        'can_delete' => false,
                        'updated_at' => now()
                    ]
                ]);
            }
        }
        
        // Reset untuk user (hanya view schedule & availability)
        foreach ($menus as $menu) {
            if ($menu->url == '/schedules') {
                $menu->roles()->syncWithoutDetaching([
                    $userRole->id => [
                        'can_view' => true,
                        'can_create' => false,
                        'can_edit' => false,
                        'can_delete' => false,
                        'updated_at' => now()
                    ]
                ]);
            } elseif ($menu->url == '/availability') {
                $menu->roles()->syncWithoutDetaching([
                    $userRole->id => [
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => false,
                        'updated_at' => now()
                    ]
                ]);
            } else {
                $menu->roles()->syncWithoutDetaching([
                    $userRole->id => [
                        'can_view' => false,
                        'can_create' => false,
                        'can_edit' => false,
                        'can_delete' => false,
                        'updated_at' => now()
                    ]
                ]);
            }
        }
        
        return redirect()->route('permissions.index')
            ->with('success', 'Hak akses telah direset ke default');
    }
    
    // Method untuk menambah menu baru
    public function addMenu(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'url' => 'required|string|max:100|unique:menus,url',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'integer|min:0',
        ]);
        
        $menu = Menu::create([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon ?? 'fa-circle',
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => true,
        ]);
        
        // Assign default permissions untuk semua roles
        $roles = Role::all();
        foreach ($roles as $role) {
            $canView = ($role->name == 'admin' || $role->name == 'pastor');
            $menu->roles()->attach($role->id, [
                'can_view' => $canView,
                'can_create' => false,
                'can_edit' => false,
                'can_delete' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        return redirect()->route('permissions.index')
            ->with('success', 'Menu "' . $menu->name . '" berhasil ditambahkan');
    }
    
    // Method untuk edit menu - PERBAIKI DENGAN MENGIRIM ROLES
    public function editMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $parents = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('order')
            ->get();
        $roles = Role::all(); // Tambahkan ini untuk mengirim roles ke view
        
        return view('permissions.edit-menu', compact('menu', 'parents', 'roles'));
    }
    
    // Method untuk update menu
    public function updateMenu(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:50',
            'url' => 'required|string|max:100|unique:menus,url,' . $menu->id,
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $menu->update([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon ?? 'fa-circle',
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);
        
        return redirect()->route('permissions.index')
            ->with('success', 'Menu "' . $menu->name . '" berhasil diupdate');
    }
    
    // Method untuk update hak akses menu dari halaman edit menu (TAMBAHKAN METHOD INI)
public function updateMenuAccess(Request $request, $id)
{
    $menu = Menu::findOrFail($id);
    $permissions = $request->permissions ?? [];
    
    $roles = Role::all();
    
    foreach ($roles as $role) {
        if ($role->name == 'admin') {
            continue;
        }
        
        $rolePerms = $permissions[$role->id] ?? [];
        
        $menu->roles()->syncWithoutDetaching([
            $role->id => [
                'can_view' => isset($rolePerms['can_view']) ? 1 : 0,
                'can_create' => isset($rolePerms['can_create']) ? 1 : 0,
                'can_edit' => isset($rolePerms['can_edit']) ? 1 : 0,
                'can_delete' => isset($rolePerms['can_delete']) ? 1 : 0,
                'updated_at' => now()
            ]
        ]);
    }
    
    return redirect()->route('permissions.edit-menu', $menu->id)
        ->with('success', 'Hak akses untuk menu "' . $menu->name . '" berhasil diupdate');
}
    // Method untuk hapus menu
    public function deleteMenu(Menu $menu)
    {
        $menuName = $menu->name;
        $menu->delete();
        
        return redirect()->route('permissions.index')
            ->with('success', 'Menu "' . $menuName . '" berhasil dihapus');
    }
}