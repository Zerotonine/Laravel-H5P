

@if($type === 'positive')
    <div title="Schließen" id="flashMessage" wire:click="$emit('removeFlash')" class="hover:cursor-pointer bg-emerald-100 border-t border-b border-emerald-500 text-emerald-700 px-4 py-1" role="alert">
        <p class="font-bold my-1">{{$title}}</p>
        <p class="text-sm mb-1">{{$message}}</p>
    </div>
@elseif($type === 'negative')
    <div title="Schließen" id="flashMessage" wire:click="$emit('removeFlash')" class="hover:cursor-pointer bg-red-100 border-t border-b border-red-500 text-red-700 px-4 py-1" role="alert">
        <p class="font-bold my-1">{{$title}}</p>
        <p class="text-sm mb-1">{{$message}}</p>
    </div>
@endif