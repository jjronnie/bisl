<x-mail::message>
# Welcome, {{ $name }}!

Your account has been successfully created. For security, you will be prompted to change this password upon your first login.

<x-mail::panel>
**Email:** {{ $email }}
**Temporary Password:** {{ $password }}
</x-mail::panel>

<x-mail::button :url="route('login')">
Login Now
</x-mail::button>

If you have any issues, please contact the system administrator.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>