<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MenuRoleAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $menuService = app(MenuService::class);

        $superAdmin = Role::findByName('Super Admin');
        $allMenuIds = Menu::pluck('id')->toArray();
        $menuService->syncRoleMenus($superAdmin->id, $allMenuIds);

        $this->command->info('All menus assigned to Super Admin role.');
    }
}
