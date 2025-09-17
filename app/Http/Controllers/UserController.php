<?php

namespace App\Http\Controllers;

use App\Models\Constant;
use App\Models\Master;
use App\Models\Branch;
use App\Models\User;
use App\Models\RoleAccessMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        $ms_roles = Master::Roles();
        $ms_cities = Master::Cities();
        $branches = Branch::all();

        $query = User::where([
            'active' => 1,
            'user_type' => Constant::USER_TYPE_ADMIN
        ]);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('username')) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $data = $query->orderBy('code', 'asc')->paginate(5)->withQueryString();

        return view('pages.user.admin.view', compact('ms_roles', 'ms_cities', 'branches', 'data'));
    }

    public function create(Request $request)
    {
        $ms_roles = Master::Roles();
        $ms_cities = Master::Cities();
        $branches = Branch::all();
        $menus = Menu::where('active', 1)
            ->where('routes', '!=', 'home')
            ->orderBy('sequence', 'asc')->get();
        $menu_access = [];
        return view('pages.user.admin.form', compact('ms_roles', 'ms_cities', 'branches', 'menus', 'menu_access'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'username' => 'required',
            'password' => 'required',
            'role_id' => 'required',
            'city_id' => 'required',
            'allowed_branches' => 'nullable|array',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("user-admin")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = new User();
                $data->code = User::generate_code();
                $data->name = $request->name;
                $data->username = $request->username;
                $data->email = $request->email;
                $data->password = $request->password;
                $data->role_id = $request->role_id;
                $data->city_id = $request->city_id;
                $data->allowed_branches = $request->allowed_branches;
                $data->address = $request->address;
                $data->phone_number = $request->phone_number;
                $data->save();

                // Simpan akses menu jika ada
                if ($request->has('menu_access')) {
                    $access = [];
                    foreach ($request->menu_access as $menu_id => $crud) {
                        $access[$menu_id] = [
                            'create' => in_array('create', $crud ?? []) ? 1 : 0,
                            'read' => in_array('read', $crud ?? []) ? 1 : 0,
                            'edit' => in_array('edit', $crud ?? []) ? 1 : 0,
                            'delete' => in_array('delete', $crud ?? []) ? 1 : 0,
                        ];
                    }
                    RoleAccessMenu::updateOrCreate(
                        ['user_id' => $data->id],
                        ['menu_access' => json_encode($access)]
                    );
                }

                DB::commit();
                return redirect()->route('user-admin')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('user-admin')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $ms_roles = Master::Roles();
        $ms_cities = Master::Cities();
        $branches = Branch::all();
        $data = User::where([
            'active' => 1,
            'user_type' => Constant::USER_TYPE_ADMIN
        ])
            ->orderBy('code', 'asc')
            ->get();
        $one = User::where(['id' => $id])->first();

        // Ambil akses menu jika ada
        $role_access_menu = RoleAccessMenu::where('user_id', $id)->first();
        $menu_access = $role_access_menu ? json_decode($role_access_menu->menu_access, true) : [];

        $menus = Menu::where('active', 1)
            ->where('routes', '!=', 'home')
            ->orderBy('sequence', 'asc')->get();

        return view('pages.user.admin.form', compact('ms_roles', 'ms_cities', 'branches', 'data', 'one', 'menus', 'menu_access'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'username' => 'required',
            'role_id' => 'required',
            'city_id' => 'required',
            'allowed_branches' => 'nullable|array',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("user-admin.edit", ['id' => $id])
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = User::where(['id' => $id])->first();
                $data->name = $request->name;
                $data->username = $request->username;
                $data->email = $request->email;
                if (isset($request->password))
                    $data->password = $request->password;
                $data->role_id = $request->role_id;
                $data->city_id = $request->city_id;
                $data->allowed_branches = $request->allowed_branches;
                $data->address = $request->address;
                $data->phone_number = $request->phone_number;
                $data->save();

                // Simpan akses menu jika ada
                if ($request->has('menu_access')) {
                    $access = [];
                    foreach ($request->menu_access as $menu_id => $crud) {
                        $access[$menu_id] = [
                            'create' => in_array('create', $crud ?? []) ? 1 : 0,
                            'read' => in_array('read', $crud ?? []) ? 1 : 0,
                            'edit' => in_array('edit', $crud ?? []) ? 1 : 0,
                            'delete' => in_array('delete', $crud ?? []) ? 1 : 0,
                        ];
                    }
                    RoleAccessMenu::updateOrCreate(
                        ['user_id' => $data->id],
                        ['menu_access' => json_encode($access)]
                    );
                }

                DB::commit();
                return redirect()->route('user-admin')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('user-admin')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function delete(Request $request)
    {
        $rules = [
            "id" => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('user-admin')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = User::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            return redirect()->route('user-admin')
                ->with('message_success', 'Delete data success.');
        }
    }
}
