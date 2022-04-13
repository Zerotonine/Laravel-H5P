<div class="container-fluid w-2/3 m-auto">
    @if(!$showAddPackages)
    @if($showCreateModal)
        @livewire('h5p-bundle-create-modal')
    @endif

    <div class="my-3">
        <button wire:click="$toggle('showCreateModal')" class="bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 font-bold rounded">New Bundle</button>
        <input wire:model="search" placeholder="Search..." class="p-1 my-1 rounded border border-gray-700">
    </div>

    {{-- CONTENT DELETION MESSAGES --}}
    {{-- @if (session()->has('h5p.deleted.complete'))
        @livewire('h5p-flash', ['title' => "Erfolg!", 'message' => session('h5p.deleted.complete')])
    @elseif (session()->has('h5p.deleted.error'))
        @livewire('h5p-flash', ['title' => 'Fehler!', 'message' => session('h5p.deleted.error'), 'type' => 'negative'])
    @endif --}}

    {{-- CONTENT CREATION/EDITING MESSAGES --}}
    {{-- @if(session()->has('success'))
        @livewire('h5p-flash', ['title' => 'Erfolg!', 'message' => session('success')])
    @elseif(session()->has('fail'))
        @livewire('h5p-flash', ['title' => 'Fehler!', 'message' => session('fail'), 'type' => 'negative'])
    @endif --}}

    <div class="row">

        <div class="col-md-12">
            <table class="h5p-lists min-w-full border-collapse block md:table">
                <colgroup>
                    <col width="10%">
                    <col width="15%">
                    <col width="*">
                    <col width="5%">
                    <col width="10%">
                    <col width="15%">
                </colgroup>

                <thead class="block md:table-header-group">
                    <tr class="border border-grey-500 md:border-none block md:table-row absolute -top-full md:top-auto -left-full md:left-auto md:relative">
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-center block md:table-cell">#</th>
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-left block md:table-cell">{{ trans('laravel-h5p.content.creator') }}</th>
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-left block md:table-cell">{{ trans('laravel-h5p.content.title') }}</th>
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-left block md:table-cell">No. of Contents</th>
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-left block md:table-cell">{{ trans('laravel-h5p.content.created_at') }}</th>
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-left block md:table-cell">{{ trans('laravel-h5p.content.action') }}</th>
                    </tr>
                </thead>

                <tbody x-init class="block md:table-row-group">

                    @unless(count($this->entries) > 0)
                    <tr class="bg-gray-300 border border-grey-500 md:border-none block md:table-row">
                        <td colspan="5" class="p-2 md:border md:border-grey-500 text-center font-bold">{{ trans('laravel-h5p.common.no-result') }}</td>
                    </tr>
                    @endunless

                    @foreach($this->entries as $n => $entry)
                    <tr class="{{($n+1)%2===0 ? 'bg-gray-300' : 'bg-white-500'}} border border-grey-500 md:border-none block md:table-row">

                        <td class="p-2 md:border md:border-grey-500 text-center block md:table-cell">
                            {{ $entry->id }}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                            {{$entry->get_user()->name}}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                            {{-- {{$entry->title}} --}}
                            {{-- <a @click="window.open('/bundle?b={{$entry->id}}', menubar=no, toolbar=no, location=no, status=no)" href="/bundle?b={{$entry->id}}" class="underline">{{$entry->title}}</a> --}}
                            <button @click="open('/bundle?b={{$entry->id}}', '_blank' , 'menubar=false, toolbar=false, location=false, status=false')" class="bg-transparent underline">{{$entry->title}}</button>
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                            {{$entry->get_content_count()}}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                            {{ $entry->updated_at->format('Y.m.d') }}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-center block md:table-cell">
                            <button wire:click="showAddPackages({{$entry->id}}, '{{$entry->title}}')" class="font-bold text-white py-2 px-4 rounded bg-blue-500 hover:bg-blue-700">{{ trans('laravel-h5p.content.edit') }}</button>
                            {{-- //TODO: make delete working --}}
                            <button disabled wire:click="showModal({{$entry->id}}, '{{$entry->title}}')" class="hover:bg-red-500 hover:text-white border border-red-500 font-semibold py-2 px-2 rounded">{{ trans('laravel-h5p.content.destroy') }}</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>


    <div class="row">
        <div class="col-md-12 mt-2">
            {{$this->entries->links()}}
        </div>
    </div>
    @else
    <div class="my-3">
        <button wire:click="closeAddPackages" class="bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 font-bold rounded">Zurück</button>
    </div>
    <h1 class="bg-gray-500 p-2 text-white font-bold">Füge Inhaltspakete dem Bundle <span class="italic font-semibold">{{$bundleId}}-{{$bundleTitle}}</span> hinzu.</h1>

    <div class="my-2 grid grid-cols-[minmax(0,_1fr)_minmax(0,_1fr)] gap-2 box-border">
        <div class="bg-red-200 w-full p-2 overflow-auto h-[500px] max-h-[500px]">
            @foreach($this->contents as $n => $content)
                <p wire:click="addToBundle({{$content->id}})" class="hover:bg-red-400 cursor-pointer">{{$content->title}}</p>
            @endforeach
        </div>

        {{-- <div class="bg-green-200 w-full max-h-[5rem]">
            <h2>grid 2</h2>
        </div> --}}

        <div class="bg-blue-200 w-full overflow-auto h-[500px] max-h-[500px]">
            @foreach($this->bundles as $bundle)
                @foreach($bundle->contents as $content)
                    <p wire:click="removeFromBundle({{$content->id}})" class="hover:bg-blue-400 cursor-pointer">{{$content->title}}</p>
                @endforeach
            @endforeach
        </div>
    </div>
    @endif
</div>

