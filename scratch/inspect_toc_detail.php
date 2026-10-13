<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$paras = $xpath->query('//w:p');

for ($i = 26; $i <= 40; $i++) {
    $p = $paras->item($i);
    $text = '';
    foreach ($xpath->query('.//w:t', $p) as $t) {
        $text .= $t->nodeValue;
    }
    echo "P[$i]: " . trim($text) . "\n";
    $pPr = $xpath->query('./w:pPr', $p);
    if ($pPr->length) {
        echo "  pPr: " . $dom->saveXML($pPr->item(0)) . "\n";
    }
}
