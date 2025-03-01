<?php

namespace App\Http\Controllers;

use App\Exports\UsersExampleExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function showForm()
    {
        return view('pages.dashboards.users.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new UsersImport, $request->file('file'));

        return redirect()->back()->with('success', 'Users imported successfully!');
    }

    function users_example() {
        return Excel::download(new UsersExampleExport, 'Export-Algarawy.xlsx');
    }
}
