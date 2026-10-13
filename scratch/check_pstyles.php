<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$paras = $xpath->query('//w:p');
$pStyles = [];
$pWithoutStyle = 0;

foreach ($paras as $p) {
    $styleNode = $xpath->query('./w:pPr/w:pStyle', $p);
    if ($styleNode->length > 0) {
        $val = $styleNode->item(0)->getAttribute('w:val');
        $pStyles[$val] = ($pStyles[$val] ?? 0) + 1;
    } else {
        $pWithoutStyle++;
    }
}
echo "Paragraph styles count:\n";
print_r($pStyles);
echo "Paragraphs without pStyle: $pWithoutStyle\n";
