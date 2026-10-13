<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    preg_match_all('/<w:p[^>]*>(.*?)<\/w:p>/s', $xml, $matches);
    
    $sections = [];
    $currentSection = "START";
    
    foreach ($matches[1] as $p) {
        $text = trim(preg_replace('/<[^>]+>/', '', $p));
        if (empty($text)) continue;
        
        // Check if heading
        if (preg_match('/^(Chương\s+\d+|[1-4]\.\d+(\.\d+)?|MỤC LỤC|LỜI CẢM ƠN|LỜI CAM ĐOAN|LỜI MỞ ĐẦU|PHỤ LỤC|DANH MỤC)/iu', $text)) {
            echo $text . PHP_EOL;
        }
    }
    $zip->close();
}
