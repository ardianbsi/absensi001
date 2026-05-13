<?php

namespace App\Models;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
            'is_active' => 'boolean',
    ];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'menu_group_id')->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
