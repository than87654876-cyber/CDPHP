<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xp = new DOMXPath($dom);
$xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
echo $dom->saveXML($xp->query('//w:p')->item(27)) . "\n";
