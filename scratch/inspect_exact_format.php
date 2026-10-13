<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === true) {
    $xml = $zip->getFromName('word/document.xml');
    
    // 1. Check sectPr (section properties, page margins)
    preg_match_all('/<w:sectPr(?:\s[^>]*)?>(.*?)<\/w:sectPr>/s', $xml, $sects);
    echo "=== SECTIONS (" . count($sects[0]) . ") ===\n";
    foreach ($sects[0] as $i => $s) {
        echo "Section $i:\n";
        if (preg_match('/<w:pgMar\s+([^>]+)\/>/', $s, $m)) {
            echo "  pgMar: " . $m[1] . "\n";
        }
        if (preg_match('/<w:pgSz\s+([^>]+)\/>/', $s, $m)) {
            echo "  pgSz: " . $m[1] . "\n";
        }
        if (preg_match('/<w:pgBorders\s+([^>]*?)>(.*?)<\/w:pgBorders>/s', $s, $m)) {
            echo "  pgBorders: " . $m[0] . "\n";
        }
    }
    
    // 2. Check headers and footers
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (strpos($name, 'word/header') === 0 || strpos($name, 'word/footer') === 0) {
            $hfXml = $zip->getFromName($name);
            $hfText = strip_tags($hfXml);
            echo "=== $name ===\n";
            echo "Text: " . trim($hfText) . "\n";
            echo "Raw: " . mb_substr($hfXml, 0, 300) . "\n\n";
        }
    }
    
    // 3. Check paragraph spacing in body
    preg_match_all('/<w:spacing\s+([^>]+)\/>/', $xml, $spacings);
    $uniqueSpacings = array_unique($spacings[1]);
    echo "=== UNIQUE SPACINGS ===\n";
    foreach (array_slice($uniqueSpacings, 0, 10) as $us) {
        echo "  spacing: $us\n";
    }
    
    // 4. Check rFonts
    preg_match_all('/<w:rFonts\s+([^>]+)\/>/', $xml, $fonts);
    $uniqueFonts = array_unique($fonts[1]);
    echo "=== UNIQUE FONTS ===\n";
    foreach (array_slice($uniqueFonts, 0, 10) as $uf) {
        echo "  font: $uf\n";
    }
    
    // 5. Check table borders and margins
    preg_match('/<w:tblBorders>(.*?)<\/w:tblBorders>/s', $xml, $tb);
    if ($tb) {
        echo "=== TBL BORDERS ===\n" . $tb[0] . "\n";
    }
    preg_match('/<w:tblCellMar>(.*?)<\/w:tblCellMar>/s', $xml, $tcm);
    if ($tcm) {
        echo "=== TBL CELL MARGINS ===\n" . $tcm[0] . "\n";
    }
}
