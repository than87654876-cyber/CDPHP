<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$drawings = $xpath->query('//w:p[.//w:drawing]');
echo "Paragraphs with drawings: " . $drawings->length . "\n";
for ($i = 0; $i < min(10, $drawings->length); $i++) {
    $p = $drawings->item($i);
    $text = '';
    foreach ($xpath->query('.//w:t', $p) as $t) $text .= $t->nodeValue;
    echo "Drawing P $i text: " . trim($text) . "\n";
}
