<?php
$files = glob(__DIR__ . '/../docs/*.*');
foreach ($files as $f) {
    echo basename($f) . "\n";
}

$hovaTen = __DIR__ . '/../docs/HOVATEN_MSSV.docx';
if (file_exists($hovaTen)) {
    $zip = new ZipArchive();
    if ($zip->open($hovaTen) === true) {
        $xml = $zip->getFromName('word/document.xml');
        $dom = new DOMDocument();
        $dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        echo "\nFirst 20 paragraphs of HOVATEN_MSSV.docx:\n";
        $ps = $xp->query('//w:p');
        for ($i = 0; $i < min(20, $ps->length); $i++) {
            $t = '';
            foreach ($xp->query('.//w:t', $ps->item($i)) as $node) $t .= $node->nodeValue;
            if (trim($t)) echo "[$i] " . trim($t) . "\n";
        }
        $zip->close();
    }
}
