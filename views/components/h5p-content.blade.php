<div class="flex flex-col justify-center">
    @if($showTitle)
        <h1 class="mx-2 py-1 font-bold text-2xl text-center">{{$title}}</h1>
    @endif
    @if($contentId)
        <iframe id="h5p-iframe-{{$contentId}}" src="http://localhost:8000/admin/h5p/h5p/embed/{{$contentId}}" width="{{$width}}" height="{{$height}}" frameborder="0" allowfullscreen>
        </iframe>
        <script src="http://localhost:8000/assets/vendor/h5p/h5p-core/js/h5p-resizer.js" charset="UTF-8"></script>
    @endif
</div>


