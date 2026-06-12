<x-mail::message>
# Salary Dispatched

Dear {{ $member->name }},

Your salary for **{{ $monthName }} {{ $period->year }}** has been dispatched to your salary account.

## Salary Summary

| Item | Amount |
|------|-------:|
| Gross Salary | UGX {{ number_format($run->gross_salary) }} |
| Total Deductions | UGX {{ number_format($run->total_deductions) }} |
| Net Salary | UGX {{ number_format($run->net_salary) }} |
| Savings Contribution | UGX {{ number_format($run->savings_contribution) }} |
@if ($run->advance_amount > 0)
| Advance Repayment | UGX {{ number_format($run->advance_amount) }} |
@endif
| **Final Take Home** | **UGX {{ number_format($run->final_take_home) }}** |

Your savings contribution of **UGX {{ number_format($run->savings_contribution) }}** has also been deposited into your savings account.

You can view your transaction history and salary balance by logging into your account.

Thank you.

<x-mail::button :url="route('member.dashboard')">
View Dashboard
</x-mail::button>

Regards,<br>
{{ config('app.name') }}
</x-mail::message>
