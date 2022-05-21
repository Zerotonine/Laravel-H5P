<div class="container-fluid w-2/3 m-auto">
    @if(!$showAddPackages)
    @if($showCreateModal)
        @livewire('h5p-bundle-create-modal')
    @endif

    @if($showDeletionModal)
        <x-lh5p::h5p-deletion-modal>
            <x-slot name="title">
                Inhalt löschen
            </x-slot>

            <x-slot name="value">
                Sind Sie sicher das Sie dieses Paket unwiderruflich löschen wollen?
            </x-slot>

            <x-slot name="footer">
                <button wire:click="deleteBundle" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Löschen</button>
            <button wire:click="$toggle('showDeletionModal')" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Abbrechen</button>
            </x-slot>
        </x-lh5p::h5p-deletion-modal>
    @endif

    <div class="my-3">
        <button wire:click="$toggle('showCreateModal')" class="bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 font-bold rounded">New Bundle</button>
        <input wire:model="search" placeholder="Search..." class="p-1 my-1 rounded border border-gray-700">
    </div>

    {{-- CONTENT DELETION MESSAGES --}}
    @if (session()->has('h5p.deleted.complete'))
        @livewire('h5p-flash', ['title' => "Erfolg!", 'message' => session('h5p.deleted.complete')])
    @elseif (session()->has('h5p.deleted.error'))
        @livewire('h5p-flash', ['title' => 'Fehler!', 'message' => session('h5p.deleted.error'), 'type' => 'negative'])
    @endif

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
                            <button wire:click="showDeletionModal({{$entry->id}})" class="hover:bg-red-500 hover:text-white border border-red-500 font-semibold py-2 px-2 rounded">{{ trans('laravel-h5p.content.destroy') }}</button>
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
    {{-- SHOW PACKAGE DETAILS //TODO: extract in extra component --}}
    <div class="my-3">
        <button wire:click="closeAddPackages" class="bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 font-bold rounded">Zurück</button>
    </div>

    <div x-data class="my-5">
        <details {{$this->getSettingsState()}}>
            <summary @click="$store.accordions.toggleSettings()" class="bg-gray-500 p-2 text-white font-bold cursor-pointer select-none">
                Einstellungen
            </summary>

            {{-- TAB START --}}

            <div x-data="{tab: 'background'}" class="mt-2">
                <div class="flex w-full">
                    <button @click="tab = 'background'" :class="{'active font-bold bg-gray-300': tab === 'background'}" class="p-3 bg-gray-200 focus:outline-none focus:bg-gray-300 hover:bg-gray-300 ">
                        Hintergrund
                    </button>
                    <button @click="tab = 'watermark'" :class="{'active font-bold bg-gray-300': tab === 'watermark'}" class="p-3 bg-gray-200 focus:outline-none focus:bg-gray-300 hover:bg-gray-300 ">
                        Wasserzeichen
                    </button>
                </div>

                <div x-show="tab === 'background'" id="background" class="bg-gray-300 p-2 grid grid-cols-3">
                    <div class="my-2 bg-gray-300 min-h-[300px] flex justify-center items-center flex-col border border-gray-400 rounded">
                        <form class="my-2 p-2 flex justify-center flex-col" wire:submit.prevent="save">
                            <input id="b-{{$this->background_id}}" class="cursor-pointer p-2 rounded" type="file" wire:model="background" />
                            <br/>
                            @error('background') <span class="p-2 text-red-500">{{$message}}</span> @enderror
                            <br/>
                            <button class="m-2 bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 font-bold rounded" type="submit">Speichern</button>
                        </form>
                    </div>
                    <div class="bg-gray-300 p-2">
                        <figure class="my-2 flex justify-center items-center flex-col min-h-[300px] min-w-[300px] border border-gray-400 rounded">
                            <div wire:loading wire:target="background">
                                <div style="border-top-color:transparent"
                                    class="w-24 h-24 border-4 border-blue-400 border-double rounded-full animate-spin"></div>
                            </div>
                            @if($this->background)
                                <div class="flex justify-center items-center flex-col" wire:loading.remove wire:target="background">
                                    <img class="max-w-[300px] max-h-[300px]" src="{{$this->background->temporaryUrl()}}" alt="Hintergrundbild Vorschau" />
                                    <figcaption>
                                        Hintergrundbild Vorschau
                                    </figcaption>
                                </div>
                            @else
                                <figcaption wire:loading.remove wire:target="background">
                                    Bitte Bild auswählen um Vorschau anzuzeigen
                                </figcaption>
                            @endif
                        </figure>
                    </div>
                    <div class="bg-gray-300 p-2">
                        <figure class="my-2 flex justify-center items-center flex-col min-w-[300px] min-h-[300px] border border-gray-400 rounded">
                            @if($this->background_path)
                                <img class="max-w-[300px] max-h-[300px]" src="{{$this->background_path}}" alt="Aktuelles Hintergrundbild" />
                                <figcaption>
                                    Aktuelles Hintergrundbild
                                </figcaption>
                            @else
                                <figcaption>
                                    Kein Hintergrundbild vorhanden
                                </figcaption>
                            @endif
                        </figure>
                    </div>
                </div>

                <div x-show="tab === 'watermark'" id="watermark" class="bg-gray-300 p-2 grid grid-cols-3">
                    <div x-data="{opacity: @entangle('watermark_opacity')}" class="min-h-[300px] flex justify-center items-center flex-col">
                        <form wire:submit.prevent="saveWatermark" class="my-2 p-2 border border-gray-400 rounded min-h-[300px] flex justify-center flex-col">
                            <input id="w-{{$this->watermark_id}}" class="cursor-pointer p-2 rounded" type="file" wire:model="watermark" />
                            <br />
                            @error('watermark') <span class="text-red-500">{{$message}}</span> @enderror
                            <br/>
                            <input x-model="opacity" class="w-full" id="opacity" type="range" min="0" max="100" step="10" />
                            <p>Sichtbarkeit: <span x-text="opacity"></span>%</p>
                            <br/>
                            <button class="m-2 bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 font-bold rounded" type="submit">Speichern</button>
                        </form>
                    </div>
                    <div class="p-2">
                        <figure class="my-2 flex justify-center items-center flex-col min-w-[300px] min-h-[300px] border border-gray-400 rounded">
                            <div wire:loading wire:target="watermark">
                                <div style="border-top-color:transparent"
                                    class="w-24 h-24 border-4 border-blue-400 border-double rounded-full animate-spin"></div>
                            </div>
                            @if($this->watermark)
                                <div class="flex justify-center items-center flex-col" wire:loading.remove wire:target="watermark">
                                    <img class="max-w-[300px] max-h-[300px]" style="opacity: {{$this->watermark_opacity > 0 ? $this->watermark_opacity / 100 : 0}} ;" src="{{$this->watermark->temporaryUrl()}}" alt="Wasserzeichen Vorschau" />
                                    <figcaption>
                                        Wasserzeichen Vorschau
                                    </figcaption>
                                </div>
                            @else
                                <figcaption wire:loading.remove wire:target="watermark">
                                    Bitte Bild auswählen um Vorschau anzuzeigen
                                </figcaption>
                            @endif
                        </figure>
                    </div>
                    <div class="p-2">
                        <figure class="my-2 flex justify-center items-center flex-col min-w-[300px] min-h-[300px] border border-gray-400 rounded">
                            @if($this->watermark_path)
                                <img class="max-w-[300px] max-h-[300px]" style="opacity: {{$this->watermark_opacity > 0 ? $this->watermark_opacity / 100 : 0}} ;" src="{{$this->watermark_path}}" alt="Aktuelles Wasserzeichen" />
                                <figcaption>
                                    Aktuelles Wasserzeichen
                                </figcaption>
                            @else
                                <figcaption>
                                    Kein Wasserzeichen vorhanden
                                </figcaption>
                            @endif
                        </figure>
                    </div>
                </div>
            </div>

            {{-- TAB END --}}
        </details>
    </div>

    <details {{$this->getBundleContentState()}}>
        <summary @click="$store.accordions.toggleBundleContent()" class="bg-gray-500 p-2 text-white font-bold cursor-pointer select-none">
            Füge Inhaltspakete dem Bundle <span class="italic font-semibold">{{$bundleId}}-{{$bundleTitle}}</span> hinzu.
        </summary>

        @if(count($this->notSupported) > 0)
        <div class="my-2 w-full bg-gray-200">
            <p class="p-2">Für folgende Bibliotheken können derzeit keine Fragen/Antworten in den Ergebnissen angezeigt werden :(</p>
            <ul class="px-2">
                @foreach ($this->notSupported as $ns)
                    <li class="text-red-500">
                        {{$ns}}
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

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
    </details>

    <div class="mt-5">
        <details {{$this->getQuestionsState()}}>
            <summary @click="$store.accordions.toggleQuestionsState()" class="bg-gray-500 p-2 text-white font-bold cursor-pointer select-none">Fragen im Bundle</summary>

            <div x-data class="bg-green-200 min-h-[150px] p-2">
                {{-- //TODO: remove debug --}}
                {{--<button wire:click="debug()" class="bg-red-500 text-white p-4">DEBUG</button>--}}
                @unless(count($this->questionsInBundle) > 0)
                    <p class="bg-red-500 text-yellow-500">
                        Fragen können nicht angezeigt werden
                    </p>
                @endunless


                @foreach ($this->questionsInBundle as $item)
                    <details class="m-2 bg-blue-300">
                        <summary class="cursor-pointer mx-2 open:border-b open:border-black select-none">{{$item->name}}</summary>
                        <div class="bg-blue-200 p-2">
                            <ol class="px-4 list-[upper-roman]">
                            @foreach ($item->questions as $n => $question)
                                <li>
                                    <h3 class="font-bold {{$n > 0 ? 'mt-3' : ''}}">
                                        Frage: {!!$question!!}
                                    </h3>
                                    @if(count($item->correct_answers)-1 >= $n )
                                        @if(!is_array($item->correct_answers[$n]))
                                        <p>Korrekte Antwort: {{$item->correct_answers[$n]}}</p>
                                        @else
                                        <p>Korrekte Antwort/en:</p>
                                        <ol class="list-decimal px-6">
                                            @foreach($item->correct_answers[$n] as $answer)
                                            <li>{!!$answer!!}</li>
                                            @endforeach
                                        </ol>
                                        @endif
                                        <div class="bg-blue-300 w-[100%] h-[1px] my-1"></div>
                                        <label>Punktemultiplikator:</label>
                                        <input x-on:change="$store.multipliers.setMultiplier('{{$item->content_id}}', {{$n}}, $event)" x-bind:value="$store.multipliers.getMultiplier('{{$item->content_id}}', {{$n}})" type="number" min="1" placeholder="Punktemultiplikator" />
                                        <button @click="$wire.saveMultipliers($store.multipliers.jsonMultipliers)" class="p-2 bg-blue-500 text-white font-bold">Speichern</button>
                                    @endif
                                </li>

                            @endforeach
                            </ol>

                        </div>
                    </details>
                @endforeach

            </div>
        </details>
    </div>

    {{-- EXPORT AREA --}}
    <div class="mt-5">
        <details {{$this->getExportState()}}>
            <summary @click="$store.accordions.toggleExportState()" class="bg-gray-500 p-2 text-white font-bold cursor-pointer select-none">Ergebnisse & Feedback</summary>

            <div class="bg-gray-300 min-h-[100px] flex justify-center align-center items-center">
                @if(isset($this->questionnaire))
                <button wire:click="exportFeedback()" class="m-3 p-4 bg-blue-500 text-white font-bold hover:bg-blue-700 text-lg rounded">Export Feedback</button>
                @endif
            </div>
        </details>
    </div>

    @endif

    @once
<script>
    document.addEventListener('livewire:load', () => {
        Alpine.store('accordions', {
            settings: @entangle('settingsState').defer,
            bundleContent: @entangle('bundleContentState').defer,
            questions: @entangle('questionsState').defer,
            export: @entangle('exportState').defer,

            toggleSettings() {
                this.settings = !this.settings;
            },

            toggleBundleContent() {
                this.bundleContent = !this.bundleContent;
            },

            toggleQuestionsState() {
                this.questions = !this.questions;
            },

            toggleExportState() {
                this.export = !this.export;
            },
        });

        Livewire.on('updatedMultipliers', () => {
            let store = Alpine.store('multipliers');
            store.jsonMultipliers = JSON.parse(store.multipliers);
        });

        Livewire.on('multipliersSet', () => {
            Alpine.store('multipliers', {
                multipliers: @entangle('multipliers').defer,
                jsonMultipliers: null,

                init(){
                    this.jsonMultipliers = JSON.parse(this.multipliers.initialValue)
                },

                getMultiplier(contentId, index){
                    if(this.jsonMultipliers.hasOwnProperty(contentId)){
                        return this.jsonMultipliers[contentId][index];
                    }
                },

                setMultiplier(contentId, index, event){
                    let value = parseInt(event.target.value);
                    if(isNaN(value) || value < 1){
                        event.target.value = this.jsonMultipliers[contentId][index];
                        return;
                    }
                    this.jsonMultipliers[contentId][index] = value;
                },
            });
        });
    });

    document.addEventListener('livewire:load', () => {
        livewire.on('removeFlash', (e) => {
            document.querySelector('#flashMessage').remove();
        });
    });
</script>
@endonce
</div>

