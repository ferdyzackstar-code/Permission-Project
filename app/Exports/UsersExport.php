<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExport implements FromView, ShouldAutoSize
{
    protected $users;
    protected $roles;
    
    public function __construct($users, $roles) {
        $this->users = $users;
        $this->roles = $roles;
    }

    public function view(): View
    {   
        return view('dashboard.users.export_excel', [
            'users' => $this->users,
            'roles' => $this->roles
        ]);
    }
}
