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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:user.index|user.create|user.edit|user.delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:user.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('roles')->select('users.*');

            return DataTables::eloquent($users)
                ->addIndexColumn()
                ->addColumn('image', function (User $user) {
                    $path = 'storage/uploads/users/' . $user->image;
                    $url = $user->image && file_exists(public_path($path)) ? asset($path) : asset('storage/uploads/users/default-user.jpg');

                    return '<img src="' . $url . '" width="50" class="img-thumbnail shadow-sm">';
                })
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
                ->rawColumns(['roles', 'action', 'image'])
                ->make(true);
        }

        return view('dashboard.users.index', $this->getIndexData());
    }

    protected function getIndexData(): array
    {
        return [
            'data' => User::with('roles')->latest()->get(),
            'roles' => Role::pluck('name', 'name')->all(),
        ];
    }

    public function create(): View
    {
        return view('dashboard.users.index', $this->getIndexData());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'roles' => 'required',
        ]);

        $input = $request->all();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '-' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('storage/uploads/users');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $input['image'] = $filename;
        }

        $input['password'] = Hash::make($input['password']);
        $input = Arr::except($input, ['confirm-password', 'roles']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('dashboard.users.index')->with('success', 'User Berhasil Ditambahkan.');
    }

    public function show($id): View
    {
        return view('dashboard.users.index', $this->getIndexData());
    }

    public function edit($id): View
    {
        return view('dashboard.users.index', $this->getIndexData());
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|same:confirm-password',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'roles' => 'required',
        ]);

        $user = User::find($id);
        $input = $request->all();

        if ($request->hasFile('image')) {
            $destinationPath = public_path('storage/uploads/users');

            if ($user->image && $user->image !== 'default-user.jpg' && File::exists($destinationPath . '/' . $user->image)) {
                File::delete($destinationPath . '/' . $user->image);
            }

            $file = $request->file('image');
            $filename = time() . '-' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $input['image'] = $filename;
        }

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        $input = Arr::except($input, ['confirm-password', 'roles', '_token', '_method']);
        $user->update($input);

        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));

        return redirect()->route('dashboard.users.index')->with('success', 'User Berhasil Diupdate.');
    }

    public function destroy($id): RedirectResponse
    {
        $user = User::find($id);

        if ($user->image && $user->image !== 'default-user.jpg') {
            $oldFilePath = public_path('storage/uploads/users/' . $user->image);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }

        $user->delete();

        return redirect()->route('dashboard.users.index')->with('success', 'User Berhasil Dihapus');
    }

    public function downloadImportTemplate()
    {
        return Excel::download(new UsersImportTemplateExport(), 'template_import_data_users.xlsx');
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

        $fileName = 'data_users_anda_petshop_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new \App\Exports\UsersExport($users, $roles), $fileName);
    }
}
