@component('mail::message')
# {{ $title }}

{{ $message }}

@component('mail::button', ['url' => url('/dashboard')])
View Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
