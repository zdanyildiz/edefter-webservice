<?php

require_once ROOT . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminExcelGenerator
{
    private $spreadsheet;
    private $sheet;

    public function __construct()
    {
        error_log("ExcelGenerator: __construct() called.", 0);
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    /**
     * Set the headers for the Excel sheet
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        error_log("ExcelGenerator: setHeaders() called with headers: " . implode(', ', $headers), 0);
        $column = 'A';
        foreach ($headers as $header) {
            $this->sheet->setCellValue($column . '1', mb_convert_encoding($header, 'UTF-8', 'auto'));

            $column++;
        }
    }

    /**
     * Add rows to the Excel sheet
     * @param array $rows
     */
    public function addRows(array $rows)
    {
        error_log("ExcelGenerator: addRows() called with row count: " . count($rows), 0);
        $rowNumber = 2; // Data starts from the second row
        foreach ($rows as $row) {
            $column = 'A';
            foreach ($row as $cell) {
                $this->sheet->setCellValue($column . $rowNumber, $cell);
                $column++;
            }
            $rowNumber++;
        }
    }

    /**
     * Create Excel with headers and rows
     * @param array $headers
     * @param array $rows
     * @param string $sheetTitle
     */
    public function createExcel(array $headers, array $rows, string $sheetTitle = 'Sheet1')
    {
        error_log("ExcelGenerator: createExcel() called.", 0);
        $this->sheet->setTitle($sheetTitle);
        $this->setHeaders($headers);
        $this->addRows($rows);
    }

    /**
     * Save the Excel file to the specified path
     * @param string $filePath
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function saveToFile(string $filePath)
    {
        error_log("ExcelGenerator: saveToFile() called with path: " . $filePath, 0);
        try {
            $writer = new Xlsx($this->spreadsheet);
            $writer->save($filePath);
            error_log("ExcelGenerator: File saved successfully.", 0);
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            error_log("ExcelGenerator: Error saving file - " . $e->getMessage(), 0);
        }
    }

    /**
     * Output the Excel file directly to the browser for download
     * @param string $fileName
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function download(string $fileName)
    {
        error_log("ExcelGenerator: download() called with filename: " . $fileName, 0);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            ob_clean(); // Buffer'ı temizleyin
            flush();    // Kalan veriyi gönderin

            $writer = new Xlsx($this->spreadsheet);
            $writer->save('php://output');
            error_log("ExcelGenerator: File sent for download.", 0);
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            error_log("ExcelGenerator: Error during download - " . $e->getMessage(), 0);
        }
    }


    /**
     * Output Excel for download (Alias for `download` method)
     * @param string $fileName
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function output(string $fileName)
    {
        error_log("ExcelGenerator: output() called with filename: " . $fileName, 0);
        $this->download($fileName);
    }
}
?>
