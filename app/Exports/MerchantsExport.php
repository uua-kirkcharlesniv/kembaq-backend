<?php

namespace App\Exports;

use App\Models\Merchant;
use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MerchantsExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        return view('exports.merchants', [
            'merchants' => Merchant::with('category')->get()->makeHidden(['latitude', 'longitude', 'about', 'description', 'updated_at']),
        ]);
    }

    // public function headings(): array
    // {
    //     return [
    //         'ID',
    //         'Logo',
    //         'Background Color',
    //         'Button Color',
    //         'Business Address',
    //         'Latitude',
    //         'Longitude',
    //         'Business Name',
    //         'About',
    //         'Description',
    //         'Created At',
    //         'Updated At',
    //         'Category',
    //         'Loyalty Type',
    //         'Currency',
    //         'Loyalty Value',
    //         'Text Color',
    //         'Border Color',
    //         'Points Color',
    //         'Hero',
    //     ];
    // }
}
