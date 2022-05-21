<div class="w-full h-screen">
    <div class="w-full h-screen flex flex-row">
        @if(count($this->entries) > 0)
        <div id="menu" x-data="menu" class="border-r border-solid border-black z-[100] shadow-2xl">
            {{-- Menu Icon --}}
            <svg name="icon" class="cursor-pointer w-[30px] h-[30px] m-3 hidden" @click="toggle" x-show="open" fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="50px" height="50px"><path d="M 9.15625 6.3125 L 6.3125 9.15625 L 22.15625 25 L 6.21875 40.96875 L 9.03125 43.78125 L 25 27.84375 L 40.9375 43.78125 L 43.78125 40.9375 L 27.84375 25 L 43.6875 9.15625 L 40.84375 6.3125 L 25 22.15625 Z"/></svg>
            {{-- Close Icon --}}
            <svg name="icon" class="cursor-pointer w-[30px] h-[30px] m-3 hidden" @click="toggle" x-show="!open" fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="50px" height="50px"><path d="M 0 7.5 L 0 12.5 L 50 12.5 L 50 7.5 L 0 7.5 z M 0 22.5 L 0 27.5 L 50 27.5 L 50 22.5 L 0 22.5 z M 0 37.5 L 0 42.5 L 50 42.5 L 50 37.5 L 0 37.5 z"/></svg>
            <div x-show="open" class="max-w-[400px] w-[400px] h-auto z-[100]">
                @foreach ($this->entries as $n => $entry)
                <div class="min-h-fit p-1 border-b border-solid border-black cursor-pointer hover:bg-gray-400 {{(!$this->showResults && $this->activeContentId === $entry->id) ? 'bg-gray-300' : ''}} text-center {{$n === 0 ? 'border-t' : ''}}">
                    <button wire:click="switchContent({{$entry->id}})" class="font-semibold w-full bg-transparent">{{$entry->title}}</button>
                </div>
                @endforeach
                <div class="min-h-fit p-1 border-b border-solid border-black cursor-pointer hover:bg-gray-400 {{$this->showResults ? 'bg-gray-300' : ''}} text-center {{$n === 0 ? 'border-t' : ''}}">
                    <button wire:click="showResults()" class="font-semibold w-full bg-transparent">Ergebnisse</button>
                </div>
            </div>
        </div>
        @endif

        <div id="content" class="flex-column w-full h-full justify-center overflow-y-hidden" style="background-image: url('{{$this->background}}'); background-size: cover;">
            <div class="w-full h-full bg-black fixed opacity-30"></div>

            @if(!empty($this->watermark))
                <img src="{{$this->watermark}}" alt="Wasserzeichen" style="opacity: {{$this->watermark_opacity > 0 ? $this->watermark_opacity / 100 : 0}} ;" class="z-10 fixed right-0 bottom-0 max-h-[200px] max-w-[200px]">
            @endif
            @if(!$this->showResults)
                <div id="frameContainer" class="w-full h-full">
                    @unless (count($this->entries) > 0)
                        <h1 class="w-screen h-screen flex justify-center items-center py-1 font-bold text-2xl text-center z-[100]">Keine Inhalte gefunden</h1>
                    @endunless
                    @if($this->activeContentId)
                        <x-lh5p::h5p-content :contentId="$this->activeContentId" :bundleId="$this->bundle" :showTitle="true" />
                    @endif
                </div>
            @else
                <div class="w-full h-full">
                    @livewire('h5p-bundle-results', ['entries' => $this->entries, 'bundleId' => $this->bundle])
                </div>
            @endif
        </div>

    </div>

@once
    <script>
        const removeHidden = () => {
            let icons = document.getElementsByName('icon');
            icons.forEach(icon => {
                icon.classList.remove('hidden');
            });
        }

        const resizeFrame = (contentId) => {
            if(!@this.showResults)
            {
                let frame = document.getElementById('h5p-iframe-'+contentId);
                const container = document.getElementById('frameContainer');
                const menu = document.getElementById('menu');

                frame.width = container.clientWidth;
                frame.height = container.clientHeight;
            }
        }

        const isNotEmpty = () => {
            return @js($this->entries).length > 0;
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (isNotEmpty()){
                resizeFrame(@this.activeContentId);
            }
            removeHidden();

            livewire.on('contentChanged', (contentId) => {
                console.log("contentId: ", contentId);
                resizeFrame(contentId);
            });

            window.addEventListener('resize', () => {
                resizeFrame(@this.activeContentId);
            });

        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('menu', () => ({
                open: isNotEmpty() ? true : false,

                toggle(){
                    this.open = !this.open;
                    this.$nextTick(() => {
                        resizeFrame(@this.activeContentId);
                    });
                }
            }));
        });

    </script>
@endonce

</div>

