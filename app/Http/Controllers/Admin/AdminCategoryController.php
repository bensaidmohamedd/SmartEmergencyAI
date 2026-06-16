<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends AdminController
{
    public function index()
    {
        $categories = Category::withCount('signalements')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ], [
            'name.unique' => 'Cette catégorie existe déjà.',
        ]);

        Category::create($validated);

        return back()->with('success', 'Catégorie créée avec succès.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
        ]);

        $category->update($validated);

        return back()->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Category $category)
    {
        if ($category->signalements()->exists()) {
            return back()->withErrors(['delete' => 'Impossible de supprimer une catégorie utilisée par des signalements.']);
        }

        $category->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }
}
