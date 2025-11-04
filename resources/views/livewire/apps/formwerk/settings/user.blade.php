<?php

use function Livewire\Volt\{title};

title('Formwerk - Meine Einstellungen');

?>

<x-intranet-app-formwerk::formwerk-layout heading="Meine Einstellungen" subheading="Persönliche Einstellungen für die Formwerk App">
    @livewire('intranet-app-base::user-settings', ['appIdentifier' => 'formwerk'])
</x-intranet-app-formwerk::formwerk-layout>
