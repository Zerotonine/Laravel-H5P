{{-- @extends( config('laravel-h5p.layout') ) --}}

{{-- @section( 'h5p' ) --}}
<div class="container-fluid p-3">

    <div class="row">

        <div class="col-md-12">
            {{-- <button wire:click="$emit('closeEditor')" class="bg-red-500 p-1 text-white font-semibold">Close</button> --}}
            @if($mode == 'create')
                <form method="POST" action="{{route('h5p.store')}}" class="form-horizontal" enctype="multipart/form-data" id="laravel-h5p-form" >
                    {{csrf_field()}}
                    <input type="hidden" name="library" wire:model="library" id="laravel-h5p-library" />
                    <input type="hidden" name="parameters" id="laravel-h5p-parameters" wire:model="parameters" />
                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}" />
                    <input type="hidden" name="nonce" wire:model="nonce" />

                    <fieldset>
                        <div id="laravel-h5p-create" >

                            <div id="laravel-h5p-editor">{{trans('laravel-h5p.content.loading_content')}}</div>
                        </div>
                    </fieldset>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold p-2 rounded">{{trans('laravel-h5p.content.save')}}</button>
                </form>

            @elseif($mode == 'edit')
                <form method="POST" action="{{route('h5p.update', $contentId)}}" class="form-horizontal" enctype="multipart/form-data" id="laravel-h5p-form">
                    <input type="hidden" name="_method" value="PATCH" />
                    {{csrf_field()}}
                    <input type="hidden" name="library" wire:model="library" id="laravel-h5p-library" />
                    <input type="hidden" name="parameters" wire:model="parameters" id="laravel-h5p-parameters" />
                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}" />
                    <input type="hidden" name="nonce" wire:model="nonce" />

                    <fieldset>
                        <div id="laravel-h5p-create">
                            <div id="laravel-h5p-editor">{{trans('laravel-h5p.content.loading_content')}}</div>
                        </div>
                    </fieldset>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold p-2 rounded">{{trans('laravel-h5p.content.save')}}</button>
                </form>

            @endif


            {{-- old form --}}

            {{-- {!! Form::open(['route' => ['h5p.store'], 'class'=>'form-horizontal', 'enctype'=>"multipart/form-data", 'id'=>'laravel-h5p-form']) !!}
            <input type="hidden" name="library" id="laravel-h5p-library" value="{{ $library }}">
            <input type="hidden" name="parameters" id="laravel-h5p-parameters" value="{{ $parameters }}">
            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" name="nonce" value="{{ $nonce }}">
            <input type="hidden" name="action" value="create" />

            <fieldset>

                <div id="laravel-h5p-create" class="form-group {{ $errors->has('parameters') ? 'has-error' : '' }}">
                    <label for="inputParameters" class="control-label">{{ trans('laravel-h5p.content.parameters') }}</label>
                    <div>
                        <div>
                            <div id="laravel-h5p-editor">{{ trans('laravel-h5p.content.loading_content') }}</div>
                        </div>

                        @if ($errors->has('parameters'))
                        <span class="help-block">
                            {{ $errors->first('parameters') }}
                        </span>
                        @endif

                    </div>
                </div> --}}







                {{-- @if(config('laravel-h5p.h5p_show_display_option'))

                <div class="form-group h5p-sidebar">
                    <label class="control-label col-md-3">{{ trans('laravel-h5p.content.display') }}</label>
                    <div class="col-md-9">

                        <div class="form-control-static">

                            <ul class="list-unstyled">

                                <li>
                                    <label>
                                        {{ Form::checkbox('frame', true, $display_options[H5PCore::DISPLAY_OPTION_FRAME], [
                                        'class' => 'h5p-visibility-toggler',
                                        'data-h5p-visibility-subject-selector' => ".h5p-action-bar-buttons-settings",
                                        'id' => 'laravel-h5p-title',
                                        'value' => old('title')
                                    ]) }}
                                        {{ trans("laravel-h5p.content.display_toolbar") }}
                                    </label>
                                </li> --}}


                                {{-- CURRENTLY NOT WORKING FOR WHATEVER REASON --}}
                                {{-- @if(isset($display_options[H5PCore::DISPLAY_OPTION_DOWNLOAD]))
                                <li>
                                    <label>
                                        {{ Form::checkbox('download', true, $display_options[H5PCore::DISPLAY_OPTION_DOWNLOAD], [
                                        'class' => 'h5p-visibility-toggler',
                                        'data-h5p-visibility-subject-selector' => ".h5p-action-bar-buttons-settings",
                                        'id' => 'laravel-h5p-title',
                                        'value' => old('title')
                                    ]) }}
                                        {{ trans("laravel-h5p.content.display_download_button") }}
                                    </label>
                                </li>
                                @endif

                                @if (isset($display_options[H5PCore::DISPLAY_OPTION_EMBED]))
                                <li>
                                    <label>
                                        {{ Form::checkbox('embed', true, $display_options[H5PCore::DISPLAY_OPTION_EMBED], [
                                        'class' => 'h5p-visibility-toggler',
                                        'data-h5p-visibility-subject-selector' => ".h5p-action-bar-buttons-settings",
                                        'id' => 'laravel-h5p-title',
                                        'value' => old('title')
                                    ]) }}
                                        {{ trans("laravel-h5p.content.display_embed_button") }}
                                    </label>
                                </li>
                                @endif

                                @if  (isset($display_options[H5PCore::DISPLAY_OPTION_COPYRIGHT]))
                                <li>
                                    <label>
                                        {{ Form::checkbox('copyright', true, $display_options[H5PCore::DISPLAY_OPTION_COPYRIGHT], [
                                        'class' => 'h5p-visibility-toggler',
                                        'data-h5p-visibility-subject-selector' => ".h5p-action-bar-buttons-settings",
                                        'id' => 'laravel-h5p-title',
                                        'value' => old('title')
                                    ]) }}
                                        {{ trans("laravel-h5p.content.display_copyright_button") }}
                                    </label>
                                </li>
                                @endif --}}

                            {{-- </ul>
                        </div>

                    </div>

                </div>
                @endif --}}


                {{-- <div class="form-group">
                <div class="d-flex justify-content-between w-100">
                    <div></div>

                    <div>
                        <a href="{{ route('h5p.index') }}" class="btn btn-default"><i class="fa fa-reply"></i> {{ trans('laravel-h5p.content.cancel') }}</a>

                        {{ Form::submit(trans('laravel-h5p.content.save'), [
                    "class"=>"btn btn-primary",
                    "data-loading-text" => trans('laravel-h5p.content.saving'),
                    "id" => 'save-button'
                            ]) }}
                    </div>

                </div>

            </div> --}}


            {{-- </fieldset>




            {!! Form::close() !!} --}}

        </div>

    </div>
</div>



{{-- @endsection --}}

@push( 'h5p-header-script' )
{{--    core styles       --}}
@foreach($settings['core']['styles'] as $style)
{{ Html::style($style) }}
@endforeach
@endpush

@push( 'h5p-footer-script' )
<script type="text/javascript">
    H5PIntegration = {!! json_encode($settings) !!};
</script>

{{--    core script       --}}
@foreach($settings['core']['scripts'] as $script)
{{ Html::script($script) }}
@endforeach

<script>
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
