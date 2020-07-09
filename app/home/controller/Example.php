<?php

declare(strict_types=1);

namespace app\home\controller;

use app\BaseController;

class Example extends BaseController
{
    public function orderExport()
    {
        // 导出 Demo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
