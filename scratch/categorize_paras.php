<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$paras = $xpath->query('//w:p');
echo "Total paragraphs: " . $paras->length . "\n";

$categories = [
    'cover' => [],
    'major_title' => [],
    'chapter' => [],
    'section_h2' => [],
    'subsection_h3' => [],
    'subsubsection_h4' => [],
    'caption' => [],
    'bullet' => [],
    'body' => [],
    'empty' => []
];

$inCover = true;

for ($i = 0; $i < $paras->length; $i++) {
    $p = $paras->item($i);
    $text = '';
    foreach ($xpath->query('.//w:t', $p) as $t) {
        $text .= $t->nodeValue;
    }
    $trimmed = trim($text);
    if ($trimmed === '') {
        $categories['empty'][] = $i;
        continue;
    }
    
    // Check if within table cell
    $isCell = false;
    $curr = $p->parentNode;
    while ($curr) {
        if ($curr->nodeName === 'w:tc') {
            $isCell = true;
            break;
        }
        $curr = $curr->parentNode;
    }
    
    if ($inCover) {
        $categories['cover'][] = [$i, $trimmed];
        if (strpos($trimmed, 'TP. Hồ Chí Minh') !== false) {
            $inCover = false;
        }
        continue;
    }
    
    // Major titles
    if (preg_match('/^(LỜI CẢM ƠN|LỜI CAM ĐOAN|MỤC LỤC|DANH MỤC|LỜI MỞ ĐẦU|KẾT LUẬN|TÀI LIỆU THAM KHẢO|PHỤ LỤC)/iu', $trimmed)) {
        $categories['major_title'][] = [$i, $trimmed];
    } elseif (preg_match('/^Chương\s+[0-9IVX]+/iu', $trimmed)) {
        $categories['chapter'][] = [$i, $trimmed];
    } elseif (preg_match('/^[0-9]+\.[0-9]+\s+[^\.]+/u', $trimmed) && !preg_match('/^[0-9]+\.[0-9]+\.[0-9]+/', $trimmed)) {
        $categories['section_h2'][] = [$i, $trimmed];
    } elseif (preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\s+/u', $trimmed) || preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\./u', $trimmed)) {
        if (preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/u', $trimmed)) {
            $categories['subsubsection_h4'][] = [$i, $trimmed];
        } else {
            $categories['subsection_h3'][] = [$i, $trimmed];
        }
    } elseif (preg_match('/^(Hình|Bảng|Sơ đồ|Biểu đồ|Lược đồ)\s+[0-9]+/iu', $trimmed)) {
        $categories['caption'][] = [$i, $trimmed];
    } elseif (preg_match('/^[-\–\•\*\+]\s+/u', $trimmed) || preg_match('/^[a-z]\)\s+/u', $trimmed)) {
        $categories['bullet'][] = [$i, $trimmed];
    } else {
        $categories['body'][] = [$i, mb_substr($trimmed, 0, 60), $isCell ? 'cell' : 'p'];
    }
}

echo "Summary:\n";
echo "Cover: " . count($categories['cover']) . "\n";
echo "Major Titles: " . count($categories['major_title']) . "\n";
echo "Chapters: " . count($categories['chapter']) . "\n";
echo "Section H2: " . count($categories['section_h2']) . "\n";
echo "Subsection H3: " . count($categories['subsection_h3']) . "\n";
echo "Subsubsection H4: " . count($categories['subsubsection_h4']) . "\n";
echo "Captions: " . count($categories['caption']) . "\n";
echo "Bullets: " . count($categories['bullet']) . "\n";
echo "Body paragraphs: " . count($categories['body']) . "\n";
echo "Empty paragraphs: " . count($categories['empty']) . "\n";

echo "\n--- Major Titles Sample ---\n";
foreach ($categories['major_title'] as $item) echo "[$item[0]] $item[1]\n";

echo "\n--- Chapters Sample ---\n";
foreach ($categories['chapter'] as $item) echo "[$item[0]] $item[1]\n";

echo "\n--- Section H2 Sample (first 10) ---\n";
foreach (array_slice($categories['section_h2'], 0, 10) as $item) echo "[$item[0]] $item[1]\n";
