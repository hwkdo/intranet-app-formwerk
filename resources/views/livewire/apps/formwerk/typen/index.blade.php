<?php

use Hwkdo\IntranetAppFormwerk\Models\Typ;
use function Livewire\Volt\{state, title, usesPagination, computed};

usesPagination();

state([
    'filter' => '',
]);

$typen = computed(function () {
    return Typ::query()
        ->when($this->filter, fn($q) => $q->where('name', 'like', '%' . $this->filter . '%'))
        ->orderBy('name')
        ->paginate(10);
});

title('Formwerk - Typen');

?>

<x-intranet-app-formwerk::formwerk-layout heading="Typen" subheading="Formwerk-Typen verwalten">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:input 
                wire:model.live.debounce.300ms="filter" 
                placeholder="Nach Name suchen..." 
                icon="magnifying-glass"
                class="w-64"
            />
            @can('manage-app-formwerk')
                <flux:button :href="route('apps.formwerk.typen.create')" icon="plus" variant="primary">
                    Neuer Typ
                </flux:button>
            @endcan
        </div>

        <flux:table :paginate="$this->typen">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Form Identifier</flux:table.column>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column>Job Class</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Aktionen</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->typen as $typ)
                    <flux:table.row :key="'typ-'.$typ->id">
                        <flux:table.cell>{{ $typ->name }}</flux:table.cell>
                        <flux:table.cell>{{ $typ->form_identifier }}</flux:table.cell>
                        <flux:table.cell>{{ $typ->subject ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="text-xs font-mono">{{ $typ->jobClass ? class_basename($typ->jobClass) : '-' }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($typ->is_active)
                                <flux:badge color="green" size="sm">Aktiv</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Inaktiv</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button 
                                :href="route('apps.formwerk.typen.edit', $typ)" 
                                size="xs" 
                                icon="pencil-square"
                                variant="ghost"
                            >
                                Bearbeiten
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</x-intranet-app-formwerk::formwerk-layout>

