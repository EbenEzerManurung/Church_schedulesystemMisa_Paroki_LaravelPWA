<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    protected function hasAccess($menuUrl, $permission = 'view')
    {
        $user = auth()->user();
        if (!$user) return false;
        
        $role = $user->roles->first();
        if (!$role) return false;
        
        if ($role->name === 'admin') return true;
        
        $menu = \App\Models\Menu::where('url', $menuUrl)->first();
        if (!$menu) return false;
        
        return $menu->hasAccess($role, $permission);
    }
}