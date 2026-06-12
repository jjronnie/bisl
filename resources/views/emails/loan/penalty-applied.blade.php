<x-mail::message>
# Penalty Applied

Dear **{{ $firstName }}**,

A penalty has been applied to your loan installment due to non-payment by the due date.

## Penalty Details

<x-mail::table>
| Description | Amount |
| :--- | ---: |
| Loan Number | **{{ $loan->loan_number }}** |
| Installment # | {{ $installment->installment_number }} |
| Due Date | {{ $installment->due_date->format('d F Y') }} |
| Principal | UGX {{ number_format($installment->principal_amount, 2) }} |
| Interest | UGX {{ number_format($installment->interest_amount, 2) }} |
| **Penalty Applied** | **UGX {{ number_format($installment->penalty_amount, 2) }}** |
| **Total Outstanding** | **UGX {{ number_format($installment->principal_amount + $installment->interest_amount + $installment->penalty_amount, 2) }}** |
</x-mail::table>

Please make payment as soon as possible to avoid further charges.

<x-mail::button :url="route('member.loans')">
View Your Loans
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
