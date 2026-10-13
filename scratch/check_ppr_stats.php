<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$paras = $xpath->query('//w:p');

$stats = [
    'has_jc' => 0,
    'no_jc' => 0,
    'has_spacing' => 0,
    'no_spacing' => 0,
    'has_ind' => 0,
    'no_ind' => 0
];

foreach ($paras as $p) {
    $jc = $xpath->query('./w:pPr/w:jc', $p);
    if ($jc->length > 0) $stats['has_jc']++; else $stats['no_jc']++;
    
    $sp = $xpath->query('./w:pPr/w:spacing', $p);
    if ($sp->length > 0) $stats['has_spacing']++; else $stats['no_spacing']++;
    
    $ind = $xpath->query('./w:pPr/w:ind', $p);
    if ($ind->length > 0) $stats['has_ind']++; else $stats['no_ind']++;
}

print_r($stats);
