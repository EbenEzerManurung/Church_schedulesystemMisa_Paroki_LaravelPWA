<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Church extends Model
{
    protected $table = 'churches';
    
    protected $fillable = [
        'code', 'name', 'address', 'phone', 'email', 
        'keuskupan_code', 'keuskupan_name', 'pastor_name', 
        'description', 'is_active', 'companycd', 'plantcd', 'bacd', 'salespointcd'
    ];
    
    public function keuskupan()
    {
        return $this->belongsTo(Keuskupan::class, 'keuskupan_code', 'code');
    }
    
    public function users()
    {
        return $this->hasMany(User::class, 'church_code', 'code');
    }
}