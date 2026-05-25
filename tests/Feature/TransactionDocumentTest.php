<?php

use App\Models\Member;
use App\Models\Transaction;
use App\Models\TransactionDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

function transactionDocAdminUser(): User
{
    $role = Role::findOrCreate('admin');
    $admin = User::factory()->create();
    $admin->assignRole($role);

    return $admin;
}

beforeEach(function () {
    Storage::fake('local');
});

test('admin can upload documents to a transaction', function () {
    $admin = transactionDocAdminUser();
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create(['member_id' => $member->id]);

    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this
        ->actingAs($admin)
        ->withHeaders(['Accept' => 'application/json'])
        ->post(route('admin.transactions.documents.store', $transaction), [
            'documents' => [
                [
                    'name' => 'Receipt',
                    'notes' => 'Payment receipt',
                    'file' => $file,
                ],
            ],
        ]);

    $response->assertOk();
    $response->assertJsonStructure(['message']);

    expect($transaction->documents()->count())->toBe(1);
    expect($transaction->documents()->first()->name)->toBe('Receipt');
    expect($transaction->documents()->first()->notes)->toBe('Payment receipt');

    Storage::disk('local')->assertExists($transaction->documents()->first()->file_path);
});

test('upload requires name and file for each document', function () {
    $admin = transactionDocAdminUser();
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create(['member_id' => $member->id]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.transactions.documents.store', $transaction), [
            'documents' => [
                [
                    'name' => '',
                    'notes' => '',
                    'file' => '',
                ],
            ],
        ]);

    $response->assertSessionHasErrors(['documents.0.name', 'documents.0.file']);
});

test('upload rejects files over 15MB', function () {
    $admin = transactionDocAdminUser();
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create(['member_id' => $member->id]);

    $file = UploadedFile::fake()->create('large.pdf', 15361);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.transactions.documents.store', $transaction), [
            'documents' => [
                [
                    'name' => 'Large File',
                    'notes' => '',
                    'file' => $file,
                ],
            ],
        ]);

    $response->assertSessionHasErrors(['documents.0.file']);
});

test('upload rejects disallowed file types', function () {
    $admin = transactionDocAdminUser();
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create(['member_id' => $member->id]);

    $file = UploadedFile::fake()->create('script.exe', 100);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.transactions.documents.store', $transaction), [
            'documents' => [
                [
                    'name' => 'Script',
                    'notes' => '',
                    'file' => $file,
                ],
            ],
        ]);

    $response->assertSessionHasErrors(['documents.0.file']);
});

test('admin can upload multiple documents at once', function () {
    $admin = transactionDocAdminUser();
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create(['member_id' => $member->id]);

    $response = $this
        ->actingAs($admin)
        ->withHeaders(['Accept' => 'application/json'])
        ->post(route('admin.transactions.documents.store', $transaction), [
            'documents' => [
                [
                    'name' => 'Doc 1',
                    'notes' => '',
                    'file' => UploadedFile::fake()->create('doc1.pdf', 100),
                ],
                [
                    'name' => 'Doc 2',
                    'notes' => 'Second document',
                    'file' => UploadedFile::fake()->create('doc2.jpg', 200),
                ],
            ],
        ]);

    $response->assertOk();
    expect($transaction->documents()->count())->toBe(2);
});

test('admin can download a transaction document', function () {
    $admin = transactionDocAdminUser();
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create(['member_id' => $member->id]);

    $file = UploadedFile::fake()->create('document.pdf', 100);
    $path = $file->store('transaction_documents');

    $document = $transaction->documents()->create([
        'name' => 'Test Doc',
        'notes' => null,
        'file_path' => $path,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.transaction-documents.download', $document));

    $response->assertOk();
    $response->assertDownload();
});

test('admin can delete a transaction document', function () {
    $admin = transactionDocAdminUser();
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create(['member_id' => $member->id]);

    $file = UploadedFile::fake()->create('document.pdf', 100);
    $path = $file->store('transaction_documents');

    $document = $transaction->documents()->create([
        'name' => 'Test Doc',
        'notes' => null,
        'file_path' => $path,
    ]);

    $response = $this
        ->actingAs($admin)
        ->delete(route('admin.transaction-documents.destroy', $document));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(TransactionDocument::count())->toBe(0);
    Storage::disk('local')->assertMissing($path);
});

test('documents are deleted when parent transaction is deleted', function () {
    $admin = transactionDocAdminUser();
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create(['member_id' => $member->id]);

    $file = UploadedFile::fake()->create('document.pdf', 100);
    $path = $file->store('transaction_documents');

    $transaction->documents()->create([
        'name' => 'Test Doc',
        'notes' => null,
        'file_path' => $path,
    ]);

    $transaction->delete();

    expect(TransactionDocument::count())->toBe(0);
});
