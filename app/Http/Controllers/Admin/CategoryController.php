<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('reports')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category()]);
    }

    public function store(Request $request)
    {
        $category = Category::create($this->validated($request));
        \App\Services\ActivityLogger::log('buat', "Menambah kategori {$category->name}.", $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validated($request));
        \App\Services\ActivityLogger::log('ubah', "Mengubah kategori {$category->name}.", $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->reports()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena sudah dipakai oleh laporan.');
        }

        $category->delete();
        \App\Services\ActivityLogger::log('hapus', "Menghapus kategori {$category->name}.", $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
