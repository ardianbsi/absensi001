<?php

namespace App\Models;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleMenuAccess extends Model
{
    use HasFactory;

    protected $table = 'role_menu_access';

    protected $fillable = [
        'role_id',
        'menu_id',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
