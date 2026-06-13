<x-mail::message>
# Penalty Applied

Dear **{{ $firstName }}**,

This email is to inform you that a penalty has been applied to your account.

## Penalty Details

<x-mail::table>
| Description | Details |
| :--- | :--- |
| **Reason** | {{ $label }} |
| **Amount** | UGX {{ number_format($penalty->amount, 2) }} |
@if($penalty->meeting_date)
| **Meeting Date** | {{ $penalty->meeting_date->format('d F Y') }} |
@endif
| **Balance Before** | UGX {{ number_format($penalty->balance_before, 2) }} |
| **Balance After** | UGX {{ number_format($penalty->balance_after, 2) }} |
</x-mail::table>

@if($penalty->notes)
_Notes: {{ $penalty->notes }}_
@endif

<br>

Thanks for saving with **{{ config('app.name') }}**.

<x-mail::button :url="route('login')">
Sign In To View Details
</x-mail::button>

<hr>

<x-slot:subcopy>
<small>
If you have any questions, please contact our support team.

This is an automated message, please do not reply directly to this email.
</small>
</x-slot:subcopy>
</x-mail::message>
