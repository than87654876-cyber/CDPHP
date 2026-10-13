<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/../docs/Báo Cáo PHP.docx') === true) {
    $xml = $zip->getFromName('word/document.xml');
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    $xp = new DOMXPath($dom);
    $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    
    $sects = $xp->query('//w:sectPr');
    echo "Found " . $sects->length . " sections:\n";
    foreach ($sects as $idx => $s) {
        echo "Section $idx:\n";
        $p = $s->parentNode->parentNode;
        $t = '';
        if ($p && $p->nodeName === 'w:p') {
            foreach ($xp->query('.//w:t', $p) as $tn) $t .= $tn->nodeValue;
        }
        echo "  After paragraph text: " . mb_substr(trim($t), 0, 50) . "\n";
        $refs = $xp->query('./w:headerReference | ./w:footerReference', $s);
        echo "  Header/Footer references count: " . $refs->length . "\n";
        foreach ($refs as $r) {
            echo "    " . $r->nodeName . ": type=" . $r->getAttribute('w:type') . ", r:id=" . $r->getAttribute('r:id') . "\n";
        }
        $pgNum = $xp->query('./w:pgNumType', $s);
        if ($pgNum->length) {
            echo "  pgNumType: start=" . $pgNum->item(0)->getAttribute('w:start') . "\n";
        }
    }
    $zip->close();
}
