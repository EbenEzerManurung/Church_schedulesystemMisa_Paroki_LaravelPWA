<?php
// app/Traits/HasKeuskupanAccess.php

namespace App\Traits;

use App\Models\Gereja;
use App\Models\Keuskupan;

trait HasKeuskupanAccess
{
    /**
     * Get churches that user can access based on their role
     */
    public function getAccessibleChurches()
    {
        if ($this->isSuperAdmin()) {
            return Gereja::with('keuskupan')->get();
        }
        
        if ($this->isAdminKeuskupan() && $this->keuskupan_id) {
            return Gereja::where('keuskupan_id', $this->keuskupan_id)
                        ->with('keuskupan')
                        ->get();
        }
        
        if ($this->isAdminGereja() && $this->gereja_id) {
            return Gereja::where('id', $this->gereja_id)
                        ->with('keuskupan')
                        ->get();
        }
        
        return collect();
    }
    
    /**
     * Get keuskupan that user can access
     */
    public function getAccessibleKeuskupans()
    {
        if ($this->isSuperAdmin()) {
            return Keuskupan::all();
        }
        
        if ($this->isAdminKeuskupan() && $this->keuskupan_id) {
            return Keuskupan::where('id', $this->keuskupan_id)->get();
        }
        
        if ($this->isAdminGereja() && $this->keuskupan_id) {
            return Keuskupan::where('id', $this->keuskupan_id)->get();
        }
        
        return collect();
    }
    
    /**
     * Check if user can access a specific church
     */
    public function canAccessChurch($gerejaId)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        if ($this->isAdminKeuskupan()) {
            $gereja = Gereja::find($gerejaId);
            return $gereja && $gereja->keuskupan_id == $this->keuskupan_id;
        }
        
        if ($this->isAdminGereja()) {
            return $this->gereja_id == $gerejaId;
        }
        
        return false;
    }
    
    /**
     * Check if user can access a specific keuskupan
     */
    public function canAccessKeuskupan($keuskupanId)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        if ($this->isAdminKeuskupan()) {
            return $this->keuskupan_id == $keuskupanId;
        }
        
        if ($this->isAdminGereja() && $this->keuskupan_id) {
            return $this->keuskupan_id == $keuskupanId;
        }
        
        return false;
    }
}