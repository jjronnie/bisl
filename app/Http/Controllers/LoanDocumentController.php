<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LoanDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, Loan $loan)
    {
        $request->validate([
            'documents.*.name' => 'required|string|max:255',
            'documents.*.notes' => 'nullable|string',
            'documents.*.file' => 'required|file|max:10240', // 10MB
        ]);

        $uploadedDocuments = [];

        foreach ($request->documents as $doc) {
            $path = $doc['file']->store('loan_documents');

            $uploadedDocuments[] = LoanDocument::create([
                'loan_id' => $loan->id,
                'name' => $doc['name'],
                'notes' => $doc['notes'] ?? null,
                'file_path' => $path,
            ]);
        }

        return response()->json([
            'message' => 'Documents uploaded successfully',
            'documents' => $uploadedDocuments,
        ]);
    }

    public function download(LoanDocument $document)
    {
        if (! Storage::exists($document->file_path)) {
            abort(404);
        }

        // Get original file extension
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);

        // Append extension to the name if it doesn't already have it
        $filename = $document->name;
        if (! str_ends_with(strtolower($filename), strtolower(".{$extension}"))) {
            $filename .= '.'.$extension;
        }

        return Storage::download($document->file_path, $filename);
    }

    /**
     * Display the specified resource.
     */
    public function show(LoanDocument $loanDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoanDocument $loanDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoanDocument $loanDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoanDocument $document)
    {

        // Delete file from storage
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        // Delete record from database
        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
}
