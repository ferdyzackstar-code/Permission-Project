<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Exports\UsersImportTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('roles')->select('users.*');

            return DataTables::eloquent($users)
                ->addIndexColumn()
                ->addColumn('roles', function (User $user) {
                    if ($user->roles->isEmpty()) {
                        return '<span class="badge badge-secondary">No Role</span>';
                    }

                    return $user->roles
                        ->map(function ($role) {
                            return '<span class="badge badge-success mr-1">' . e($role->name) . '</span>';
                        })
                        ->implode(' ');
                })
                ->addColumn('action', function (User $user) {
                    return '<button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#modalShowUser' .
                        $user->id .
                        '">
                            <i class="fa fa-eye"></i> Show
                        </button>
                        <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#modalEditUser' .
                        $user->id .
                        '">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <form method="POST" action="' .
                        route('dashboard.users.destroy', $user->id) .
                        '" class="delete-form" style="display:inline;">
                            ' .
                        csrf_field() .
                        '
                            <input name="_method" type="hidden" value="DELETE">
                            <button type="button" class="btn btn-danger btn-sm show_confirm">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>';
                })
                ->rawColumns(['roles', 'action'])
                ->make(true);
        }

        return view('dashboard.users.index', $this->getIndexData());
    }

    /**
     * Data kebutuhan view index user berbasis modal.
     */
    protected function getIndexData(): array
    {
        return [
            'data' => User::with('roles')->latest()->get(),
            'roles' => Role::pluck('name', 'name')->all(),
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('dashboard.users.index', $this->getIndexData());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
        ]);

        // Ambil semua input
        $input = $request->all();

        // Hash passwordnya
        $input['password'] = Hash::make($input['password']);

        // --- PERBAIKAN DI SINI ---
        // Hapus field yang bukan bagian dari kolom tabel users
        $input = Arr::except($input, ['confirm-password', 'roles']);

        // Sekarang aman untuk dicreate
        $user = User::create($input);

        // Role diberikan lewat Spatie, bukan lewat create() tadi
        $user->assignRole($request->input('roles'));

        return redirect()->route('dashboard.users.index')->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        return view('dashboard.users.index', $this->getIndexData());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        return view('dashboard.users.index', $this->getIndexData());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password', // Password opsional saat update
            'roles' => 'required',
        ]);

        $input = $request->all();

        // Logika jika password diisi (ingin ganti password)
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            // Jika password kosong, hapus dari array agar tidak menimpa password lama dengan null
            $input = Arr::except($input, ['password']);
        }

        // --- PERBAIKAN: Buang field yang bukan kolom tabel users ---
        $input = Arr::except($input, ['confirm-password', 'roles', '_token', '_method']);

        $user = User::find($id);
        $user->update($input);

        // Update Role (Hapus role lama, lalu masukkan yang baru)
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));

        return redirect()->route('dashboard.users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();

        return redirect()->route('dashboard.users.index')->with('success', 'User deleted successfully');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $import = new \App\Imports\UsersImport();

        $import->import($file);

        if ($import->failures()->isNotEmpty()) {
            return back()->with('import_failures', $import->failures());
        }

        return redirect()->route('dashboard.users.index')->with('success', 'Data berhasil diimport!');
    }

    public function export()
    {
        $users = User::with('roles')->get();
        $roles = \Spatie\Permission\Models\Role::all();

        return Excel::download(new UsersExport($users, $roles), 'data_users_anda_petshop.xlsx');
    }

    public function downloadImportTemplate()
    {
        return Excel::download(new UsersImportTemplateExport(), 'template_import_data_users.xlsx');
    }
}
