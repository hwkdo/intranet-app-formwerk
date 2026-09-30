<?php

use Flux\Flux;
use Hwkdo\IntranetAppFormwerk\Enums\DatenabrufType;
use Hwkdo\IntranetAppFormwerk\Models\Datenabruf;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app')] #[Title('Formwerk – Datenabrufe')] class extends Component {
    public ?int $editingId = null;

    public string $name = '';

    public string $type = '';

    public string $key = '';

    public bool $is_active = true;

    public bool $showForm = false;

    #[Computed]
    public function datenabrufe(): \Illuminate\Database\Eloquent\Collection
    {
        return Datenabruf::query()
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function typeOptions(): array
    {
        return collect(DatenabrufType::cases())
            ->mapWithKeys(fn (DatenabrufType $type) => [$type->value => $type->label()])
            ->all();
    }

    public function maskKey(?string $key): string
    {
        if ($key === null || $key === '') {
            return '—';
        }

        if (strlen($key) <= 8) {
            return str_repeat('•', strlen($key));
        }

        return substr($key, 0, 4).str_repeat('•', max(4, strlen($key) - 8)).substr($key, -4);
    }

    public function create(): void
    {
        $this->authorize('manage-app-formwerk');
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('manage-app-formwerk');

        $datenabruf = Datenabruf::findOrFail($id);
        $this->editingId = $id;
        $this->name = $datenabruf->name;
        $this->type = $datenabruf->type->value;
        $this->key = $datenabruf->key ?? '';
        $this->is_active = $datenabruf->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->authorize('manage-app-formwerk');

        $keyRules = ['nullable', 'string', 'max:255'];

        if (filled($this->key)) {
            $keyRules[] = Rule::unique('intranet_app_formwerk_datenabrufe', 'key')
                ->where(fn ($query) => $query->where('type', $this->type))
                ->ignore($this->editingId);
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(DatenabrufType::class)],
            'key' => $keyRules,
            'is_active' => ['boolean'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'key' => filled($validated['key'] ?? null) ? $validated['key'] : null,
            'is_active' => $validated['is_active'],
        ];

        if ($this->editingId) {
            Datenabruf::findOrFail($this->editingId)->update($payload);
            Flux::toast('Datenabruf wurde aktualisiert.', variant: 'success');
        } else {
            Datenabruf::create($payload);
            Flux::toast('Datenabruf wurde erstellt.', variant: 'success');
        }

        $this->resetForm();
        unset($this->datenabrufe);
    }

    public function delete(int $id): void
    {
        $this->authorize('manage-app-formwerk');

        Datenabruf::findOrFail($id)->delete();
        Flux::toast('Datenabruf wurde gelöscht.', variant: 'success');
        unset($this->datenabrufe);
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->type = '';
        $this->key = '';
        $this->is_active = true;
        $this->showForm = false;
        $this->resetValidation();
    }
}; ?>

<div>
<x-intranet-app-formwerk::formwerk-layout heading="Datenabrufe" subheading="Externe Formwerk-Datenabrufe verwalten">
    <div class="space-y-4">
        @if($showForm)
            <flux:card class="space-y-4 glass-card">
                <flux:heading size="lg">
                    {{ $editingId ? 'Datenabruf bearbeiten' : 'Neuer Datenabruf' }}
                </flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>Name <flux:badge size="sm" color="red">Pflicht</flux:badge></flux:label>
                        <flux:input wire:model="name" placeholder="z.B. HWR Gewerke" autofocus />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Typ <flux:badge size="sm" color="red">Pflicht</flux:badge></flux:label>
                        <flux:select wire:model="type" placeholder="Typ wählen…">
                            @foreach($this->typeOptions as $value => $label)
                                <flux:select.option :value="$value">{{ $label }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Key (optional)</flux:label>
                        <flux:input wire:model="key" placeholder="Nur setzen, wenn Formwerk einen Key mitschickt" />
                        <flux:description>Leer = Abruf ohne Key (öffentlich für diesen Typ).</flux:description>
                        <flux:error name="key" />
                    </flux:field>

                    <flux:field>
                        <flux:checkbox wire:model="is_active" label="Aktiv" />
                    </flux:field>

                    <div class="flex gap-3">
                        <flux:button type="submit" variant="primary" size="sm">
                            {{ $editingId ? 'Speichern' : 'Erstellen' }}
                        </flux:button>
                        <flux:button wire:click="cancel" variant="ghost" size="sm" type="button">Abbrechen</flux:button>
                    </div>
                </form>
            </flux:card>
        @else
            <div class="flex justify-end">
                @can('manage-app-formwerk')
                    <flux:button wire:click="create" variant="primary" icon="plus" size="sm">
                        Neuer Datenabruf
                    </flux:button>
                @endcan
            </div>
        @endif

        <flux:card class="glass-card">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Typ</flux:table.column>
                    <flux:table.column>URL (Formwerk)</flux:table.column>
                    <flux:table.column>Key</flux:table.column>
                    <flux:table.column>Abrufe</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($this->datenabrufe as $datenabruf)
                        <flux:table.row wire:key="datenabruf-{{ $datenabruf->id }}">
                            <flux:table.cell class="font-medium">{{ $datenabruf->name }}</flux:table.cell>
                            <flux:table.cell>{{ $datenabruf->type->label() }}</flux:table.cell>
                            <flux:table.cell>
                                <code class="block max-w-md truncate text-xs break-all whitespace-normal" title="{{ $datenabruf->type->formwerkUrl() }}">
                                    {{ $datenabruf->type->formwerkUrl() }}
                                </code>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="font-mono text-xs">{{ $this->maskKey($datenabruf->key) }}</span>
                            </flux:table.cell>
                            <flux:table.cell>{{ number_format($datenabruf->call_count, 0, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>
                                @if($datenabruf->is_active)
                                    <flux:badge color="green" size="sm">Aktiv</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Inaktiv</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @can('manage-app-formwerk')
                                    <div class="flex gap-1">
                                        <flux:button wire:click="edit({{ $datenabruf->id }})" variant="ghost" size="sm" icon="pencil" />
                                        <flux:button
                                            wire:click="delete({{ $datenabruf->id }})"
                                            wire:confirm="Datenabruf '{{ $datenabruf->name }}' wirklich löschen?"
                                            variant="ghost"
                                            size="sm"
                                            icon="trash"
                                            class="text-red-500 hover:text-red-700"
                                        />
                                    </div>
                                @endcan
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center text-zinc-500 py-6">
                                Noch keine Datenabrufe vorhanden.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</x-intranet-app-formwerk::formwerk-layout>
</div>
