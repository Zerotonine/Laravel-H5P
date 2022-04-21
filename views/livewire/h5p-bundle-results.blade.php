<div class="my-2">
    <h1 class="text-2xl font-bold text-center">Ergebnisse</h1>

    <div class="mx-3 grid grid-cols-2">
        @foreach ($this->entries as $n => $entry)
            <div class="border-b border-black border-solid bg-blue-500">
                {{$entry->title}}
            </div>

            <div class="bg-green-500 border-b border-solid border-black text-center">
                @if($this->results['score'][$n] === 'n/a')
                    <p class="text-xl">Keine Ergebnisse gefunden</p>
                @else
                    <p class="text-xl">Erreichte Punktzahl: {{$this->results['score'][$n]}}/{{$this->results['max_score'][$n]}} ({{$this->getPercentage($this->results['score'][$n], $this->results['max_score'][$n])}}%)</p>
                @endif
            </div>
        @endforeach


        <div class="col-start-2 text-center bg-red-200">
            <p class="text-xl">Gesamtpunktzahl: {{$this->totalScore->score}}/{{$this->totalScore->max_score}} ({{$this->getPercentage($this->totalScore->score, $this->totalScore->max_score)}}%)</p>
        </div>
    </div>

</div>