<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    
    // Find references around Sơ đồ
    $pos = 0;
    while (($pos = mb_strpos($xml, 'Sơ đồ 2.', $pos)) !== false) {
        $start = max(0, $pos - 300);
        $chunk = mb_substr($xml, $start, 1200);
        
        // Strip tags but keep image ids
        preg_match_all('/r:embed="([^"]+)"/', $chunk, $m);
        echo "Found Sơ đồ around pos $pos:\n";
        echo "Embeds: " . implode(', ', $m[1]) . "\n";
        echo strip_tags($chunk) . "\n-----------------------------------------\n";
        $pos += 20;
    }
}
