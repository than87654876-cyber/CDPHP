<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

// Find all sectPr
$body = $xpath->query('//w:body')->item(0);
$bodySectPr = $xpath->query('./w:sectPr', $body);
echo "Body sectPr count: " . $bodySectPr->length . "\n";

$pSectPr = $xpath->query('//w:pPr/w:sectPr');
echo "Paragraph sectPr count: " . $pSectPr->length . "\n";

foreach ($pSectPr as $idx => $sp) {
    $parentP = $sp->parentNode->parentNode;
    $pText = '';
    foreach ($xpath->query('.//w:t', $parentP) as $t) {
        $pText .= $t->nodeValue;
    }
    echo "pSectPr $idx after text: " . mb_substr(trim($pText), 0, 50) . "\n";
}

// Let's check headers and footers references in sectPr
$allSectPr = $xpath->query('//w:sectPr');
foreach ($allSectPr as $idx => $sp) {
    echo "\nSectPr $idx references:\n";
    foreach ($xpath->query('./w:headerReference | ./w:footerReference', $sp) as $ref) {
        echo "  " . $ref->nodeName . ": type=" . $ref->getAttribute('w:type') . ", r:id=" . $ref->getAttribute('r:id') . "\n";
    }
}
