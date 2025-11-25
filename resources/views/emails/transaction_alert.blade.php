<x-mail::message>
{{-- Salutation --}}
# Transaction Notification

Dear **{{ $transaction->member->user->name ?? 'Valued Member' }}**,

{{-- Logic for Debit vs Credit Wording --}}
@if($transaction->transaction_type === 'deposit')
This email is to inform you that Your account has been **credited** with UGX**{{ number_format($transaction->amount, 2) }}**.
@else
This email is to inform you that Your account has been **debited** with **{{ number_format($transaction->amount, 2) }}**.
@endif

Please find the details of this transaction below:

{{-- Transaction Details Table --}}
<x-mail::table>
| Description | Details |
| :--- | :--- |
| **Transaction Date** | {{ $transaction->created_at->format('d M Y, h:i A') }} |
| **Reference No** | {{ $transaction->reference_number }} |
| **Transaction Type** | {{ ucfirst($transaction->transaction_type) }} |
| **Amount** | {{ number_format($transaction->amount, 2) }} |
| **Available Balance** | **{{ number_format($transaction->balance_after, 2) }}** |
</x-mail::table>

{{-- Optional Remarks Section --}}
@if($transaction->remarks)
_Remarks: {{ $transaction->remarks }}_
@endif

<br>

Thanks for saving with **{{ config('app.name') }}**.

<br>

<x-mail::button :url="route('login')">
Sign In To  View Details
</x-mail::button>

<hr>

{{-- Official Banking Footer / Disclaimer --}}
<x-slot:subcopy>
<small>
Security Tip: {{ config('app.name') }} will never ask you for your password or PIN via email. If you did not authorize this transaction, please contact our support team immediately.

This is an automated message, please do not reply directly to this email.
</small>
</x-slot:subcopy>

</x-mail::message>