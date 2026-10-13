<?php
$f = __DIR__ . '/../docs/Hướng phát triển nhóm 10.docx';
if (file_exists($f)) {
    $zip = new ZipArchive();
    if ($zip->open($f) === true) {
        $xml = $zip->getFromName('word/document.xml');
        $dom = new DOMDocument();
        $dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        echo "Paragraphs of Hướng phát triển nhóm 10.docx:\n";
        $ps = $xp->query('//w:p');
        for ($i = 0; $i < min(20, $ps->length); $i++) {
            $t = '';
            foreach ($xp->query('.//w:t', $ps->item($i)) as $node) $t .= $node->nodeValue;
            if (trim($t)) echo "[$i] " . trim($t) . "\n";
        }
        $zip->close();
    }
}
