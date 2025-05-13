<?php

namespace App\Http\Controllers;

use App\Models\Detail_sales;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $products = products::all();
        $products = products::orderBy('name', 'asc')->get();
        return view('module.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('module.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'required|image|max:8192' //max 8mb
        ],[
            'image.max' => 'Silahkan pilih foto yang tidak lebih dari 8mb'
        ]);
    
        $imagePath = $request->file('image')->store('products', 'public');
    
        products::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath
        ]);
    
        return redirect()->route('product')->with('success', 'Berhasil Membuat Product');
    }


    /**
     * Display the specified resource.
     */
    public function show(products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = products::findOrFail($id);
        return view('module.product.edit', compact('item'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            // 'stock' => 'required|numeric',
            'image' => 'nullable|image|max:8192' // Nullable agar tidak wajib upload gambar baru
        ]);
    
        $product = Products::findOrFail($id);
    
        // Jika ada gambar baru yang diupload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }
    
        // Update data produk
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            // 'stock' => $request->stock,
        ]);
    
        return redirect()->route('product')->with('success', 'Produk berhasil diperbarui!');
    }
    
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|numeric|min:0',
        ]);
    
        // Cari produk berdasarkan ID
        $product = Products::findOrFail($id);
    
        // Update hanya stok produk
        $product->update([
            'stock' => $request->stock,
        ]);
        return redirect()->route('product')->with('success', 'Stock berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Cek apakah produk memiliki relasi di detail_sales
        $isProductUsed = detail_sales::where('product_id', $id)->exists();
    
        if ($isProductUsed) {
            return redirect()->route('product')->with('error', 'Produk tidak bisa dihapus karena sudah masuk transaksi.');
        }
    
        // Jika tidak ada relasi, hapus produk
        products::where('id', $id)->delete();
        
        return redirect()->route('product')->with('success', 'Berhasil menghapus produk.');
    }
    
}
