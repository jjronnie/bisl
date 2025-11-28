@component('mail::message')
# Payment Confirmation

Dear **{{ $loan->member->user->name ?? 'Member' }}**,

We have successfully received a payment towards your loan.

| Detail | Value |
| :--- | :--- |
| **Loan ID** | #{{ $loan->loan_number }} |
| **Amount Paid** | **UGX {{ number_format($amountPaid, 2) }}** |
| **Date** | {{ now()->format('F d, Y') }} |

---


For a detailed view of your updated statement, repayment history, and remaining balance, please log in to your member portal.

@component('mail::button', ['url' => url('/login')])
Log In to View Full Details
@endcomponent

Thank you for your  payment.

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent