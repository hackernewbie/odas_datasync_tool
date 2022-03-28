<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;

class GoogleSheetService
{
    private $spreadSheetId;
    private $sheetName;
    private $client;
    private $googleSheetService;

    public function __construct()
    {
        $this->spreadSheetId = config('google.google_sheet_id');
        $this->sheetName     = config('google.sheet_name');
        $this->client        = new Google_Client();
        $this->client->setAuthConfig(storage_path('creds.json'));
        $this->client->addScope("https://www.googleapis.com/auth/spreadsheets");

        $this->googleSheetService = new Google_Service_Sheets($this->client);

    }

    public function readGoogleSheet($sheetName)
    {
        $dimensions = $this->getDimensions($this->spreadSheetId, $this->sheetName);
        $range = $sheetName.'!A1:' . $dimensions['colCount'];
        //$range = $sheetName.'!A1:' . 'AL';
        //dd($range);
        $data = $this->googleSheetService
            ->spreadsheets_values
            ->batchGet($this->spreadSheetId, ['ranges' => $range]);

        dd($data->getValueRanges()[0]->values);
        return $data->getValueRanges()[0]->values;
    }

    private function getDimensions($spreadSheetId, $sheetName)
    {
        //dd($this->googleSheetService->spreadsheets_values);
        $rowDimensions = $this->googleSheetService->spreadsheets_values->batchGet(
            $spreadSheetId,
            ['ranges' => $sheetName.'!A:A', 'majorDimension' => 'COLUMNS']
        );

        //if data is present at nth row, it will return array till nth row
        //if all column values are empty, it returns null
        $rowMeta = $rowDimensions->getValueRanges()[0]->values;
        if (!$rowMeta) {
            return [
                'error' => true,
                'message' => 'missing row data'
            ];
        }

        $colDimensions = $this->googleSheetService->spreadsheets_values->batchGet(
            $spreadSheetId,
            ['ranges' => $sheetName.'!1:1', 'majorDimension' => 'ROWS']
        );

        //if data is present at nth col, it will return array till nth col
        //if all column values are empty, it returns null
        $colMeta = $colDimensions->getValueRanges()[0]->values;
        if (!$colMeta) {
            return [
                'error' => true,
                'message' => 'missing row data'
            ];
        }

        return [
            'error' => false,
            'rowCount' => count($rowMeta[0]),
            'colCount' => $this->colLengthToColumnAddress(count($colMeta[0]))
        ];
    }

    private function colLengthToColumnAddress($number)
    {
        if ($number <= 0) return null;

        $letter = '';
        while ($number > 0) {
            $temp = ($number - 1) % 26;
            $letter = chr($temp + 65) . $letter;
            $number = ($number - $temp - 1) / 26;
        }
        return $letter;
    }
}
