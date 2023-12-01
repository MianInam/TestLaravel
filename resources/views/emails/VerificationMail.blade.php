<x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="route('verification.verify', ['code' => $data['remember_token']])">
    Verify your email
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
