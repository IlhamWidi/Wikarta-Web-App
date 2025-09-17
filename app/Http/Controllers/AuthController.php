<?php

namespace App\Http\Controllers;

use App\Models\Constant;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    //
    public function index(Request $request)
    {
        if (isset($request->order_id)) {
            $invoice = Invoice::where(['invoice_number' => $request->order_id])->first();
            if (isset($invoice)) {
                return redirect()->route('landing.payment', ['id' => $invoice->id]);
            } else {
                return redirect()->to('https://wikarta.co.id');
            }
        }
        return view('pages.auth.login');
    }


    public function login(Request $request)
    {
        $rules = [
            "username" => 'required',
            "password" => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("login")
                ->withErrors($validator)
                ->withInput($request->except('password'));
        } else {
            $credentials = request(['username', 'password']);
            if (!Auth::attempt($credentials)) {
                $request->session()->flash('message_error', "Wrong username or password");
                return redirect()->route("login")->withInput($request->except('password'));
            }
            $user = Auth::getProvider()->retrieveByCredentials($credentials);
            Auth::login($user, true);
            return redirect()->route("home");
        }
    }

    public function logout(Request $request)
    {
        Session::flush();
        Auth::logout();
        return redirect()->route("login");
    }
}
