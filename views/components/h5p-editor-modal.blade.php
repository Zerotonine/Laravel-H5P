<div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​

    <div onclick="livewire.emit('closeEditor')" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
    </div>

    <div class="w-2/4 relative inline-block align-bottom bg-white rounded-lg text-left shadow-xl transition-all sm:my-8 sm:align-middle">
      <div class="bg-white px-2 pt-3 pb-2 sm:p-3 sm:pb-2 rounded">
        {{-- <div class="sm:flex sm:items-start"> --}}

          <div class="mt-3 text-center sm:mt-0 sm:ml-2 sm:text-left">
            @if($contentId !== null)
              @livewire('h5p-editor', ['mode' => 'edit', 'contentId' => $contentId, 'nonce' => $nonce])
            @else
              @livewire('h5p-editor', ['mode' => 'create', 'nonce' => $nonce])
            @endif
          </div>
        {{-- </div> --}}
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('keydown', e => {
    if(e.keyCode === 27){
      livewire.emit('closeEditor');
    }
  });
</script>


