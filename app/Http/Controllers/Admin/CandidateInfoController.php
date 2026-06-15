<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateInformation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CandidateInfoController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/CandidateInfo/Index', [
            'sections' => CandidateInformation::orderBy('section')->orderBy('order')->get()->groupBy('section'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/CandidateInfo/Edit', [
            'info' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'integer|min:0',
        ]);

        CandidateInformation::create($validated);

        return redirect()->route('admin.info.index')->with('success', 'Información creada.');
    }

    public function edit(CandidateInformation $info)
    {
        return Inertia::render('Admin/CandidateInfo/Edit', [
            'info' => $info,
        ]);
    }

    public function update(Request $request, CandidateInformation $info)
    {
        $validated = $request->validate([
            'section' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'integer|min:0',
        ]);

        $info->update($validated);

        return redirect()->route('admin.info.index')->with('success', 'Información actualizada.');
    }

    public function destroy(CandidateInformation $info)
    {
        $info->delete();

        return redirect()->route('admin.info.index')->with('success', 'Información eliminada.');
    }
}
