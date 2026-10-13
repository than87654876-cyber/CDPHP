<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/../docs/Báo Cáo PHP.docx') === true) {
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (substr($name, -4) === '.xml' || substr($name, -5) === '.rels') {
            $content = $zip->getFromIndex($i);
            $dom = new DOMDocument();
            $prevError = libxml_use_internal_errors(true);
            $loaded = $dom->loadXML($content);
            $errors = libxml_get_errors();
            libxml_use_internal_errors($prevError);
            if (!$loaded) {
                echo "ERROR in $name:\n";
                foreach ($errors as $e) {
                    echo "  " . trim($e->message) . "\n";
                }
            }
        }
    }
    echo "XML Validation finished. No errors found!\n";
    $zip->close();
}
