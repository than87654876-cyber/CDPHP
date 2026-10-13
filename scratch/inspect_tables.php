<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$tables = $xpath->query('//w:tbl');
echo "Total tables: " . $tables->length . "\n";

foreach ($tables as $idx => $tbl) {
    $rows = $xpath->query('./w:tr', $tbl);
    $tblPr = $xpath->query('./w:tblPr', $tbl);
    $jc = $xpath->query('./w:tblPr/w:jc', $tbl);
    $align = $jc->length ? $jc->item(0)->getAttribute('w:val') : 'none';
    echo "Table $idx: " . $rows->length . " rows, align: $align\n";
}
