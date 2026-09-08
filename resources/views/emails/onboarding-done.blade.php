<x-mail::message>
# Onboarding abgeschlossen

**{{ $name }}**@if($username) ({{ $username }})@endif hat das Onboarding abgeschlossen.

@if($pdfUrl)
<x-mail::button :url="$pdfUrl">
Zum Onboarding-Dokument
</x-mail::button>
@endif

Vielen Dank,<br>
{{ config('app.name') }}
</x-mail::message>
