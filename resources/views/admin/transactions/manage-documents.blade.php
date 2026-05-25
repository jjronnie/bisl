<x-app-layout>
    <x-page-title title="Transaction Documents"
        subtitle="{{ $transaction->reference_number }} - {{ $transaction->member->name }}" />

    <div class="max-w-4xl mx-auto space-y-6">

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Existing Documents --}}
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

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <i data-lucide="paperclip" class="w-5 h-5"></i>
                Attached Documents
            </h3>

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
                <x-empty-state message="No documents attached to this transaction." />
            @endif
        </div>

        {{-- Upload Form --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <i data-lucide="upload" class="w-5 h-5"></i>
                Attach New Documents
            </h3>

            <form method="POST" action="{{ route('admin.transactions.documents.store', $transaction->id) }}"
                  enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div x-data="transactionDocsUpload()" class="space-y-4">
                    <template x-for="(doc, index) in documents" :key="index">
                        <div class="border rounded-lg p-4 space-y-3">
                            <div class="flex items-start justify-between">
                                <span class="text-sm font-medium text-gray-700" x-text="'Document ' + (index + 1)"></span>
                                <button type="button" @click="removeDocument(index)"
                                        class="text-red-600 hover:text-red-800 text-sm" x-show="documents.length > 1">
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Document Name</label>
                                    <input type="text" x-model="doc.name"
                                           :name="'documents[' + index + '][name]'"
                                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                           placeholder="e.g. Payment Receipt" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Notes <span class="text-gray-400">(optional)</span></label>
                                    <input type="text" x-model="doc.notes"
                                           :name="'documents[' + index + '][notes]'"
                                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                           placeholder="Optional notes">
                                </div>
                            </div>

                            {{-- Styled file picker like x-image-upload --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                                <label class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
                                       :class="doc.file ? 'border-green-400 bg-green-50' : ''">
                                    <template x-if="!doc.file">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <i data-lucide="upload" class="w-8 h-8 mb-1"></i>
                                            <span class="text-sm font-medium">Click to upload</span>
                                            <span class="text-xs text-gray-400 mt-1">PDF, Word, Excel, CSV, or Image (max 15MB)</span>
                                        </div>
                                    </template>
                                    <template x-if="doc.file">
                                        <div class="flex flex-col items-center justify-center text-green-600">
                                            <i data-lucide="check-circle" class="w-8 h-8 mb-1"></i>
                                            <span class="text-sm font-medium" x-text="doc.file.name"></span>
                                            <span class="text-xs text-gray-400 mt-1" x-text="(doc.file.size / 1024 / 1024).toFixed(2) + ' MB'"></span>
                                        </div>
                                    </template>
                                    <input type="file"
                                           :name="'documents[' + index + '][file]'"
                                           class="hidden"
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.jpeg,.png,.gif,.webp,.bmp"
                                           @change="handleFileUpload($event, index)">
                                </label>
                                <template x-if="doc.error">
                                    <p class="text-red-500 text-sm mt-1" x-text="doc.error"></p>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div class="flex gap-2">
                        <button type="button" @click="addDocument()"
                                class="btn">
                            <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                            Add Another Document
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t">
                    <a href="{{ route('admin.transactions.index') }}" class="btn-gray">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                        Back to Transactions
                    </a>
                    <button type="submit" class="btn-success">
                        <i data-lucide="upload" class="w-4 h-4 mr-1"></i>
                        Upload Documents
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
function transactionDocsUpload() {
    return {
        documents: [
            { name: '', notes: '', file: null, error: null }
        ],
        maxFileSize: 15 * 1024 * 1024, // 15MB

        addDocument() {
            this.documents.push({ name: '', notes: '', file: null, error: null });
        },

        removeDocument(index) {
            this.documents.splice(index, 1);
        },

        handleFileUpload(event, index) {
            const file = event.target.files[0];
            if (!file) return;

            this.documents[index].error = null;

            if (file.size > this.maxFileSize) {
                this.documents[index].error = 'File size exceeds 15MB!';
                event.target.value = '';
                return;
            }

            this.documents[index].file = file;
        }
    }
}
</script>
