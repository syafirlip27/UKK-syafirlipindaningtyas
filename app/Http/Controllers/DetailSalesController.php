<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Exports\Salesimport;
use App\Models\Customers;
use App\Models\Detail_sales;
use App\Models\Sales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

class DetailSalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentDate = Carbon::now()->toDateString();

        // Hitung jumlah transaksi hari ini
        $todaySalesCount = detail_sales::whereDate('created_at', $currentDate)->count();
        
        // Ambil seluruh data penjualan tanpa batasan bulan atau tahun
        $sales = detail_sales::selectRaw('DATE(created_at) AS date, COUNT(*) AS total')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();
        
        $detail_sales = detail_sales::with('sales', 'product')->get();
        
        // Ubah hasil query menjadi array terstruktur
        $labels = $sales->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M Y'))->toArray();
        $salesData = $sales->pluck('total')->toArray();

        $productShell = detail_sales::with('product')
        ->selectRaw('product_id, SUM(amount) as total_amount')
        ->groupBy('product_id')
        ->get();
    
        // Ambil nama produk sebagai label dan jumlah produk terjual sebagai data
        $labelspieChart = $productShell->map(fn($item) => $item->product->name . ' : ' . $item->total_amount)->toArray();
        $salesDatapieChart = $productShell->map(fn($item) => $item->total_amount)->toArray();
        
        return view('module.dashboard.index', compact('labels', 'salesData', 'detail_sales', 'todaySalesCount', 'productShell', 'labelspieChart', 'salesDatapieChart'));
        
    }


    public function show(Request $request, $id)
    {
        // Ambil sale berdasarkan id
        $sale = sales::with('detail_sales.product')->findOrFail($id);
        // check request apakah dia ngirim request poin yang artinya dia adalah member jika tidak ada maka dia non member
        if($request->check_poin){
            // Proses pengurangan point
            $customer = customers::where('id', $request->customer_id)->first();
            $sale->update([
                'total_point' => $customer->point,
                'total_pay' => $sale->total_pay - $customer->point,
                'total_return' => $sale->total_return + $customer->point,
                'total_discount' => $sale->total_price - $customer->point,
            ]);

            $customer->update([
                'name' => $request->name ? $request->name : $customer->name,
                'point' => 0
            ]);
        }
        if ($request->name) {
            $customer = customers::where('id', $request->customer_id)->first();
            $customer->update([
                'name' => $request->name
            ]);
        }
        return view('module.sale.print-sale', compact('sale'));
    }

    public function downloadPDF($id) {
        try {
            $sale = sales::with('detail_sales.product')->findOrFail($id);

            $pdf = FacadePdf::loadView('module.sale.download', ['sale' => $sale]);
            Log::info('PDF berhasil diunduh untuk transaksi dengan ID ' . $id);

            return $pdf->download('Surat_receipt.pdf');
        } catch (\Exception $e) {
            Log::error('Gagal mengunduh PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh PDF');
        }
    }

    public function exportExcel(Request $request)
{
    // Filter berdasarkan tanggal
    $salesQuery = Sales::with('customer', 'user', 'detail_sales');

    // Pastikan filter_type dan filter_value diterima dari request
    if ($request->filter_type && $request->filter_value) {
        $filterType = $request->filter_type;
        $filterValue = $request->filter_value;

        if ($filterType == 'daily') {
            // Pastikan filterValue adalah tanggal yang benar
            $salesQuery->whereDate('sale_date', Carbon::parse($filterValue));
        } elseif ($filterType == 'monthly') {
            // Pastikan filterValue adalah bulan dan tahun dalam format 'YYYY-MM'
            $salesQuery->whereMonth('sale_date', Carbon::parse($filterValue)->month)
                ->whereYear('sale_date', Carbon::parse($filterValue)->year);
        } elseif ($filterType == 'yearly') {
            // Pastikan filterValue adalah tahun dalam format 'YYYY'
            $salesQuery->whereYear('sale_date', $filterValue);
        }
    }

    // Ambil data penjualan sesuai filter
    $sales = $salesQuery->get();

    // Jika tidak ada data yang ditemukan
    if ($sales->isEmpty()) {
        return redirect()->back()->with('error', 'Tidak ada data untuk filter yang dipilih.');
    }

    // Mendownload Excel
    return Excel::download(new SalesExport($sales), 'penjualan.xlsx');
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(detail_sales $detail_sales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, detail_sales $detail_sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(detail_sales $detail_sales)
    {
        //
    }
}
