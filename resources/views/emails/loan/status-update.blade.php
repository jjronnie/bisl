@component('mail::message')
# Loan Status Update

Dear **{{ $loan->member->user->name ?? $loan->member->email }}**,

This Email is  to inform you of an update regarding your loan application with Loan Number **{{ $loan->loan_number }}** for the amount of **UGX{{ number_format($loan->amount, 0) }}**.

The status of your application is now: **{{ ucfirst($loan->status) }}**.

@if ($loan->status === 'approved')
@component('mail::panel')
**Your loan has been APPROVED!**
The next step is **Disbursement**. The funds will be transferred shortly.
@endcomponent
@elseif ($loan->status === 'rejected')
@component('mail::panel')
**Your loan has been REJECTED.**
We regret to inform you that your application did not meet our current criteria. Please **contact an administrator** for more details regarding the reason for rejection.
@endcomponent
@elseif ($loan->status === 'disbursed')
@component('mail::panel')
**Your loan has been DISBURSED!**
The funds have been transferred to your account. Please login to Your account to view your repayment schedule.
@endcomponent
@else
Thank you for Saving with {{ config('app.name') }} . We will notify you of any further changes.
@endif

@component('mail::button', ['url' => route('member.loans')])
View Loan Details
@endcomponent



Thanks,<br>
{{ config('app.name') }}
@endcomponent