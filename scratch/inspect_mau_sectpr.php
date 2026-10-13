<?php
$xml = file_get_contents(__DIR__ . '/mau_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$sectPrs = $xpath->query('//w:sectPr');
echo "Template has " . $sectPrs->length . " sections:\n";
foreach ($sectPrs as $idx => $sp) {
    echo "\n--- Section $idx ---\n";
    echo $dom->saveXML($sp) . "\n";
}
