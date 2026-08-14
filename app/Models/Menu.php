<?php
// app/Models/Menu.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'url',
        'route_name',
        'icon',
        'parent_id',
        'order',
        'permission_name',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Parent menu relationship
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }
    
    /**
     * Child menus relationship
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }
    
    /**
     * Roles that have access to this menu
     */
    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'menu_roles')
                    ->withPivot('can_view', 'can_create', 'can_edit', 'can_delete')
                    ->withTimestamps();
    }
    
    /**
     * Check if role has access to this menu
     *
     * @param \Spatie\Permission\Models\Role $role
     * @param string $permission
     * @return bool
     */
    public function hasAccess($role, $permission = 'view')
    {
        $pivot = $this->roles->firstWhere('id', $role->id);
        
        if (!$pivot) {
            return false;
        }
        
        switch ($permission) {
            case 'create':
                return $pivot->pivot->can_create ?? false;
            case 'edit':
                return $pivot->pivot->can_edit ?? false;
            case 'delete':
                return $pivot->pivot->can_delete ?? false;
            default:
                return $pivot->pivot->can_view ?? false;
        }
    }
    
    /**
     * Scope for active menus
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope for parent menus
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
    
    /**
     * Get all menus as tree
     */
    public static function getMenuTree()
    {
        return self::with('children')
            ->parents()
            ->active()
            ->orderBy('order')
            ->get();
    }
}