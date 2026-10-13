<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$paras = $xpath->query('//w:p');

for ($i = 0; $i <= 12; $i++) {
    $p = $paras->item($i);
    $text = '';
    foreach ($xpath->query('.//w:t', $p) as $t) {
        $text .= $t->nodeValue;
    }
    echo "P[$i]: " . trim($text) . "\n";
    $jc = $xpath->query('./w:pPr/w:jc', $p);
    if ($jc->length) echo "  jc: " . $jc->item(0)->getAttribute('w:val') . "\n";
    $sz = $xpath->query('.//w:rPr/w:sz', $p);
    if ($sz->length) echo "  sz: " . $sz->item(0)->getAttribute('w:val') . "\n";
}
