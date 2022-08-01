<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MerchantsExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportMerchants()
    {
        return Excel::download(new MerchantsExport, 'merchants-'.Carbon::now()->format('YmdHms') .'.xlsx');
    }
}
