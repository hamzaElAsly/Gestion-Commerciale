<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::latest()->paginate(10);
        return view('note.index', compact('notes'));
    }

    public function create()
    {
        return view('note.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'nom_client'  => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Note::create($validated);

        return redirect()->route('note.index')->with('success', 'Note ajoutée avec succès.');
    }

    public function show(string $id)
    {
        $note = Note::findOrFail($id);
        return view('note.show', compact('note'));
    }

    public function edit(string $id)
    {
        $note = Note::findOrFail($id);
        return view('note.edit', compact('note'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'nom_client'  => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $note = Note::findOrFail($id);
        $note->update($validated);

        return redirect()->route('note.index')->with('success', 'Note modifiée avec succès.');
    }

    public function destroy(string $id)
    {
        $note = Note::findOrFail($id);
        $note->delete();

        return redirect()->route('note.index')->with('success', 'Note supprimée avec succès.');
    }
}