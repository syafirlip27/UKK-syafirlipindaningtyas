<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon; // Tambahkan ini untuk parsing tanggal

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sales;

    public function __construct(Collection $sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return $this->sales;
    }

    public function headings(): array
    {
        return [
            'Nama Pembeli',
            'No HP Pembeli',
            'Point Pembeli',
            'Produk',
            'Total Harga',
            'Total Bayar',
            'Total Discount Point',
            'Total Kembalian',
            'Tanggal Pembelian',
        ];
    }

    public function map($item): array
    {
        return [
            optional($item->customer)->name ?? 'Bukan Member',
            optional($item->customer)->no_hp ?? '-',
            optional($item->customer)->point ?? 0,
            $item->detail_sales->map(function ($detail) {
                return optional($detail->product)->name
                    ? optional($detail->product)->name . ' (' . $detail->amount . ' x Rp. ' . number_format($detail->product->price, 0, ',', '.') . ' = Rp. ' . number_format($detail->subtotal, 0, ',', '.') . ')'
                    : 'Produk tidak tersedia';
            })->implode(', '),
            'Rp. ' . number_format($item->total_price, 0, ',', '.'),
            'Rp. ' . number_format($item->total_pay, 0, ',', '.'),
            'Rp. ' . number_format(optional($item->customer)->point ?? 0, 0, ',', '.'),
            'Rp. ' . number_format($item->total_return, 0, ',', '.'),
            Carbon::parse($item->sale_date)->format('d-m-Y'),
        ];
    }
}

