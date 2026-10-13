<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
    
    // Parse rels
    preg_match_all('/Id="([^"]+)"[^>]*Target="([^"]+)"/', $relsXml, $relMatches);
    $rels = [];
    for ($i = 0; $i < count($relMatches[1]); $i++) {
        $rels[$relMatches[1][$i]] = $relMatches[2][$i];
    }
    
    // Split into paragraphs
    preg_match_all('/<w:p[^>]*>(.*?)<\/w:p>/s', $xml, $pMatches);
    
    for ($i = 0; $i < count($pMatches[1]); $i++) {
        $p = $pMatches[1][$i];
        if (strpos($p, 'r:embed=') !== false) {
            preg_match('/r:embed="([^"]+)"/', $p, $rId);
            $target = isset($rId[1]) && isset($rels[$rId[1]]) ? $rels[$rId[1]] : 'unknown';
            
            // Check text of this paragraph and adjacent paragraphs
            $prevText = ($i > 0) ? trim(preg_replace('/<[^>]+>/', '', $pMatches[1][$i-1])) : '';
            $thisText = trim(preg_replace('/<[^>]+>/', '', $p));
            $nextText = ($i < count($pMatches[1]) - 1) ? trim(preg_replace('/<[^>]+>/', '', $pMatches[1][$i+1])) : '';
            
            echo "IMG: $target\n";
            if ($prevText) echo "  PREV: $prevText\n";
            if ($thisText) echo "  THIS: $thisText\n";
            if ($nextText) echo "  NEXT: $nextText\n";
            echo "-------------------------------------\n";
        }
    }
    $zip->close();
}
