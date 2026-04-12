<x-mail::message>
# {{ $reminder->getSubject() }}

Dear **{{ $loan->member->user->name ?? $loan->member->name }}**,

@if($reminder->reminderType === 'due_today')
    <strong>Your loan installment is DUE TODAY.</strong>

    Please ensure you make the payment to avoid penalties.
@else
    <strong>Reminder: Your loan installment will be due in 7 days.</strong>

    We recommend making payment early to avoid any issues.
@endif

## Payment Details

<x-mail::table>
| Description | Amount |
| :--- | ---: |
| Principal | UGX {{ number_format($installment->principal_amount, 2) }} |
| Interest | UGX {{ number_format($installment->interest_amount, 2) }} |
| Penalty | UGX {{ number_format($installment->penalty_amount, 2) }} |
| **Total Due** | **UGX {{ number_format($installment->principal_amount + $installment->interest_amount + $installment->penalty_amount, 2) }}** |
</x-mail::table>

**Loan Number:** {{ $loan->loan_number }}
**Due Date:** {{ $installment->due_date->format('d F Y') }}



Please contact us if you have any questions regarding this payment.

Thanks,
{{ config('app.name') }}
</x-mail::message>
