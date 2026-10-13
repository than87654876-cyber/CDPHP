<?php
$file = __DIR__ . '/../docs/Báo Cáo PHP.docx';
if (!file_exists($file)) {
    echo "File not found: $file\n";
    exit(1);
}

$zip = new ZipArchive();
if ($zip->open($file) === true) {
    echo "Files in Báo Cáo PHP.docx:\n";
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $stat = $zip->statIndex($i);
        if (strpos($stat['name'], 'document.xml') !== false || strpos($stat['name'], 'styles.xml') !== false || strpos($stat['name'], 'header') !== false || strpos($stat['name'], 'footer') !== false) {
            echo " - " . $stat['name'] . " (" . $stat['size'] . " bytes)\n";
        }
    }
    
    $xml = $zip->getFromName('word/document.xml');
    file_put_contents(__DIR__ . '/bc_doc.xml', $xml);
    
    $dom = new DOMDocument();
    $dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    
    // Check margins
    $sectPrs = $xpath->query('//w:sectPr');
    echo "\n--- Section Margins in Báo Cáo PHP.docx ---\n";
    foreach ($sectPrs as $idx => $sectPr) {
        $pgMar = $xpath->query('./w:pgMar', $sectPr);
        if ($pgMar->length > 0) {
            $m = $pgMar->item(0);
            echo "Section $idx:\n";
            echo "  Top: " . $m->getAttribute('w:top') . " (" . round($m->getAttribute('w:top') / 567, 2) . " cm)\n";
            echo "  Bottom: " . $m->getAttribute('w:bottom') . " (" . round($m->getAttribute('w:bottom') / 567, 2) . " cm)\n";
            echo "  Left: " . $m->getAttribute('w:left') . " (" . round($m->getAttribute('w:left') / 567, 2) . " cm)\n";
            echo "  Right: " . $m->getAttribute('w:right') . " (" . round($m->getAttribute('w:right') / 567, 2) . " cm)\n";
        }
    }
    
    // Check fonts, sizes, alignments in paragraphs
    $paras = $xpath->query('//w:p');
    echo "\nTotal paragraphs: " . $paras->length . "\n";
    
    $fonts = [];
    $sizes = [];
    $aligns = [];
    $spacings = [];
    
    foreach ($paras as $p) {
        $jc = $xpath->query('./w:pPr/w:jc', $p);
        if ($jc->length > 0) {
            $val = $jc->item(0)->getAttribute('w:val');
            $aligns[$val] = ($aligns[$val] ?? 0) + 1;
        }
        $sp = $xpath->query('./w:pPr/w:spacing', $p);
        if ($sp->length > 0) {
            $line = $sp->item(0)->getAttribute('w:line');
            $spacings[$line] = ($spacings[$line] ?? 0) + 1;
        }
        $rFonts = $xpath->query('.//w:rPr/w:rFonts', $p);
        foreach ($rFonts as $rf) {
            $ascii = $rf->getAttribute('w:ascii') ?: $rf->getAttribute('w:hAnsi');
            if ($ascii) $fonts[$ascii] = ($fonts[$ascii] ?? 0) + 1;
        }
        $szs = $xpath->query('.//w:rPr/w:sz', $p);
        foreach ($szs as $sz) {
            $val = $sz->getAttribute('w:val');
            $sizes[$val] = ($sizes[$val] ?? 0) + 1;
        }
    }
    
    echo "\nFonts used:\n";
    print_r($fonts);
    echo "\nFont sizes used (in half-points, e.g. 26 = 13pt, 28 = 14pt):\n";
    print_r($sizes);
    echo "\nAlignments used:\n";
    print_r($aligns);
    echo "\nLine spacings used:\n";
    print_r($spacings);
    
    $zip->close();
}
