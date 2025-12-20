<x-slide-form button-text="Loan Documents" title="Documents">


         {{-- Loan Documents Table --}}
<h2 class="text-lg font-bold mt-8 mb-4">Uploaded Documents</h2>

<x-table :headers="['#','Name', 'Notes',  'Uploaded At']" showActions="false">
    @forelse($loan->documents as $index => $document)
        <x-table.row>
            <x-table.cell>{{ $index +1 }}</x-table.cell>
            <x-table.cell>{{ ucfirst($document->name) }}</x-table.cell>
            <x-table.cell>{{ $document->notes ?? '-' }}</x-table.cell>
           
            <x-table.cell>{{ $document->created_at->format('d M, Y H:i') }}</x-table.cell>

             <x-table.cell>
                <a href="{{ route('admin.loan-documents.download', $document->id) }}"
                   class="btn"
                   target="_blank">
                    Download
                </a>

                
                    <x-confirm-modal :action="route('admin.loan-documents.destroy', $document->id)"
                        warning="Are you sure you want to delete this Document? This action cannot be undone."
                        triggerIcon="trash-2" />


            </x-table.cell>

        </x-table.row>
    @empty
    <x-empty-state message="No Documents Found." />

    @endforelse
</x-table>

{{-- Loan Documents Upload --}}
<div x-data="loanDocsUpload()" class="space-y-4 mt-6">
    <h2 class="text-lg font-bold">Attach Documents</h2>

    <template x-for="(doc, index) in documents" :key="index">
        <div class="border rounded-lg p-4 flex flex-col md:flex-row gap-4 items-start md:items-center">
            <div class="flex-1 space-y-2">
                <div>
                    <label class="block text-sm font-medium">Document Name</label>
                    <input type="text" x-model="doc.name"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm sm:text-base"
                           placeholder="Enter document name" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Notes</label>
                    <textarea x-model="doc.notes" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm sm:text-base" placeholder="Optional notes"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium">File</label>
                    <input type="file" @change="handleFileUpload($event, index)" required
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm sm:text-base"
                           accept=".pdf,.doc,.docx,.jpg,.png">
                    <p class="text-xs text-gray-500 mt-1">Max 10MB per file</p>
                </div>
            </div>

            <button type="button" @click="removeDocument(index)"
                    class="text-red-600 hover:text-red-800 mt-2 md:mt-0">
                Remove
            </button>
        </div>
    </template>

    <div class="flex gap-2">
        <button type="button" @click="addDocument()"
                class="btn">
            Add Another Document
        </button>

        <button type="button" @click="submitDocuments()"
                class="btn-success">
            Upload 
        </button>
    </div>

    <div x-show="error" class="text-red-600 text-sm mt-2" x-text="error"></div>
</div>

<script>
function loanDocsUpload() {
    return {
        documents: [
            { name: '', notes: '', file: null }
        ],
        error: '',
        maxFileSize: 10 * 1024 * 1024, // 10MB

        addDocument() {
            this.documents.push({ name: '', notes: '', file: null });
        },

        removeDocument(index) {
            this.documents.splice(index, 1);
        },

        handleFileUpload(event, index) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > this.maxFileSize) {
                this.error = 'File size exceeds 10MB!';
                event.target.value = '';
                return;
            }
            this.error = '';
            this.documents[index].file = file;
        },

        async submitDocuments() {
            this.error = '';

            // Frontend validation
            for (let doc of this.documents) {
                if (!doc.name || !doc.file) {
                    this.error = 'Each document must have a name and a file.';
                    return;
                }
            }

            let formData = new FormData();
            this.documents.forEach((doc, index) => {
                formData.append(`documents[${index}][name]`, doc.name);
                formData.append(`documents[${index}][notes]`, doc.notes || '');
                formData.append(`documents[${index}][file]`, doc.file);
            });

            try {
                const response = await fetch('{{ route('admin.loans.documents.store', $loan->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: formData
                });

                if (!response.ok) throw new Error('Upload failed');

                const data = await response.json();
                alert('Documents uploaded successfully!');
                // Optionally reload page to show uploaded docs
                location.reload();
            } catch (e) {
                this.error = e.message || 'Something went wrong.';
            }
        }
    }
}
</script>
</x-slide-form>