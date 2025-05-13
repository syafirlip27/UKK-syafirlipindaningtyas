<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Detail_sales;
use App\Models\Products;
use App\Models\Sales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
{
    $salesQuery = Sales::with(['customer', 'user', 'detail_sales']);

    if ($request->filter_type && $request->filter_value) {
        $filterValue = $request->filter_value;
        $date = \Carbon\Carbon::parse($filterValue);

        switch ($request->filter_type) {
            case 'daily':
                $salesQuery->whereDate('sale_date', $date);
                break;
            case 'monthly':
                $salesQuery->whereMonth('sale_date', $date->month)
                           ->whereYear('sale_date', $date->year);
                break;
            case 'yearly':
                $salesQuery->whereYear('sale_date', $date->year);
                break;
        }
    }

    $sales = $salesQuery->get();

    return view('module.sale.index', compact('sales'));
}


    


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::all();
        return view('module.sale.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->has('shop')) {
            return back()->with('error', 'Pilih produk terlebih dahulu!');
        }

        // Hapus data sebelumnya agar tidak terjadi duplikasi
        session()->forget('shop');

        $selectedProducts = $request->shop;

        // Pastikan data dikirim dalam bentuk array
        if (!is_array($selectedProducts)) {
            return back()->with('error', 'Format data tidak valid!');
        }

        // Simpan hanya produk yang memiliki jumlah lebih dari 0, hapus duplikasi
        $filteredProducts = collect($selectedProducts)
            ->mapWithKeys(function ($item) {
                $parts = explode(';', $item);
                if (count($parts) > 3) {
                    $id = $parts[0];
                    return [$id => $item]; // Pastikan hanya 1 produk per ID
                }
                return [];
            })
            ->values()
            ->toArray();

        // Simpan ke sesi
        session(['shop' => $filteredProducts]);

        return redirect()->route('sales.post');
    }


    public function post()
    {
        $shop = session('shop', []);
        return view('module.sale.detail', compact('shop'));
    }

    public function createsales(Request $request)
    {
        $request->validate([
            'total_pay' => 'required',
        ], [
            'total_pay.required' => 'Berapa jumlah uang yang dibayarkan?',
        ]);
        $newPrice = (int) preg_replace('/\D/', '', $request->total_price);
        $newPay = (int) preg_replace('/\D/', '', $request->total_pay);
        $newreturn = $newPay - $newPrice;

        if ($request->member === 'Member') {
            // Mengecek apakah customer sudah pernah melakukan pembelian sebelumnya
            $existCustomer = customers::where('no_hp', $request->no_hp)->first();
            // Akumulasi Point
            $point = floor($newPrice / 100);
            if ($existCustomer) {
                // Jika customer sebelumnya sudah ada, maka update point
                $existCustomer->update([
                    'point' => $existCustomer->point + $point,
                ]);
                // Ambil ID customer
                $customer_id = $existCustomer->id;
            } else {
                // Jika customer baru, maka create customer baru
                $existCustomer = customers::create([
                    'name' => "",
                    'no_hp' => $request->no_hp,
                    'point' => $point,
                ]);
                // Ambil ID customer baru
                $customer_id = $existCustomer->id;
            }
            // Membuat data penjualan
            $sales = sales::create([
                'sale_date' => Carbon::now()->format('Y-m-d'),
                'total_price' => $newPrice,
                'total_pay' => $newPay,
                'total_return' => $newreturn,
                'customer_id' => $customer_id,
                'user_id' => Auth::id(),
                'point' => floor($newPrice / 100),
                'total_point' => 0,
            ]);
            $detailSalesData = [];

            foreach ($request->shop as $shopItem) {
                $item = explode(';', $shopItem);
                $productId = (int) $item[0];
                $amount = (int) $item[3];
                $subtotal = (int) $item[4];

                $detailSalesData[] = [
                    'sale_id' => $sales->id,
                    'product_id' => $productId,
                    'amount' => $amount,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // //menyebabkan duplikasi data
                // detail_sales::insert($detailSalesData);

                // Update stok produk di database
                $product = products::find($productId);
                if ($product) {
                    $newStock = $product->stock - $amount;
                    if ($newStock < 0) {
                        return redirect()->back()->withErrors(['error' => 'Stok tidak mencukupi untuk produk ' . $product->name]);
                    }
                    $product->update(['stock' => $newStock]);
                }
            }
            detail_sales::insert($detailSalesData);
            return redirect()->route('sales.create.member', ['id' => sales::latest()->first()->id])
                ->with('message', 'Silahkan daftar sebagai member');
        } else {
            $sales = sales::create([
                'sale_date' => Carbon::now()->format('Y-m-d'),
                'total_price' => $newPrice,
                'total_pay' => $newPay,
                'total_return' => $newreturn,
                'customer_id' => $request->customer_id,
                'user_id' => Auth::id(),
                'point' => 0,
                'total_point' => 0,
            ]);

            $detailSalesData = [];

            foreach ($request->shop as $shopItem) {
                $item = explode(';', $shopItem);
                $productId = (int) $item[0];
                $amount = (int) $item[3];
                $subtotal = (int) $item[4];

                $detailSalesData[] = [
                    'sale_id' => $sales->id,
                    'product_id' => $productId,
                    'amount' => $amount,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

             

                // Update stok produk di database
                $product = products::find($productId);
                if ($product) {
                    $newStock = $product->stock - $amount;
                    if ($newStock < 0) {
                        return redirect()->back()->withErrors(['error' => 'Stok tidak mencukupi untuk produk ' . $product->name]);
                    }
                    $product->update(['stock' => $newStock]);
                }
            }
            detail_sales::insert($detailSalesData);
            return redirect()->route('sales.print.show', ['id' => $sales->id])->with('Silahkan Print');
        }

    }


    /**
     * Display the specified resource.
     */
    public function createmember($id)
    {
        $sale = sales::with('detail_sales.product')->findOrFail($id);
        // Menentukan apakah customer sudah pernah melakukan pembelian sebelumnya
        $notFirst = sales::where('customer_id', $sale->customer->id)->count() != 1 ? true : false;
        return view('module.sale.view-member', compact('sale','notFirst'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(sales $sales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, sales $sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(sales $sales)
    {
        //
    }
}