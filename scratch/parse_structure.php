<?php
$zip = new ZipArchive();
$filename = 'docs/HOVATEN_MSSV.docx';
if ($zip->open($filename) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    // Find all paragraphs and their style or text
    preg_match_all('/<w:p(?:\s[^>]*)?>(.*?)<\/w:p>/s', $xml, $matches);
    
    $headings = [];
    $all_paragraphs = [];
    
    foreach ($matches[1] as $p) {
        $pStyle = '';
        if (preg_match('/<w:pStyle\s+w:val="([^"]+)"/', $p, $sm)) {
            $pStyle = $sm[1];
        }
        
        $pText = strip_tags(preg_replace('/<w:tab\/>/', "\t", $p));
        $pText = trim(html_entity_decode($pText, ENT_QUOTES, 'UTF-8'));
        
        if ($pText !== '') {
            $all_paragraphs[] = ['style' => $pStyle, 'text' => $pText];
            // Check if it looks like a heading or chapter
            if (preg_match('/^(CHƯƠNG|MỤC LỤC|DANH MỤC|LỜI|PHẦN|KẾT LUẬN|TÀI LIỆU|\d+\.\d+|\d+\s+[A-ZÀ-Ỹ])/u', $pText) || 
                stripos($pStyle, 'heading') !== false || stripos($pStyle, 'tieude') !== false) {
                $headings[] = "[$pStyle] " . $pText;
            }
        }
    }
    
    file_put_contents('scratch/headings.txt', implode("\n", $headings));
    echo "Found " . count($headings) . " potential headings. Saved to scratch/headings.txt\n";
    
    // Also print first 50 headings
    for ($i = 0; $i < min(60, count($headings)); $i++) {
        echo $headings[$i] . "\n";
    }
}
