<?php

namespace EscolaSoft\LaravelH5p\Exporter;

use Illuminate\Support\Facades\Storage;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FeedbackExporter {
    public static function exportFeedback(array $questionnaire){
        $questionnaire = (object)$questionnaire;
        //dd($questionnaire);

        self::createDir($questionnaire->bundle_id);
        //self::testSheet($questionnaire->bundle_id);
        $data = self::buildSheetData($questionnaire);
        self::saveFeedbackSheet($data, $questionnaire->bundle_id);

        return Storage::path("/public/h5p/results/{$questionnaire->bundle_id}/feedback.xlsx");
    }

    private static function createDir($bundleId){
        Storage::makeDirectory("public/h5p/results/{$bundleId}");
    }

    public static function buildSheetData(array|object $questionnaireData):array{
        if(is_array($questionnaireData)){
            $questionnaireData = (object)$questionnaireData;
        }

        // $data = [
        //     ['bundleId', 'Frage', 'Antwort/en']
        // ];
        $data = [
            ['Kurs', 'User', 'Email', 'Frage', 'Antwort/en']
        ];
        //dd($questionnaireData);
        // foreach($questionnaireData->questions as $n => $question){
        //     $question = (object)$question;

        //     foreach($questionnaireData->given_answers as $m => $answer){
        //         array_push($data, [
        //             $questionnaireData->bundle_id,
        //             $question->question,
        //             $question->machineName === 'H5P.OpenEndedQuestion' ? $answer[$n]['answers']
        //             : implode(', ', $answer[$n]['answers'])
        //         ]);
        //     }
        // }
        foreach($questionnaireData->questions as $n => $question){
            $question = (object)$question;

            foreach($questionnaireData->given_answers as $m => $answer){
                array_push($data, [
                    $answer[$n]['bundle_name'],
                    $answer[$n]['user_name'],
                    $answer[$n]['email'],
                    $question->question,
                    $question->machineName === 'H5P.OpenEndedQuestion' ? $answer[$n]['answers']
                    : implode(', ', $answer[$n]['answers'])
                ]);
            }
        }

        return $data;
    }

    private static function saveFeedbackSheet(array $sheetData, $bundleId){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($sheetData, NULL, 'A1');

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path("app/public/h5p/results/{$bundleId}/feedback.xlsx"));
    }

    private static function testSheet($bundleId){
        $header = ['Username', 'Kurs', 'Frage', 'Antwort/en'];

        $data = [
            ['Username', 'Kurs', 'Frage', 'Antwort/en'],
            ['Hans', 'UE', 'WTF', 'Bro'],
            ['Peter', 'UE', 'Antwort auf alle Fragen?', '42']
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data, NULL, 'A1');

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path("app/public/h5p/results/{$bundleId}/test.xlsx"));
    }
}