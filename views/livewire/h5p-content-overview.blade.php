{{-- @extends( config('laravel-h5p.layout') ) --}}

{{-- @section( 'h5p' ) --}}
<div class="container-fluid">
    @if($showModal)
    {{-- @include('lh5p::h5p-deletion-modal') --}}
    <x-lh5p::h5p-deletion-modal>
        <x-slot name="title">
            Inhalt löschen
        </x-slot>

        <x-slot name="value">
            Sind Sie sicher das Sie dieses Paket unwiderruflich löschen wollen?
        </x-slot>

        <x-slot name="footer">
            <button wire:click="delete" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Löschen</button>
            <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Abbrechen</button>
        </x-slot>
    </x-lh5p::h5p-deletion-modal>
    @endif

    @if($showEditor && $contentId !== null) {{-- edit mode --}}
        <x-lh5p::h5p-editor-modal :contentId="$contentId" :nonce="$nonce" />
    @elseif($showEditor && $contentId === null) {{-- create mode --}}
        <x-lh5p::h5p-editor-modal :nonce="$nonce" />
    @endif


    {{-- <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-3">
            <a href="{{ route("h5p.create") }}" class="bg-blue-500 text-white fontbold p-2 m-2 rounded">{{ trans('laravel-h5p.content.create') }}</a>
        </div>
    </div> --}}

    <div class="mb-3">
        <button wire:click="newContent" class="bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 font-bold rounded">{{trans('laravel-h5p.content.create')}}</button>
    </div>

    {{-- CONTENT DELETION MESSAGES --}}
    @if (session()->has('h5p.deleted.complete'))
        @livewire('h5p-flash', ['title' => "Erfolg!", 'message' => session('h5p.deleted.complete')])
    @elseif (session()->has('h5p.deleted.error'))
        @livewire('h5p-flash', ['title' => 'Fehler!', 'message' => session('h5p.deleted.error'), 'type' => 'negative'])
    @endif

    {{-- CONTENT CREATION/EDITING MESSAGES --}}
    @if(session()->has('success'))
        @livewire('h5p-flash', ['title' => 'Erfolg!', 'message' => session('success')])
    @elseif(session()->has('fail'))
        @livewire('h5p-flash', ['title' => 'Fehler!', 'message' => session('fail'), 'type' => 'negative'])
    @endif

    <div class="row">

        <div class="col-md-12">

            <table class="h5p-lists min-w-full border-collapse block md:table">
                <colgroup>
                    <col width="10%">
                    <col width="15%">
                    <col width="*">
                    <col width="10%">
                    <col width="15%">
                </colgroup>

                <thead class="block md:table-header-group">
                    <tr class="border border-grey-500 md:border-none block md:table-row absolute -top-full md:top-auto -left-full md:left-auto md:relative">
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-center block md:table-cell">#</th>
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-left block md:table-cell">{{ trans('laravel-h5p.content.creator') }}</th>
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-left block md:table-cell">{{ trans('laravel-h5p.content.title') }}</th>
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-left block md:table-cell">{{ trans('laravel-h5p.content.created_at') }}</th>
                        <th class="bg-gray-600 p-2 text-white font-bold md:border md:border-grey-500 text-left block md:table-cell">{{ trans('laravel-h5p.content.action') }}</th>
                    </tr>
                </thead>

                <tbody class="block md:table-row-group">

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
                            {{ $entry->get_user()->name }}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                            <a class="underline" href="{{ route('h5p.show', $entry->id) }}">{{ $entry->title }}</a>
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                            {{ $entry->updated_at->format('Y.m.d') }}
                        </td>

                        <td class="p-2 md:border md:border-grey-500 text-center block md:table-cell">
                            {{-- <a href="{{ route('h5p.edit', $entry->id) }}" class="btn btn-default"  data-tooltip="{pos:'top'}" title="{{ trans('laravel-h5p.content.edit') }}">{{ trans('laravel-h5p.content.edit') }}</a> --}}
                            <button wire:click="showEditor({{$entry->id}})" class="font-bold text-white py-2 px-4 rounded bg-blue-500 hover:bg-blue-700">{{ trans('laravel-h5p.content.edit') }}</button>
                            {{-- <button class="btn btn-danger h5p-delete" data-delete="{{ route('h5p.destroy', $entry->id) }}" data-tooltip="{pos:'top'}" title="{{ trans('laravel-h5p.content.destroy') }}">{{ trans('laravel-h5p.content.destroy') }}</button> --}}
                            <button wire:click="showModal({{$entry->id}}, '{{$entry->title}}')" class="hover:bg-red-500 hover:text-white border border-red-500 font-semibold py-2 px-2 rounded">{{ trans('laravel-h5p.content.destroy') }}</button>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>


    {{-- <div class="row">

        <div class="col-md-12 text-center" style='margin-top:20px;'>
            {!! $entries->render() !!}
        </div>

    </div> --}}

    <div class="row">
        <div class="col-md-12 mt-2">
            {{$this->entries->links()}}
        </div>
    </div>
</div>

{{-- @endsection --}}

@push( 'h5p-header-script' )
@foreach($settings['core']['styles'] as $style)
{{ Html::style($style) }}
@endforeach
@endpush

@push( 'h5p-footer-script' )
<script type="text/javascript">
    H5PIntegration = {!! json_encode($settings) !!};
</script>

@foreach($settings['core']['scripts'] as $script)
{{ Html::script($script) }}
@endforeach

<script>

document.addEventListener('livewire:load', () => {
    livewire.on('removeFlash', (e) => {
        document.querySelector('#flashMessage').remove();
    });
});

H5P.jQuery(document).ready(function () {
    H5P.jQuery('#save-button').click(function () {
        setTimeout(() => {
            H5P.jQuery(this).prop('disabled', 'disabled');
            H5P.jQuery('.h5p-delete').prop('disabled', 'disabled');
        }, 50);
    })
});
</script>

@endpush

{{-- @push( 'h5p-header-script' )
@endpush

@push( 'h5p-footer-script' )
<script type="text/javascript">

    H5P.jQuery(document).ready(function () {

        H5P.jQuery('.h5p-delete').on('click', function () {

            var $obj = H5P.jQuery(this);
            var msg = "{{ trans('laravel-h5p.content.confirm_destroy') }}";
            if (confirm(msg)) {

                H5P.jQuery.ajax({
                    url: $obj.data('delete'),
                    method: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': H5P.jQuery('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (data) {
                        location.reload();
                    },
                    error: function () {
                        alert("{{ trans('laravel-h5p.content.can_not_delete') }}");
                    }
                })
            }

        });
    });

</script>
@endpush --}}
