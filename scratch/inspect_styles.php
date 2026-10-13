<?php
$file = __DIR__ . '/../docs/Báo Cáo PHP.docx';
$zip = new ZipArchive();
if ($zip->open($file) === true) {
    $stylesXml = $zip->getFromName('word/styles.xml');
    file_put_contents(__DIR__ . '/bc_styles.xml', $stylesXml);
    
    $header1 = $zip->getFromName('word/header1.xml');
    if ($header1) file_put_contents(__DIR__ . '/bc_header1.xml', $header1);
    
    $footer1 = $zip->getFromName('word/footer1.xml');
    if ($footer1) file_put_contents(__DIR__ . '/bc_footer1.xml', $footer1);
    
    $zip->close();
    echo "Extracted styles.xml, header1.xml, footer1.xml\n";
}
