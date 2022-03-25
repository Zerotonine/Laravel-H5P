<div class="container-fluid">
    {{-- DELETION MODAL --}}
    @if($showDeletionModal)
        <x-lh5p::h5p-deletion-modal>
            <x-slot name="title">
                Bibliothek löschen
            </x-slot>

            <x-slot name="value">
                Sind Sie sicher das Sie diese Bibliothek unwiderruflich löschen wollen?
            </x-slot>

            <x-slot name="footer">
                <button wire:click="delete" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Löschen</button>
                <button wire:click="$toggle('showDeletionModal')" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Abbrechen</button>
            </x-slot>
        </x-lh5p::h5p-deletion-modal>
    @endif

    {{-- RESTRICTED FLASH --}}
    @if(session()->has('h5p.restricted.success'))
        @livewire('h5p-flash', ['title' => 'Erfolg!', 'message' => session('h5p.restricted.success')])
    @elseif(session()->has('h5p.restricted.error'))
        @livewire('h5p-flash', ['title' => 'Fehler!', 'message' => session('h5p.restricted.error'), 'type' => 'negative'])
    @endif

    {{-- LIB DELETE FLASH --}}
    @if(session()->has('h5p.delete.success'))
        @livewire('h5p-flash', ['title' => 'Erfolg!', 'message' => session('h5p.delete.success')])
    @elseif(session()->has('h5p.delete.error'))
        @livewire('h5p-flash', ['title' => 'Fehler!', 'message' => session('h5p.delete.error'), 'type' => 'negative'])
    @endif

    <div>
        <table class="h5p-lists min-w-full border-collapse block md:table mt-6">
            <colgroup>
                <col width="*" />
                <col width="8%"/>
                <col width="8%"/>
                <col width="8%"/>
                <col width="10%"/>
                <col width="10%"/>
                <col width="15%"/>
            </colgroup>

            <thead class="block md:table-header-group">
                <tr class="border border-grey-500 md:border-none block md:table-row absolute -top-full md:top-auto -left-full md:left-auto md:relative">
                    <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-left block md:table-cell">{{ trans('laravel-h5p.library.name') }}</th>
                    <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-center block md:table-cell">{{ trans('laravel-h5p.library.version') }}</th>
                    <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-center block md:table-cell">{{ trans('laravel-h5p.library.restricted') }}</th>
                    <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-center block md:table-cell">{{ trans('laravel-h5p.library.contents') }}</th>
                    <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-center block md:table-cell">{{ trans('laravel-h5p.library.contents_using_it') }}</th>
                    <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-center block md:table-cell">{{ trans('laravel-h5p.library.libraries_using_it') }}</th>
                    <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-center block md:table-cell">{{ trans('laravel-h5p.library.actions') }}</th>
                </tr>
            </thead>

            <tbody class="block md:table-row-group">
                @unless(count($this->entries) > 0)
                    <tr class="bg-gray-300 border border-grey-500 md:border-none block md:table-row">
                        <td colspan="7" class="p-2 md:border md-border-grey-500 text-center block md:table-cell">
                            {{ trans('laravel-h5p.common.no-result') }}
                        </td>
                    </tr>
                @endunless

                @foreach ($this->entries as $n => $entry)
                    <tr class="{{($n+1)%2===0 ? 'bg-gray-300' : 'bg-white-500'}} border border-grey-500 md:border-none block md:table-row">
                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                            {{$entry->title}}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-center block md:table-cell">
                            {{$entry->major_version.'.'.$entry->minor_version.'.'.$entry->patch_version}}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-center block md:table-cell">
                            <input wire:click="restricted({{$entry->id}})" class="hover:cursor-pointer p-2" type="checkbox" value="{{$entry->restricted}}" {{$entry->restricted ? 'checked' : ''}} />
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-center block md:table-cell">
                            {{number_format($entry->numContent())}}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-center block md:table-cell">
                            {{number_format($entry->getCountContentDependencies())}}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-center block md:table-cell">
                            {{number_format($entry->getCountLibraryDependencies())}}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-center block md:table-cell">
                            <button wire:click="showDeletionModal({{$entry->id}})" class="border border-red-500 p-2 font-semibold rounded hover:bg-red-500 hover:text-white">{{ trans('laravel-h5p.library.remove') }}</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{$this->entries->links('pagination::tailwind')}}
    </div>



<script>
    document.addEventListener('livewire:load', () => {
        livewire.on('removeFlash', (e) => {
            document.querySelector('#flashMessage').remove();
        });
    });
</script>
</div>

