<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index(Request $request)
    {
        $query = Categorie::withCount('produits');

        if ($request->filled('search')) {
            $query->where('nom_categorie', 'LIKE', "%{$request->search}%");
        }

        $categories = $query->orderBy('nom_categorie')->paginate(15)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_categorie' => 'required|string|max:100|unique:categories,nom_categorie',
            'description' => 'nullable|string',
        ], [
            'nom_categorie.required' => 'Le nom de la catégorie est obligatoire.',
            'nom_categorie.unique' => 'Cette catégorie existe déjà.',
        ]);

        Categorie::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie ajoutée avec succès.');
    }

    public function edit(Categorie $categorie)
    {
        return view('categories.edit', compact('categorie'));
    }

    public function update(Request $request, Categorie $categorie)
    {
        $validated = $request->validate([
            'nom_categorie' => 'required|string|max:100|unique:categories,nom_categorie,' . $categorie->id_categorie . ',id_categorie',
            'description' => 'nullable|string',
        ]);

        $categorie->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie modifiée avec succès.');
    }

    public function destroy(Categorie $categorie)
    {
        if ($categorie->produits()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des produits.');
        }

        $categorie->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}