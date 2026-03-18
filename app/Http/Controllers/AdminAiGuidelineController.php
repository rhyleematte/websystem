<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiGuideline;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class AdminAiGuidelineController extends Controller
{
    public function index()
    {
        $guidelines = AiGuideline::latest()->get();
        return view('admin.guidelines.index', compact('guidelines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,txt|max:10240', // 10MB max
        ]);

        $file = $request->file('document');
        $originalFilename = $file->getClientOriginalName();
        $path = $file->store('ai_guidelines');

        AiGuideline::create([
            'title' => $request->title,
            'file_path' => $path,
            'original_filename' => $originalFilename,
            'is_parsed' => false,
        ]);

        // Trigger background parsing - in this demo we might handle it synchronously or queue it.
        // For now, let's keep it pending so our Background Parser job can pick it up.

        return back()->with('success', 'Guideline document uploaded successfully. It will be parsed in the background.');
    }

    public function destroy($id)
    {
        $guideline = AiGuideline::findOrFail($id);
        
        // Delete the file from storage
        if (Storage::exists($guideline->file_path)) {
            Storage::delete($guideline->file_path);
        }

        $guideline->delete();

        return back()->with('success', 'Guideline deleted successfully.');
    }
}
