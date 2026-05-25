<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionDocumentController extends Controller
{
    public function index(Transaction $transaction)
    {
        $transaction->load('documents');

        return view('admin.transactions.manage-documents', compact('transaction'));
    }

    public function store(Request $request, Transaction $transaction)
    {
        $request->validate([
            'documents.*.name' => 'required|string|max:255',
            'documents.*.notes' => 'nullable|string',
            'documents.*.file' => 'required|file|max:15360|mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png,gif,webp,bmp',
        ]);

        foreach ($request->documents as $doc) {
            $path = $doc['file']->store('transaction_documents');

            TransactionDocument::create([
                'transaction_id' => $transaction->id,
                'name' => $doc['name'],
                'notes' => $doc['notes'] ?? null,
                'file_path' => $path,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Documents uploaded successfully']);
        }

        return redirect()
            ->route('admin.transactions.documents', $transaction)
            ->with('success', 'Documents uploaded successfully.');
    }

    public function download(TransactionDocument $document)
    {
        if (! Storage::exists($document->file_path)) {
            abort(404);
        }

        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);

        $filename = $document->name;
        if (! str_ends_with(strtolower($filename), strtolower(".{$extension}"))) {
            $filename .= '.'.$extension;
        }

        return Storage::download($document->file_path, $filename);
    }

    public function destroy(TransactionDocument $document)
    {
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
}
