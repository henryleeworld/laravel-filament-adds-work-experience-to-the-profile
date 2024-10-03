<x-dynamic-component
    :component="static::isSimple() ? 'filament-panels::page.simple' : 'filament-panels::page'"
>
    <x-filament-panels::form id="form" wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <div class="space-y-2">
        <div class="flex justify-end">
		    {{ $this->createAction }}
        </div>
        @foreach($experiences as $experience)
            <x-filament::section :compact="true">
                <div class="flex justify-between">
                    <div>
                        <h3 class="font-medium">{{ $experience->company_name }}</h3>
                        <div class="text-sm text-gray-500">{{ $experience->job_title }}</div>
                        <div class="text-sm text-gray-500">
						{{ $experience->start_date->format('Y-m-d') }} &bullet;
                        @if ($experience->end_date)
                            {{ $experience->end_date->format('Y-m-d') }}
                        @else
                            Now
                        @endif
                        </div>
                        <p>{{ $experience->job_description }}</p>
                    </div>
                    <div>
                        <x-filament-actions::group
                            :actions="[
                                ($this->editAction)(['experience' => $experience->id]),
                                ($this->deleteAction)(['experience' => $experience->id]),
                            ]"
                            dropdown-placement="botton-end"
                        />
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-dynamic-component>