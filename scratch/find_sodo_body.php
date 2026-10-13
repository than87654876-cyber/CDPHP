<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
    
    // Parse rels
    preg_match_all('/Id="([^"]+)"[^>]+Target="([^"]+)"/', $relsXml, $rm);
    $rels = array_combine($rm[1], $rm[2]);
    
    $pos = 500000;
    while (($pos = mb_strpos($xml, 'Sơ đồ 2.', $pos)) !== false) {
        $start = max(0, $pos - 500);
        $chunk = mb_substr($xml, $start, 1500);
        
        preg_match_all('/r:embed="([^"]+)"/', $chunk, $m);
        echo "Found Sơ đồ around pos $pos:\n";
        foreach ($m[1] as $rId) {
            echo "  Embed rId: $rId -> " . ($rels[$rId] ?? 'unknown') . "\n";
        }
        echo strip_tags($chunk) . "\n-----------------------------------------\n";
        $pos += 20;
    }
}
