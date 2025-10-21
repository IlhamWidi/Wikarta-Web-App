<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\RoleAccessMenu;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            $user = Auth::user();
            $title = $request->segment(1);
            $title = str_replace("-", " ", $title);
            $title = ucwords($title);

            $menus = collect();
            if (isset($user)) {
                // Ambil akses menu user
                $roleAccess = RoleAccessMenu::where('user_id', $user->id)->first();
                $allowedMenuIds = [];
                if ($roleAccess && is_array($roleAccess->menu_access)) {
                    $allowedMenuIds = $roleAccess->menu_access;
                } elseif ($roleAccess && is_string($roleAccess->menu_access)) {
                    $allowedMenuIds = json_decode($roleAccess->menu_access, true) ?? [];
                }

                // Ambil menu yang diizinkan
                $menus = \App\Models\Menu::where('active', 1)
                    ->whereIn('id', $allowedMenuIds)
                    ->orderBy('sequence', 'asc')
                    ->get();

                $auth = User::where(['id' => $user->id])->first();
                if (isset($auth)) {
                    View::share([
                        "name" => $auth["name"],
                        "username" => $auth["username"],
                        "role_id" => $auth["role_id"],
                        "title" => $title,
                        "menus" => $menus,
                    ]);
                }
            }
            return $next($request);
        });
    }
}
