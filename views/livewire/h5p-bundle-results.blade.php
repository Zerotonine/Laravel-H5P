<div class="my-2">
    <div class="bg-white opacity-70 m-2">
        <h1 class="mx-2 py-1 font-bold text-2xl text-center z-[100]">Ergebnisse</h1>
    </div>

    <div class="mx-2 grid grid-cols-2 z-[100]">
        <div class="text-center border-b border-black border-solid bg-blue-500 z-[100] font-bold p-1 text-xl">
            Quiz
        </div>
        <div class="bg-green-500 border-b border-solid border-black text-center z-[100] font-bold p-1 text-xl">
            Punktzahl
        </div>
        @foreach ($this->entries as $n => $entry)
            <div class="border-b border-black border-solid bg-blue-500 z-[100] text-center">
                <p class="text-xl">{{$entry->title}}</p>
            </div>

            <div class="bg-green-500 border-b border-solid border-black text-center z-[100]">
                @if($this->results['score'][$n] === 'n/a')
                    <p class="text-xl">Ergebnis kann nicht angezeigt werden</p>
                @else
                    <p class="text-xl">{{$this->results['score'][$n]}}/{{$this->results['max_score'][$n]}} ({{$this->getPercentage($this->results['score'][$n], $this->results['max_score'][$n])}}%)</p>
                @endif
            </div>
        @endforeach


        <div class="col-start-2 text-center bg-red-200 z-[100]">
            <p class="text-xl">Gesamtpunktzahl: {{$this->totalScore->score}}/{{$this->totalScore->max_score}} ({{$this->getPercentage($this->totalScore->score, $this->totalScore->max_score)}}%)</p>
        </div>
    </div>

</div>