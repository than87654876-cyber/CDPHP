<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$brs = $xpath->query('//w:br[@w:type="page"]');
echo "Page breaks count: " . $brs->length . "\n";
foreach ($brs as $idx => $br) {
    // Find text near this page break
    $p = $br->parentNode;
    while ($p && $p->nodeName !== 'w:p') $p = $p->parentNode;
    $t = '';
    if ($p) {
        foreach ($xpath->query('.//w:t', $p) as $tn) $t .= $tn->nodeValue;
    }
    echo "PageBreak $idx near: " . mb_substr(trim($t), 0, 40) . "\n";
}
