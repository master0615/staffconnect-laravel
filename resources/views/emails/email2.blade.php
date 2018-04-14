@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level == 'error')
# Whoops!
@else
# Hello!
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}
@endforeach

{{-- Action Buttons --}}
@foreach($actionButtons as $actionButton)
@isset($actionButton['actionText'])
@component('mail::button', ['url' => ($actionButton['actionUrl']), 'color' => ($actionButton['actionColor'])])
{{ ($actionButton['actionText']) }}
@endcomponent
@endisset
@endforeach

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Regards,
<br>
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@component('mail::subcopy')
blah blah unsubscribe text here
@endcomponent 
@endcomponent
