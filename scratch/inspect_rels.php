<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/../docs/Báo Cáo PHP.docx') === true) {
    $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
    file_put_contents(__DIR__ . '/bc_rels.xml', $relsXml);
    $dom = new DOMDocument();
    $dom->loadXML($relsXml);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/package/2006/relationships');
    
    echo "Relationships in document.xml.rels:\n";
    foreach ($xpath->query('//r:Relationship') as $rel) {
        $id = $rel->getAttribute('Id');
        $type = basename($rel->getAttribute('Type'));
        $target = $rel->getAttribute('Target');
        if (strpos($type, 'header') !== false || strpos($type, 'footer') !== false) {
            echo "  $id: $type -> $target\n";
        }
    }
    $zip->close();
}
