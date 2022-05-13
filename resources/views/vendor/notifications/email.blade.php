@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
<p style="color: #292d34; font-weight: 500">Bonjour !</p>
@endif
@endif

{{-- Intro Lines --}}
{{ "Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse e-mail." }}

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ "Vérifier l'adresse e-mail" }}
@endcomponent
@endisset

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Salutations'),<br>
 <p style="color: #292d34; font-weight: 500">{{ config('app.name') }}</p>
@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
@lang(
    "Si vous ne parvenez pas à cliquer sur le bouton Vérifier l'adresse e-mail, copiez et collez l'URL ci-dessous dans votre navigateur Web : ",
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
@endslot
@endisset
@endcomponent
