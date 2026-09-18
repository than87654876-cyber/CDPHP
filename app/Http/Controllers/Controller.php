<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class Controller
{
    /**
     * Helper stream file CSV với mã hóa UTF-8 BOM chuẩn hiển thị tiếng Việt trên mọi phần mềm (Excel, LibreOffice...).
     *
     * @param string $filename Tên tệp tin tải về
     * @param array $headers Mảng tiêu đề các cột
     * @param iterable $records Danh sách bản ghi cần xuất
     * @param callable $rowCallback Hàm ánh xạ từng bản ghi thành mảng dữ liệu dòng
     * @return StreamedResponse
     */
    protected function exportCsvStream(string $filename, array $headers, iterable $records, callable $rowCallback): StreamedResponse
    {
        $responseHeaders = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($headers, $records, $rowCallback) {
            $file = fopen('php://output', 'w');
            // Ghi UTF-8 BOM để Excel đọc đúng tiếng Việt
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Ghi hàng tiêu đề cột
            fputcsv($file, $headers);

            // Ghi từng dòng dữ liệu
            foreach ($records as $record) {
                $row = $rowCallback($record);
                if (is_array($row)) {
                    fputcsv($file, $row);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $responseHeaders);
    }
}
