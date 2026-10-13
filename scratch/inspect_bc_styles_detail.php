<?php
$xml = file_get_contents(__DIR__ . '/bc_styles.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

echo "--- Default Document Font & Size ---\n";
$docDefaults = $xpath->query('//w:docDefaults');
if ($docDefaults->length > 0) {
    echo $dom->saveXML($docDefaults->item(0)) . "\n";
}

echo "--- Styles in Báo Cáo PHP.docx ---\n";
$styles = $xpath->query('//w:style');
foreach ($styles as $s) {
    $id = $s->getAttribute('w:styleId');
    $name = $xpath->query('./w:name', $s);
    $nameVal = $name->length ? $name->item(0)->getAttribute('w:val') : '';
    echo "Style ID: $id (Name: $nameVal)\n";
}
