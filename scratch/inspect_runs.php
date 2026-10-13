<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$runs = $xpath->query('//w:r');
echo "Total runs: " . $runs->length . "\n";

$rColors = [];
$rFonts = [];
$rSizes = [];

foreach ($runs as $r) {
    $col = $xpath->query('./w:rPr/w:color', $r);
    if ($col->length > 0) {
        $c = $col->item(0)->getAttribute('w:val');
        $rColors[$c] = ($rColors[$c] ?? 0) + 1;
    }
    $f = $xpath->query('./w:rPr/w:rFonts', $r);
    if ($f->length > 0) {
        $rf = $f->item(0)->getAttribute('w:ascii');
        $rFonts[$rf] = ($rFonts[$rf] ?? 0) + 1;
    }
    $sz = $xpath->query('./w:rPr/w:sz', $r);
    if ($sz->length > 0) {
        $s = $sz->item(0)->getAttribute('w:val');
        $rSizes[$s] = ($rSizes[$s] ?? 0) + 1;
    }
}

echo "Colors in runs:\n";
print_r($rColors);
echo "Fonts in runs:\n";
print_r($rFonts);
echo "Sizes in runs:\n";
print_r($rSizes);
