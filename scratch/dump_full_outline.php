<?php
$text = file_get_contents('scratch/extracted_hovaten.txt');

// Let's print out lines that start with numbers or chapter titles
$lines = explode("\n", $text);
$outline = [];
foreach ($lines as $line) {
    $trimmed = trim($line);
    if (preg_match('/^(CHƯƠNG|Chương|\d+\.\d+|LỜI|MỤC LỤC|DANH MỤC|PHỤ LỤC|KẾT LUẬN|TÀI LIỆU)/u', $trimmed)) {
        if (strlen($trimmed) < 150) {
            $outline[] = $trimmed;
        }
    }
}

file_put_contents('scratch/exact_outline.txt', implode("\n", $outline));
echo "Found " . count($outline) . " outline items.\n";
foreach (array_slice($outline, 0, 70) as $item) {
    echo $item . "\n";
}
