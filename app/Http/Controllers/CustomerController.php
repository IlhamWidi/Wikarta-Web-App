<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Constant;
use App\Models\Helper;
use App\Models\Master;
use App\Models\Package;
use App\Models\User;

class CustomerController extends Controller
{
    //
    public function create(Request $request)
    {
        $ms_packages = Package::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();
        $ms_brances = Branch::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();
        // Untuk form tambah, tidak ada data user (one)
        return view('pages.user.customer.form', compact('ms_packages', 'ms_brances'));
    }

    public function index(Request $request)
    {
        $ms_packages = Package::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();
        $ms_brances = Branch::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();

        $query = User::where([
            'active' => 1,
            'user_type' => Constant::USER_TYPE_CUSTOMER
        ]);

        // Filter by ID (code)
        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }
        // Filter by Name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        // Filter by PhoneNumber
        if ($request->filled('phone_number')) {
            $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
        }
        // Filter by Branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        // Filter by Package
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->except('page'));
        $isEmpty = $data->isEmpty();

        // Kirim filter ke view agar tetap terisi
        $filters = [
            'code' => $request->code,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'branch_id' => $request->branch_id,
            'package_id' => $request->package_id,
        ];

        return view('pages.user.customer.view', compact('ms_packages', 'ms_brances', 'data', 'filters', 'isEmpty'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'username' => 'required',
            'phone_number' => 'required',
            // 'email' => 'required',
            'password' => 'required',
            'branch_id' => 'required',
            'package_id' => 'required',
            'identity_number' => 'required',
            'lampiran_foto_ktp' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'lampiran_foto_rumah' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'coordinates' => 'required',
            'kode_server' => 'nullable|string|max:255',
            'password_server' => 'nullable|string|max:255',
            'vlan' => 'nullable|string|max:255',
            'odp' => 'nullable|string|max:255',
            'opm' => 'nullable|string|max:255',
            'odc' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("user-customer.create")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();

                $lampiran_foto_ktp = null;
                if ($request->hasFile('lampiran_foto_ktp')) {
                    $ext = $request->file('lampiran_foto_ktp')->guessExtension();
                    $name = sprintf("%s_%s_ktp", Helper::str_to_slug($request->name), time());
                    $lampiran_foto_ktp = "storage/" . $request->file('lampiran_foto_ktp')->storeAs('image', $name . '.' . $ext, 'public');
                }
                $lampiran_foto_rumah = null;
                if ($request->hasFile('lampiran_foto_rumah')) {
                    $ext = $request->file('lampiran_foto_rumah')->guessExtension();
                    $name = sprintf("%s_%s_ktp", Helper::str_to_slug($request->name), time());
                    $lampiran_foto_rumah = "storage/" . $request->file('lampiran_foto_rumah')->storeAs('image', $name . '.' . $ext, 'public');
                }

                $data = new User();
                $data->user_type = Constant::USER_TYPE_CUSTOMER;
                $data->code = User::generateCustomerCode();
                $data->name = $request->name;
                $data->email = $request->email;
                $data->username = $request->username;
                $data->phone_number = $request->phone_number;
                $data->branch_id = $request->branch_id;
                $data->package_id = $request->package_id;
                $data->subscribe_price = floatval(str_replace(",", "", $request->subscribe_price));
                $data->registration_price = floatval(str_replace(",", "", $request->registration_price));
                $data->identity_number = $request->identity_number;
                $data->password = $request->password;
                $data->coordinates = $request->coordinates;
                $data->address = $request->address;
                $data->lampiran_foto_ktp = $lampiran_foto_ktp;
                $data->lampiran_foto_rumah = $lampiran_foto_rumah;
                $data->status = $request->status == "on" ? true : false;
                $data->collection_date = now();
                $data->kode_server = $request->kode_server;
                $data->password_server = $request->password_server;
                $data->vlan = $request->vlan;
                $data->odp = $request->odp;
                $data->opm = $request->opm;
                $data->odc = $request->odc;
                $data->keterangan = $request->keterangan;
                $data->save();
                DB::commit();
                return redirect()->route('user-customer')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('user-customer.create')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $ms_packages = Package::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();

        $ms_brances = Branch::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();

        $data = User::where([
            'active' => 1,
            'user_type' => Constant::USER_TYPE_CUSTOMER
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        $one = User::where(['id' => $id])->first();

        return view('pages.user.customer.form', compact('ms_packages', 'ms_brances', 'data', 'one'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'code' => 'required',
            'username' => 'required',
            'phone_number' => 'required',
            // 'email' => 'required',
            'branch_id' => 'required',
            'package_id' => 'required',
            'identity_number' => 'required',
            'lampiran_foto_ktp' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'lampiran_foto_rumah' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'coordinates' => 'required',
            'kode_server' => 'nullable|string|max:255',
            'password_server' => 'nullable|string|max:255',
            'vlan' => 'nullable|string|max:255',
            'odp' => 'nullable|string|max:255',
            'opm' => 'nullable|string|max:255',
            'odc' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("user-customer.edit", ['id' => $id])
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = User::where(['id' => $id])->first();

                $lampiran_foto_ktp = $data->lampiran_foto_ktp;
                if ($request->hasFile('lampiran_foto_ktp')) {
                    $ext = $request->file('lampiran_foto_ktp')->guessExtension();
                    $name = sprintf("%s_%s_ktp", Helper::str_to_slug($request->name), time());
                    $lampiran_foto_ktp = "storage/" . $request->file('lampiran_foto_ktp')->storeAs('image', $name . '.' . $ext, 'public');
                }

                $lampiran_foto_rumah = $data->lampiran_foto_rumah;
                if ($request->hasFile('lampiran_foto_rumah')) {
                    $ext = $request->file('lampiran_foto_rumah')->guessExtension();
                    $name = sprintf("%s_%s_ktp", Helper::str_to_slug($request->name), time());
                    $lampiran_foto_rumah = "storage/" . $request->file('lampiran_foto_rumah')->storeAs('image', $name . '.' . $ext, 'public');
                }

                $data->user_type = Constant::USER_TYPE_CUSTOMER;
                $data->code = $request->code;
                $data->name = $request->name;
                $data->username = $request->username;
                $data->phone_number = $request->phone_number;
                $data->email = $request->email;
                $data->branch_id = $request->branch_id;
                $data->package_id = $request->package_id;
                $data->subscribe_price = floatval(str_replace(",", "", $request->subscribe_price));
                $data->registration_price = floatval(str_replace(",", "", $request->registration_price));
                $data->identity_number = $request->identity_number;
                $data->password = $request->password;
                $data->coordinates = $request->coordinates;
                $data->address = $request->address;
                $data->lampiran_foto_ktp = $lampiran_foto_ktp;
                $data->lampiran_foto_rumah = $lampiran_foto_rumah;
                $data->status = $request->status == "on" ? true : false;
                $data->kode_server = $request->kode_server;
                $data->password_server = $request->password_server;
                $data->vlan = $request->vlan;
                $data->odp = $request->odp;
                $data->opm = $request->opm;
                $data->odc = $request->odc;
                $data->keterangan = $request->keterangan;
                $data->save();

                DB::commit();
                return redirect()->route('user-customer')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('user-customer')
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
            return redirect()->route('user-customer')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = User::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            return redirect()->route('user-customer')
                ->with('message_success', 'Delete data success.');
        }
    }

    public function detail(Request $request, $id)
    {
        $data = User::where(['id' => $id])->first();
        return view('pages.user.customer.detail', compact('data'));
    }
}
