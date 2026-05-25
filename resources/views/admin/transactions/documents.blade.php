@php
    $fileIcons = [
        'pdf' => 'file-text',
        'doc' => 'file-text',
        'docx' => 'file-text',
        'xls' => 'file-spreadsheet',
        'xlsx' => 'file-spreadsheet',
        'csv' => 'file-spreadsheet',
        'jpg' => 'file-image',
        'jpeg' => 'file-image',
        'png' => 'file-image',
        'gif' => 'file-image',
        'webp' => 'file-image',
        'bmp' => 'file-image',
    ];
@endphp

<div class="border-t pt-6 mt-6 px-6 pb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold flex items-center gap-2">
            <i data-lucide="paperclip" class="w-5 h-5"></i>
            Documents
        </h3>
        <a href="{{ route('admin.transactions.documents', $transaction->id) }}"
           class="btn">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
            Attach
        </a>
    </div>

    @if($transaction->documents->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($transaction->documents as $document)
                @php
                    $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                    $icon = $fileIcons[$ext] ?? 'file';
                @endphp
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="shrink-0 w-10 h-10 flex items-center justify-center bg-white rounded-lg border">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5 text-gray-500"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $document->name }}</p>
                        @if($document->notes)
                            <p class="text-xs text-gray-500 truncate">{{ $document->notes }}</p>
                        @endif
                        <p class="text-xs text-gray-400">{{ strtoupper($ext) }} &middot; {{ $document->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <a href="{{ route('admin.transaction-documents.download', $document->id) }}"
                           target="_blank"
                           class="p-2 text-gray-500 hover:text-primary-600 transition-colors"
                           title="Download">
                            <i data-lucide="download" class="w-4 h-4"></i>
                        </a>
                        <x-confirm-modal :action="route('admin.transaction-documents.destroy', $document->id)"
                            warning="Are you sure you want to delete this document? This action cannot be undone."
                            triggerIcon="trash-2" />
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-gray-500">No documents attached to this transaction.</p>
    @endif
</div>
