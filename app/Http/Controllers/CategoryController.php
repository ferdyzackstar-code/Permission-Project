<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Ambil kategori yang tidak punya parent (Kategori Utama)
        // $categories = \App\Models\Category::with('children')->whereNull('parent_id')->get();
        return view('dashboard.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);

        \App\Models\Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id, // Bisa null kalau kategori utama
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambah!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $category = \App\Models\Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $category = \App\Models\Category::findOrFail($id);
        $category->delete(); // Karena pakai 'cascade' di migration, anak-anaknya otomatis terhapus

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}
