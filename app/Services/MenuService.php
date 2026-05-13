<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuGroup;
use App\Models\RoleMenuAccess;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class MenuService
{
    public function getMenusForRole($role): array
    {
        $roleId = $role instanceof \Spatie\Permission\Models\Role ? $role->id : $role;

        $menuIds = RoleMenuAccess::where('role_id', $roleId)->pluck('menu_id');

        $groups = MenuGroup::active()
            ->orderBy('order')
            ->with(['menus' => function ($query) use ($menuIds) {
                $query->whereIn('id', $menuIds)
                    ->active()
                    ->parents()
                    ->with(['children' => function ($childQuery) use ($menuIds) {
                        $childQuery->whereIn('id', $menuIds)->active()->orderBy('order');
                    }]);
            }])
            ->get();

        return $groups
            ->filter(function ($group) {
                return $group->menus->isNotEmpty();
            })
            ->values()
            ->toArray();
    }

    public function buildSidebar($role): array
    {
        $roleId = $role instanceof \Spatie\Permission\Models\Role ? $role->id : $role;

        return Cache::remember("sidebar.{$roleId}", 3600, function () use ($roleId) {
            return $this->getMenusForRole($roleId);
        });
    }

    public function canAccess(string $route, $role): bool
    {
        $roleId = $role instanceof \Spatie\Permission\Models\Role ? $role->id : $role;

        $menuIds = RoleMenuAccess::where('role_id', $roleId)->pluck('menu_id');

        return Menu::whereIn('id', $menuIds)
            ->where(function ($query) use ($route) {
                $query->where('route', $route)
                    ->orWhere('url', $route);
            })
            ->exists();
    }

    public function getAccessibleRoutes($role): array
    {
        $roleId = $role instanceof \Spatie\Permission\Models\Role ? $role->id : $role;

        $menuIds = RoleMenuAccess::where('role_id', $roleId)->pluck('menu_id');

        return Menu::whereIn('id', $menuIds)
            ->whereNotNull('route')
            ->pluck('route')
            ->unique()
            ->values()
            ->toArray();
    }

    public function assignMenuToRole(int $menuId, int $roleId): void
    {
        RoleMenuAccess::firstOrCreate([
            'role_id' => $roleId,
            'menu_id' => $menuId,
        ]);

        $this->clearSidebarCache($roleId);
    }

    public function removeMenuFromRole(int $menuId, int $roleId): void
    {
        RoleMenuAccess::where('role_id', $roleId)
            ->where('menu_id', $menuId)
            ->delete();

        $this->clearSidebarCache($roleId);
    }

    public function syncRoleMenus(int $roleId, array $menuIds): void
    {
        RoleMenuAccess::where('role_id', $roleId)
            ->whereNotIn('menu_id', $menuIds)
            ->delete();

        foreach ($menuIds as $menuId) {
            RoleMenuAccess::firstOrCreate([
                'role_id' => $roleId,
                'menu_id' => $menuId,
            ]);
        }

        $this->clearSidebarCache($roleId);
    }

    public function getMenuTree(): array
    {
        $groups = MenuGroup::active()->orderBy('order')->get();

        return $groups->map(function ($group) {
            return [
                'id' => 'group_' . $group->id,
                'name' => $group->name,
                'icon' => $group->icon,
                'type' => 'group',
                'children' => Menu::where('menu_group_id', $group->id)
                    ->active()
                    ->parents()
                    ->orderBy('order')
                    ->with(['children' => function ($query) {
                        $query->active()->orderBy('order');
                    }])
                    ->get()
                    ->map(function ($menu) {
                        return [
                            'id' => $menu->id,
                            'name' => $menu->name,
                            'icon' => $menu->icon,
                            'route' => $menu->route,
                            'url' => $menu->url,
                            'type' => 'menu',
                            'children' => $menu->children->map(function ($child) {
                                return [
                                    'id' => $child->id,
                                    'name' => $child->name,
                                    'icon' => $child->icon,
                                    'route' => $child->route,
                                    'url' => $child->url,
                                    'type' => 'menu',
                                    'children' => [],
                                ];
                            }),
                        ];
                    }),
            ];
        })->toArray();
    }

    public function clearSidebarCache(int $roleId): void
    {
        Cache::forget("sidebar.{$roleId}");
    }

    public function clearAllSidebarCache(): void
    {
        $roleIds = \Spatie\Permission\Models\Role::pluck('id');
        foreach ($roleIds as $roleId) {
            Cache::forget("sidebar.{$roleId}");
        }
    }

    public function getCurrentRouteMenu(): ?Menu
    {
        $currentRoute = Route::currentRouteName();
        if (!$currentRoute) {
            return null;
        }

        return Menu::where('route', $currentRoute)->first();
    }

    public function getBreadcrumbs(string $routeName = null): array
    {
        $routeName = $routeName ?? Route::currentRouteName();
        if (!$routeName) {
            return [];
        }

        $menu = Menu::where('route', $routeName)->with('parent')->first();
        if (!$menu) {
            return [['name' => 'Dashboard', 'route' => 'dashboard']];
        }

        $breadcrumbs = [];
        $current = $menu;

        while ($current) {
            array_unshift($breadcrumbs, [
                'name' => $current->name,
                'route' => $current->route,
            ]);
            $current = $current->parent;
        }

        return $breadcrumbs;
    }
}
