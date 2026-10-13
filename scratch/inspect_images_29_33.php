<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
    
    preg_match_all('/Id="([^"]+)"[^>]*Target="([^"]+)"/', $relsXml, $relMatches);
    $rels = [];
    for ($i = 0; $i < count($relMatches[1]); $i++) {
        $rels[$relMatches[1][$i]] = $relMatches[2][$i];
    }
    
    preg_match_all('/<w:p[^>]*>(.*?)<\/w:p>/s', $xml, $pMatches);
    
    for ($i = 0; $i < count($pMatches[1]); $i++) {
        $p = $pMatches[1][$i];
        if (strpos($p, 'r:embed=') !== false) {
            preg_match('/r:embed="([^"]+)"/', $p, $rId);
            $target = isset($rId[1]) && isset($rels[$rId[1]]) ? $rels[$rId[1]] : 'unknown';
            
            if (preg_match('/image(29|30|31|32|33)/', $target)) {
                echo "-------------------------------------\n";
                echo "IMG: $target\n";
                for ($k = max(0, $i - 3); $k <= min(count($pMatches[1]) - 1, $i + 3); $k++) {
                    $t = trim(preg_replace('/<[^>]+>/', '', $pMatches[1][$k]));
                    if ($t) echo "  LINE $k: $t\n";
                }
            }
        }
    }
    $zip->close();
}
