<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    // Ubah ke /dashboard agar konsisten dengan aplikasi Anda Petshop
    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        // 1. Buat data user seperti biasa
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // 2. Tambahkan baris ini untuk memberikan role otomatis
        // Pastikan nama role 'user' sama persis dengan yang ada di database
        $user->assignRole('user');

        return $user;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered(($user = $this->create($request->all()))));

        // Kita sengaja tidak memanggil $this->guard()->login($user);
        // Supaya user harus login manual lewat form Sign In.

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }
}
