<x-mail::message>
# Dear {{ $name }}, Welcome to {{ config('app.name') }}

This email informs you that you have been registered as a member.



<x-mail::panel>

Please use the email  {{ $email }} as **Username** and the  **One Time Password** below to sign in to your Account:

</x-mail::panel>

{{-- Custom style for the OTP box using Markdown components --}}
<div style="text-align: center; margin: 30px 0;">
    <div style="
        display: inline-block;
        font-size: 28px;
        font-weight: bold;
        letter-spacing: 10px;
        padding: 15px 30px;
        border: 2px solid #3b82f6; /* Blue border for visibility */
        border-radius: 8px;
        background-color: #eff6ff; /* Light blue background */
        color: #1e40af; /* Dark blue text */
        text-align: center;
        user-select: all; /* Allows easy copying */
    ">
        {{ $password }}
    </div>
</div>

<x-mail::button :url="route('login')">
Sign In Now
</x-mail::button>

<x-mail::subcopy>

For the safety of your account, you will be immediately **prompted to change this temporary OTP** to a new, strong password upon successful sign-in. This is a crucial step to secure your membership account.
</x-mail::subcopy>

If you have any issues signing in, please contact the system administrator.

Best Regards,<br>
The {{ config('app.name') }} Team
</x-mail::message>