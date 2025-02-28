<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CornerstoneExport;
use Illuminate\Http\Request;

class CornerstoneController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $uuids = ['220b6576-f91f-4f96-bc36-13654f3c4885'];

        return Excel::download(new CornerstoneExport($uuids), 'cornerstone.xlsx');
    }
}
