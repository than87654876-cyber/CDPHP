<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$paras = $xpath->query('//w:p');
echo "Total paragraphs: " . $paras->length . "\n";

$first50 = [];
for ($i = 0; $i < min(50, $paras->length); $i++) {
    $p = $paras->item($i);
    $text = '';
    foreach ($xpath->query('.//w:t', $p) as $t) {
        $text .= $t->nodeValue;
    }
    $pStyle = $xpath->query('./w:pPr/w:pStyle', $p);
    $style = $pStyle->length ? $pStyle->item(0)->getAttribute('w:val') : 'none';
    $jc = $xpath->query('./w:pPr/w:jc', $p);
    $align = $jc->length ? $jc->item(0)->getAttribute('w:val') : 'none';
    if (trim($text) !== '') {
        $first50[] = "[$i][Style: $style][Align: $align] " . mb_substr(trim($text), 0, 80);
    }
}
echo implode("\n", $first50) . "\n";
