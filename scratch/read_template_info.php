<?php
// Inspect template guidelines
$templateFile = __DIR__ . '/../docs/Mẫu hướng dẫn trình bày báo cáo (4).docx';
if (!file_exists($templateFile)) {
    // Try globbing
    $files = glob(__DIR__ . '/../docs/*.docx');
    echo "Found docx files:\n";
    foreach ($files as $f) {
        echo basename($f) . "\n";
    }
    exit(1);
}

$zip = new ZipArchive();
if ($zip->open($templateFile) === true) {
    $xml = $zip->getFromName('word/document.xml');
    file_put_contents(__DIR__ . '/mau_doc.xml', $xml);
    
    // Extract plain text
    $dom = new DOMDocument();
    $dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    
    // Check page margins in sectPr
    $sectPrs = $xpath->query('//w:sectPr');
    echo "--- Section Properties in Template ---\n";
    foreach ($sectPrs as $sectPr) {
        $pgMar = $xpath->query('./w:pgMar', $sectPr);
        if ($pgMar->length > 0) {
            $m = $pgMar->item(0);
            echo "Top: " . $m->getAttribute('w:top') . " (" . round($m->getAttribute('w:top') / 567, 2) . " cm)\n";
            echo "Bottom: " . $m->getAttribute('w:bottom') . " (" . round($m->getAttribute('w:bottom') / 567, 2) . " cm)\n";
            echo "Left: " . $m->getAttribute('w:left') . " (" . round($m->getAttribute('w:left') / 567, 2) . " cm)\n";
            echo "Right: " . $m->getAttribute('w:right') . " (" . round($m->getAttribute('w:right') / 567, 2) . " cm)\n";
            echo "Header: " . $m->getAttribute('w:header') . "\n";
            echo "Footer: " . $m->getAttribute('w:footer') . "\n";
        }
    }
    
    // Extract text paragraphs
    $paras = $xpath->query('//w:p');
    echo "\n--- Paragraphs in Template (" . $paras->length . ") ---\n";
    $textList = [];
    foreach ($paras as $p) {
        $tNodes = $xpath->query('.//w:t', $p);
        $pText = '';
        foreach ($tNodes as $t) {
            $pText .= $t->nodeValue;
        }
        if (trim($pText) !== '') {
            $textList[] = trim($pText);
        }
    }
    file_put_contents(__DIR__ . '/mau_text.txt', implode("\n", $textList));
    echo "Wrote " . count($textList) . " non-empty paragraphs to mau_text.txt\n";
    
    // Check styles.xml
    $stylesXml = $zip->getFromName('word/styles.xml');
    if ($stylesXml) {
        file_put_contents(__DIR__ . '/mau_styles.xml', $stylesXml);
    }
    
    $zip->close();
} else {
    echo "Could not open $templateFile\n";
}
