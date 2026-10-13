<?php
// Script to apply exact formatting rules from Mẫu hướng dẫn trình bày báo cáo (4).docx to Báo Cáo PHP.docx

$sourceDocx = __DIR__ . '/../docs/Báo Cáo PHP.docx';
$backupDocx = __DIR__ . '/../docs/Báo Cáo PHP_backup.docx';
$outputDocx = __DIR__ . '/../docs/Báo Cáo PHP_formatted.docx';

// Make sure backup exists
if (!file_exists($backupDocx)) {
    copy($sourceDocx, $backupDocx);
}

// 1. Copy source to output
copy($sourceDocx, $outputDocx);

$zip = new ZipArchive();
if ($zip->open($outputDocx) !== true) {
    die("Cannot open $outputDocx\n");
}

// Extract styles.xml, document.xml, header1.xml, footer1.xml
$stylesXml = $zip->getFromName('word/styles.xml');
$docXml = $zip->getFromName('word/document.xml');
$headerXml = $zip->getFromName('word/header1.xml');
$footerXml = $zip->getFromName('word/footer1.xml');

// ==========================================
// A. UPDATE word/styles.xml
// ==========================================
$stylesDom = new DOMDocument();
$stylesDom->loadXML($stylesXml);
$stylesXp = new DOMXPath($stylesDom);
$stylesXp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

// Update docDefaults
$docDefaults = $stylesXp->query('//w:docDefaults')->item(0);
if ($docDefaults) {
    // rPrDefault
    $rPr = $stylesXp->query('.//w:rPrDefault/w:rPr', $docDefaults)->item(0);
    if ($rPr) {
        $rFonts = $stylesXp->query('./w:rFonts', $rPr)->item(0);
        if ($rFonts) {
            $rFonts->setAttribute('w:ascii', 'Times New Roman');
            $rFonts->setAttribute('w:eastAsia', 'Times New Roman');
            $rFonts->setAttribute('w:hAnsi', 'Times New Roman');
            $rFonts->setAttribute('w:cs', 'Times New Roman');
        }
        $sz = $stylesXp->query('./w:sz', $rPr)->item(0);
        if ($sz) $sz->setAttribute('w:val', '26');
        $szCs = $stylesXp->query('./w:szCs', $rPr)->item(0);
        if ($szCs) $szCs->setAttribute('w:val', '26');
        $color = $stylesXp->query('./w:color', $rPr)->item(0);
        if ($color) $color->setAttribute('w:val', '000000');
    }
    // pPrDefault
    $pPr = $stylesXp->query('.//w:pPrDefault/w:pPr', $docDefaults)->item(0);
    if ($pPr) {
        $spacing = $stylesXp->query('./w:spacing', $pPr)->item(0);
        if ($spacing) {
            $spacing->setAttribute('w:before', '0');
            $spacing->setAttribute('w:after', '120'); // 6pt
            $spacing->setAttribute('w:line', '360'); // 1.5 lines
            $spacing->setAttribute('w:lineRule', 'auto');
        }
    }
}

// Update or ensure Styles
function updateStyle($stylesDom, $stylesXp, $styleId, $szVal, $bold, $colorVal, $spacingBefore, $spacingAfter, $jcVal, $italic = false) {
    $styleNode = $stylesXp->query("//w:style[@w:styleId='$styleId']")->item(0);
    if ($styleNode) {
        $rPr = $stylesXp->query('./w:rPr', $styleNode)->item(0);
        if (!$rPr) {
            $rPr = $stylesDom->createElement('w:rPr');
            $styleNode->appendChild($rPr);
        }
        
        // Font
        $rFonts = $stylesXp->query('./w:rFonts', $rPr)->item(0);
        if (!$rFonts) {
            $rFonts = $stylesDom->createElement('w:rFonts');
            $rPr->appendChild($rFonts);
        }
        $rFonts->setAttribute('w:ascii', 'Times New Roman');
        $rFonts->setAttribute('w:hAnsi', 'Times New Roman');
        $rFonts->setAttribute('w:cs', 'Times New Roman');
        
        // Size
        $sz = $stylesXp->query('./w:sz', $rPr)->item(0);
        if (!$sz) {
            $sz = $stylesDom->createElement('w:sz');
            $rPr->appendChild($sz);
        }
        $sz->setAttribute('w:val', $szVal);
        
        $szCs = $stylesXp->query('./w:szCs', $rPr)->item(0);
        if (!$szCs) {
            $szCs = $stylesDom->createElement('w:szCs');
            $rPr->appendChild($szCs);
        }
        $szCs->setAttribute('w:val', $szVal);
        
        // Bold
        $b = $stylesXp->query('./w:b', $rPr)->item(0);
        if ($bold) {
            if (!$b) $rPr->appendChild($stylesDom->createElement('w:b'));
            $bCs = $stylesXp->query('./w:bCs', $rPr)->item(0);
            if (!$bCs) $rPr->appendChild($stylesDom->createElement('w:bCs'));
        } else {
            if ($b) $rPr->removeChild($b);
            $bCs = $stylesXp->query('./w:bCs', $rPr)->item(0);
            if ($bCs) $rPr->removeChild($bCs);
        }
        
        // Italic
        $i = $stylesXp->query('./w:i', $rPr)->item(0);
        if ($italic) {
            if (!$i) $rPr->appendChild($stylesDom->createElement('w:i'));
            $iCs = $stylesXp->query('./w:iCs', $rPr)->item(0);
            if (!$iCs) $rPr->appendChild($stylesDom->createElement('w:iCs'));
        }
        
        // Color
        $color = $stylesXp->query('./w:color', $rPr)->item(0);
        if (!$color) {
            $color = $stylesDom->createElement('w:color');
            $rPr->appendChild($color);
        }
        $color->setAttribute('w:val', $colorVal);
        
        // pPr
        $pPr = $stylesXp->query('./w:pPr', $styleNode)->item(0);
        if (!$pPr) {
            $pPr = $stylesDom->createElement('w:pPr');
            $styleNode->appendChild($pPr);
        }
        
        $spacing = $stylesXp->query('./w:spacing', $pPr)->item(0);
        if (!$spacing) {
            $spacing = $stylesDom->createElement('w:spacing');
            $pPr->appendChild($spacing);
        }
        $spacing->setAttribute('w:before', $spacingBefore);
        $spacing->setAttribute('w:after', $spacingAfter);
        $spacing->setAttribute('w:line', '360');
        $spacing->setAttribute('w:lineRule', 'auto');
        
        if ($jcVal) {
            $jc = $stylesXp->query('./w:jc', $pPr)->item(0);
            if (!$jc) {
                $jc = $stylesDom->createElement('w:jc');
                $pPr->appendChild($jc);
            }
            $jc->setAttribute('w:val', $jcVal);
        }
    }
}

// Update Normal: Size 13pt (26), regular, black, 1.5 lines, both
updateStyle($stylesDom, $stylesXp, 'Normal', '26', false, '000000', '0', '120', 'both');
// Update Heading1: Size 16pt (32), bold, black, before 240, after 120, center
updateStyle($stylesDom, $stylesXp, 'Heading1', '32', true, '000000', '240', '120', 'center');
// Update Heading2: Size 14pt (28), bold, black, before 180, after 60, left
updateStyle($stylesDom, $stylesXp, 'Heading2', '28', true, '000000', '180', '60', 'left');
// Update Heading3: Size 13pt (26), bold, black, before 120, after 60, left
updateStyle($stylesDom, $stylesXp, 'Heading3', '26', true, '000000', '120', '60', 'left');
// Update Heading4: Size 13pt (26), bold+italic, black, before 120, after 60, left, true
updateStyle($stylesDom, $stylesXp, 'Heading4', '26', true, '000000', '120', '60', 'left', true);
// Update Caption: Size 12pt (24), italic, black, before 60, after 180, center, true
updateStyle($stylesDom, $stylesXp, 'Caption', '24', false, '000000', '60', '180', 'center', true);

// ==========================================
// B. UPDATE word/header1.xml & word/footer1.xml
// ==========================================
// Header: Left: Tên đề tài, Right: GVHD (Times New Roman, 11pt, Color: 000000)
$newHeaderXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:hdr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:p>
    <w:pPr>
      <w:pBdr>
        <w:bottom w:val="single" w:sz="6" w:space="4" w:color="CCCCCC"/>
      </w:pBdr>
      <w:tabs>
        <w:tab w:val="right" w:pos="9071"/>
      </w:tabs>
      <w:spacing w:after="120" w:line="240" w:lineRule="auto"/>
      <w:jc w:val="left"/>
    </w:pPr>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:i/><w:iCs/>
        <w:color w:val="333333"/>
        <w:sz w:val="22"/>
        <w:szCs w:val="22"/>
      </w:rPr>
      <w:t>Đề tài: Xây dựng hệ thống giao đồ ăn trực tuyến FOODDAILY</w:t>
    </w:r>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:i/><w:iCs/>
        <w:color w:val="333333"/>
        <w:sz w:val="22"/>
        <w:szCs w:val="22"/>
      </w:rPr>
      <w:tab/>
      <w:t>GVHD: KS. Huỳnh Luân</w:t>
    </w:r>
  </w:p>
</w:hdr>';

// Footer: Left: SVTH: Trần Lê Thân, Right: Trang {PAGE} (Times New Roman, 11pt)
$newFooterXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:ftr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:p>
    <w:pPr>
      <w:pBdr>
        <w:top w:val="single" w:sz="6" w:space="4" w:color="CCCCCC"/>
      </w:pBdr>
      <w:tabs>
        <w:tab w:val="right" w:pos="9071"/>
      </w:tabs>
      <w:spacing w:before="120" w:line="240" w:lineRule="auto"/>
      <w:jc w:val="left"/>
    </w:pPr>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:color w:val="333333"/>
        <w:sz w:val="22"/>
        <w:szCs w:val="22"/>
      </w:rPr>
      <w:t>SVTH: Trần Lê Thân</w:t>
    </w:r>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:color w:val="333333"/>
        <w:sz w:val="22"/>
        <w:szCs w:val="22"/>
      </w:rPr>
      <w:tab/>
      <w:t xml:space="preserve">Trang </w:t>
    </w:r>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:color w:val="333333"/>
        <w:sz w:val="22"/>
        <w:szCs w:val="22"/>
      </w:rPr>
      <w:fldChar w:fldCharType="begin"/>
    </w:r>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:color w:val="333333"/>
        <w:sz w:val="22"/>
        <w:szCs w:val="22"/>
      </w:rPr>
      <w:instrText xml:space="preserve"> PAGE </w:instrText>
    </w:r>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:color w:val="333333"/>
        <w:sz w:val="22"/>
        <w:szCs w:val="22"/>
      </w:rPr>
      <w:fldChar w:fldCharType="separate"/>
    </w:r>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:color w:val="333333"/>
        <w:sz w:val="22"/>
        <w:szCs w:val="22"/>
      </w:rPr>
      <w:fldChar w:fldCharType="end"/>
    </w:r>
  </w:p>
</w:ftr>';

// ==========================================
// C. PROCESS word/document.xml
// ==========================================
$docDom = new DOMDocument();
$docDom->loadXML($docXml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
$docXp = new DOMXPath($docDom);
$docXp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

// 1. Process Section Margins on all sectPr
$sectPrs = $docXp->query('//w:sectPr');
foreach ($sectPrs as $sp) {
    // Page Size A4
    $pgSz = $docXp->query('./w:pgSz', $sp)->item(0);
    if (!$pgSz) {
        $pgSz = $docDom->createElement('w:pgSz');
        $sp->appendChild($pgSz);
    }
    $pgSz->setAttribute('w:w', '11906');
    $pgSz->setAttribute('w:h', '16838');
    
    // Page Margins: Top: 2.0cm (1134), Bottom: 2.0cm (1134), Left: 3.0cm (1701), Right: 2.0cm (1134)
    $pgMar = $docXp->query('./w:pgMar', $sp)->item(0);
    if (!$pgMar) {
        $pgMar = $docDom->createElement('w:pgMar');
        $sp->appendChild($pgMar);
    }
    $pgMar->setAttribute('w:top', '1134');
    $pgMar->setAttribute('w:bottom', '1134');
    $pgMar->setAttribute('w:left', '1701');
    $pgMar->setAttribute('w:right', '1134');
    $pgMar->setAttribute('w:header', '720');
    $pgMar->setAttribute('w:footer', '720');
    $pgMar->setAttribute('w:gutter', '0');
}

// Center all tables
$tables = $docXp->query('//w:tbl');
foreach ($tables as $tbl) {
    $tblPr = $docXp->query('./w:tblPr', $tbl)->item(0);
    if ($tblPr) {
        $jc = $docXp->query('./w:jc', $tblPr)->item(0);
        if (!$jc) {
            $jc = $docDom->createElement('w:jc');
            $tblPr->appendChild($jc);
        }
        $jc->setAttribute('w:val', 'center');
    }
}

// 2. Classify and format each paragraph
$paras = $docXp->query('//w:p');
echo "Processing " . $paras->length . " paragraphs in document.xml...\n";

$inCover = true;
$inTOC = false;

for ($i = 0; $i < $paras->length; $i++) {
    $p = $paras->item($i);
    
    // Extract plain text
    $text = '';
    foreach ($docXp->query('.//w:t', $p) as $t) {
        $text .= $t->nodeValue;
    }
    $trimmed = trim($text);
    
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
    
    // Check if paragraph contains drawing/image
    $hasDrawing = $docXp->query('.//w:drawing', $p)->length > 0;
    
    // Get or create pPr
    $pPr = $docXp->query('./w:pPr', $p)->item(0);
    if (!$pPr) {
        $pPr = $docDom->createElement('w:pPr');
        $p->insertBefore($pPr, $p->firstChild);
    }
    
    // Helper function to set pPr attributes
    $setParaProps = function($jcVal, $beforeVal, $afterVal, $lineVal, $firstLineVal = 0, $styleVal = null, $pageBreakBefore = false, $leftIndent = 0, $hanging = 0) use ($docDom, $docXp, $pPr) {
        // Style
        if ($styleVal) {
            $pStyle = $docXp->query('./w:pStyle', $pPr)->item(0);
            if (!$pStyle) {
                $pStyle = $docDom->createElement('w:pStyle');
                $pPr->insertBefore($pStyle, $pPr->firstChild);
            }
            $pStyle->setAttribute('w:val', $styleVal);
        }
        
        // Alignment
        if ($jcVal) {
            $jc = $docXp->query('./w:jc', $pPr)->item(0);
            if (!$jc) {
                $jc = $docDom->createElement('w:jc');
                $pPr->appendChild($jc);
            }
            $jc->setAttribute('w:val', $jcVal);
        }
        
        // Spacing
        $spacing = $docXp->query('./w:spacing', $pPr)->item(0);
        if (!$spacing) {
            $spacing = $docDom->createElement('w:spacing');
            $pPr->appendChild($spacing);
        }
        $spacing->setAttribute('w:before', (string)$beforeVal);
        $spacing->setAttribute('w:after', (string)$afterVal);
        $spacing->setAttribute('w:line', (string)$lineVal);
        $spacing->setAttribute('w:lineRule', 'auto');
        
        // Indentation
        $ind = $docXp->query('./w:ind', $pPr)->item(0);
        if ($firstLineVal > 0 || $leftIndent > 0 || $hanging > 0) {
            if (!$ind) {
                $ind = $docDom->createElement('w:ind');
                $pPr->appendChild($ind);
            }
            if ($firstLineVal > 0) $ind->setAttribute('w:firstLine', (string)$firstLineVal);
            if ($leftIndent > 0) $ind->setAttribute('w:left', (string)$leftIndent);
            if ($hanging > 0) $ind->setAttribute('w:hanging', (string)$hanging);
        } elseif ($ind) {
            // Remove firstLine if not applicable
            $ind->removeAttribute('w:firstLine');
        }
        
        // Page break before
        if ($pageBreakBefore) {
            $pbb = $docXp->query('./w:pageBreakBefore', $pPr)->item(0);
            if (!$pbb) {
                $pbb = $docDom->createElement('w:pageBreakBefore');
                $pPr->appendChild($pbb);
            }
        }
    };
    
    // Helper to format all runs in this paragraph
    $formatRuns = function($szVal, $bold = null, $italic = null, $colorVal = '000000') use ($docDom, $docXp, $p) {
        $runs = $docXp->query('.//w:r', $p);
        foreach ($runs as $r) {
            $rPr = $docXp->query('./w:rPr', $r)->item(0);
            if (!$rPr) {
                $rPr = $docDom->createElement('w:rPr');
                $r->insertBefore($rPr, $r->firstChild);
            }
            
            // Fonts: Times New Roman
            $rFonts = $docXp->query('./w:rFonts', $rPr)->item(0);
            if (!$rFonts) {
                $rFonts = $docDom->createElement('w:rFonts');
                $rPr->appendChild($rFonts);
            }
            $rFonts->setAttribute('w:ascii', 'Times New Roman');
            $rFonts->setAttribute('w:eastAsia', 'Times New Roman');
            $rFonts->setAttribute('w:hAnsi', 'Times New Roman');
            $rFonts->setAttribute('w:cs', 'Times New Roman');
            
            // Size
            $sz = $docXp->query('./w:sz', $rPr)->item(0);
            if (!$sz) {
                $sz = $docDom->createElement('w:sz');
                $rPr->appendChild($sz);
            }
            $sz->setAttribute('w:val', (string)$szVal);
            
            $szCs = $docXp->query('./w:szCs', $rPr)->item(0);
            if (!$szCs) {
                $szCs = $docDom->createElement('w:szCs');
                $rPr->appendChild($szCs);
            }
            $szCs->setAttribute('w:val', (string)$szVal);
            
            // Color
            $col = $docXp->query('./w:color', $rPr)->item(0);
            if (!$col) {
                $col = $docDom->createElement('w:color');
                $rPr->appendChild($col);
            }
            $col->setAttribute('w:val', $colorVal);
            
            // Bold
            if ($bold !== null) {
                $b = $docXp->query('./w:b', $rPr)->item(0);
                if ($bold) {
                    if (!$b) $rPr->appendChild($docDom->createElement('w:b'));
                    $bCs = $docXp->query('./w:bCs', $rPr)->item(0);
                    if (!$bCs) $rPr->appendChild($docDom->createElement('w:bCs'));
                } else {
                    if ($b) $rPr->removeChild($b);
                    $bCs = $docXp->query('./w:bCs', $rPr)->item(0);
                    if ($bCs) $rPr->removeChild($bCs);
                }
            }
            
            // Italic
            if ($italic !== null) {
                $it = $docXp->query('./w:i', $rPr)->item(0);
                if ($italic) {
                    if (!$it) $rPr->appendChild($docDom->createElement('w:i'));
                    $iCs = $docXp->query('./w:iCs', $rPr)->item(0);
                    if (!$iCs) $rPr->appendChild($docDom->createElement('w:iCs'));
                } else {
                    if ($it) $rPr->removeChild($it);
                    $iCs = $docXp->query('./w:iCs', $rPr)->item(0);
                    if ($iCs) $rPr->removeChild($iCs);
                }
            }
        }
    };
    
    // --- 1. COVER PAGE ---
    if ($inCover) {
        if (strpos($trimmed, 'TRƯỜNG') !== false) {
            $setParaProps('center', 0, 120, 260);
            $formatRuns('32', true, false, '000000'); // 16pt Bold
        } elseif (strpos($trimmed, 'KHOA') !== false) {
            $setParaProps('center', 0, 360, 260);
            $formatRuns('28', true, false, '000000'); // 14pt Bold
        } elseif (strpos($trimmed, 'ĐỒ ÁN') !== false || strpos($trimmed, 'BÁO CÁO') !== false) {
            $setParaProps('center', 0, 120, 260);
            $formatRuns('32', true, false, '000000'); // 16pt Bold
        } elseif (strpos($trimmed, 'CHUYÊN NGÀNH') !== false) {
            $setParaProps('center', 0, 480, 260);
            $formatRuns('28', true, false, '000000'); // 14pt Bold
        } elseif (strpos($trimmed, 'ĐỀ TÀI:') !== false) {
            $setParaProps('center', 0, 120, 260);
            $formatRuns('32', true, false, '000000'); // 16pt Bold
        } elseif (strpos($trimmed, 'FOODDAILY') !== false || strpos($trimmed, 'XÂY DỰNG') !== false) {
            $setParaProps('center', 0, 720, 280);
            $formatRuns('36', true, false, '000000'); // 18pt Bold
        } elseif (preg_match('/^(Giảng viên|Sinh viên|Lớp|Khoá)/iu', $trimmed)) {
            $setParaProps('left', 60, 60, 260, 0, null, false, 1500); // indent 1500 to align nicely
            $formatRuns('28', true, false, '000000'); // 14pt Bold
        } elseif (strpos($trimmed, 'TP. Hồ Chí Minh') !== false) {
            $setParaProps('center', 720, 0, 260);
            $formatRuns('26', true, false, '000000'); // 13pt Bold
            $inCover = false; // Cover ends here
        }
        continue;
    }
    
    // --- 2. TABLE OF CONTENTS TRACKING ---
    if ($trimmed === 'MỤC LỤC') {
        $inTOC = true;
        $setParaProps('center', 240, 180, 360, 0, null, true);
        $formatRuns('32', true, false, '000000'); // 16pt Bold
        continue;
    }
    if ($inTOC && $trimmed === 'DANH MỤC SƠ ĐỒ, LƯỢC ĐỒ, BIỂU ĐỒ, HÌNH') {
        $inTOC = false; // TOC list ends, next is List of Figures
    }
    
    // If inside TOC lines or Danh mục lines
    if ($inTOC || (preg_match('/^(Hình|Bảng|Sơ đồ|Biểu đồ|Lược đồ)\s+[0-9]/u', $trimmed) && $i < 139) || (preg_match('/^(Chương\s+[0-9]|[0-9]+\.[0-9]+|LỜI)/u', $trimmed) && $i < 139)) {
        // Add tab right dot leader
        $tabs = $docXp->query('./w:tabs', $pPr)->item(0);
        if (!$tabs) {
            $tabs = $docDom->createElement('w:tabs');
            $pPr->appendChild($tabs);
        }
        $tab = $docXp->query('./w:tab', $tabs)->item(0);
        if (!$tab) {
            $tab = $docDom->createElement('w:tab');
            $tabs->appendChild($tab);
        }
        $tab->setAttribute('w:val', 'right');
        $tab->setAttribute('w:leader', 'dot');
        $tab->setAttribute('w:pos', '9071');
        
        $setParaProps('both', 30, 30, 300, 0); // 1.25 lines for TOC
        $formatRuns('26', null, null, '000000'); // 13pt
        continue;
    }
    
    // --- 3. MAJOR SECTION TITLES (Front & Back matter) ---
    if (preg_match('/^(LỜI CẢM ƠN|LỜI CAM ĐOAN|DANH MỤC SƠ ĐỒ, LƯỢC ĐỒ, BIỂU ĐỒ, HÌNH|DANH MỤC BẢNG|LỜI MỞ ĐẦU|PHỤ LỤC|DANH MỤC TÀI LIỆU THAM KHẢO)$/u', $trimmed)) {
        // Start on new page
        $setParaProps('center', 240, 180, 360, 0, null, true);
        $formatRuns('32', true, false, '000000'); // 16pt Bold
        continue;
    }
    
    // --- 4. CHAPTER HEADINGS ---
    if (preg_match('/^Chương\s+[0-9IVX]+[\.:]/iu', $trimmed) && $i > 130) {
        $setParaProps('center', 240, 180, 360, 0, 'Heading1', true);
        $formatRuns('32', true, false, '000000'); // 16pt Bold
        continue;
    }
    
    // --- 5. SECTION HEADINGS (1.1, 1.2, 2.1...) ---
    if (preg_match('/^[0-9]+\.[0-9]+\s+[^\.]+/u', $trimmed) && !preg_match('/^[0-9]+\.[0-9]+\.[0-9]+/', $trimmed) && $i > 130) {
        $setParaProps('left', 180, 60, 360, 0, 'Heading2');
        $formatRuns('28', true, false, '000000'); // 14pt Bold
        continue;
    }
    
    // --- 6. SUBSECTION HEADINGS (1.1.1, 1.1.2...) ---
    if ((preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\s+/u', $trimmed) || preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\./u', $trimmed)) && !preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/', $trimmed) && $i > 130) {
        $setParaProps('left', 120, 60, 360, 0, 'Heading3');
        $formatRuns('26', true, false, '000000'); // 13pt Bold
        continue;
    }
    
    // --- 7. SUBSUBSECTION HEADINGS (1.1.1.1...) ---
    if (preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/u', $trimmed) && $i > 130) {
        $setParaProps('left', 120, 60, 360, 0, 'Heading4');
        $formatRuns('26', true, true, '000000'); // 13pt Bold + Italic
        continue;
    }
    
    // --- 8. CAPTIONS (Hình..., Bảng..., Sơ đồ...) ---
    if (preg_match('/^(Hình|Bảng|Sơ đồ|Biểu đồ|Lược đồ)\s+[0-9]+/iu', $trimmed) && $i > 130) {
        $setParaProps('center', 60, 180, 360, 0, 'Caption');
        $formatRuns('24', null, true, '000000'); // 12pt Italic
        continue;
    }
    
    // --- 9. DRAWINGS / IMAGES ---
    if ($hasDrawing) {
        $setParaProps('center', 120, 120, 360, 0);
        continue;
    }
    
    // --- 10. SIGNATURES (cuối Lời cảm ơn, Lời cam đoan) ---
    if (preg_match('/^(Tôi xin chân thành cảm ơn!|Sinh viên thực hiện:|Trần Lê Thân)$/iu', $trimmed)) {
        $setParaProps('right', 60, 60, 360, 0);
        $formatRuns('26', ($trimmed === 'Trần Lê Thân'), false, '000000');
        continue;
    }
    
    // --- 11. TABLE CELL PARAGRAPHS ---
    if ($isCell) {
        $setParaProps('both', 30, 30, 260, 0);
        $formatRuns('24', null, null, '000000'); // 12pt in table cells
        continue;
    }
    
    // --- 12. BULLET LIST ITEMS (- , + , * , •) ---
    if (preg_match('/^[-–•\*\+]\s+/u', $trimmed) || preg_match('/^[a-z]\)\s+/u', $trimmed)) {
        $setParaProps('both', 0, 100, 360, 0, null, false, 360, 240); // left indent 360, hanging 240
        $formatRuns('26', null, null, '000000'); // 13pt
        continue;
    }
    
    // --- 13. EMPTY PARAGRAPHS ---
    if ($trimmed === '') {
        $setParaProps('both', 0, 60, 360, 0);
        continue;
    }
    
    // --- 14. NORMAL BODY PARAGRAPHS ---
    // Justified, 1.5 lines, 6pt after, 13pt Times New Roman, 1.0cm (567 dxa) first line indent
    $setParaProps('both', 0, 120, 360, 567, 'Normal');
    $formatRuns('26', null, null, '000000');
}

// 3. Add Section Break right before Chương 1 so Chapter 1 starts on page 1 with Header & Footer!
// Find the paragraph with text 'Chương 1. TỔNG QUAN TÀI LIỆU'
for ($i = 0; $i < $paras->length; $i++) {
    $p = $paras->item($i);
    $text = '';
    foreach ($docXp->query('.//w:t', $p) as $t) $text .= $t->nodeValue;
    if (trim($text) === 'Chương 1. TỔNG QUAN TÀI LIỆU') {
        // The preceding paragraph should have a sectPr for Section 2 (Front Matter)
        $prevP = $paras->item($i - 1);
        $prevPPr = $docXp->query('./w:pPr', $prevP)->item(0);
        if (!$prevPPr) {
            $prevPPr = $docDom->createElement('w:pPr');
            $prevP->insertBefore($prevPPr, $prevP->firstChild);
        }
        
        $sect2 = $docDom->createElement('w:sectPr');
        $sect2Type = $docDom->createElement('w:type');
        $sect2Type->setAttribute('w:val', 'nextPage');
        $sect2->appendChild($sect2Type);
        
        $sect2Sz = $docDom->createElement('w:pgSz');
        $sect2Sz->setAttribute('w:w', '11906');
        $sect2Sz->setAttribute('w:h', '16838');
        $sect2->appendChild($sect2Sz);
        
        $sect2Mar = $docDom->createElement('w:pgMar');
        $sect2Mar->setAttribute('w:top', '1134');
        $sect2Mar->setAttribute('w:bottom', '1134');
        $sect2Mar->setAttribute('w:left', '1701');
        $sect2Mar->setAttribute('w:right', '1134');
        $sect2Mar->setAttribute('w:header', '720');
        $sect2Mar->setAttribute('w:footer', '720');
        $sect2Mar->setAttribute('w:gutter', '0');
        $sect2->appendChild($sect2Mar);
        
        $prevPPr->appendChild($sect2);
        echo "Inserted Section Break before Chương 1 at paragraph " . ($i - 1) . "\n";
        break;
    }
}

// In the final body sectPr (Section 3: Main body from Chapter 1 to end), ensure pgNumType start="1"
$body = $docXp->query('//w:body')->item(0);
$bodySectPr = $docXp->query('./w:sectPr', $body)->item(0);
if ($bodySectPr) {
    $pgNumType = $docXp->query('./w:pgNumType', $bodySectPr)->item(0);
    if (!$pgNumType) {
        $pgNumType = $docDom->createElement('w:pgNumType');
        $bodySectPr->appendChild($pgNumType);
    }
    $pgNumType->setAttribute('w:start', '1');
    echo "Set Section 3 page numbering to start at 1\n";
}

// Save back into ZipArchive
$zip->addFromString('word/styles.xml', $stylesDom->saveXML());
$zip->addFromString('word/document.xml', $docDom->saveXML());
$zip->addFromString('word/header1.xml', $newHeaderXml);
$zip->addFromString('word/footer1.xml', $newFooterXml);

$zip->close();
echo "Successfully created and closed $outputDocx\n";

// Copy formatted file to replace original Báo Cáo PHP.docx
copy($outputDocx, $sourceDocx);
echo "Overwrote docs/Báo Cáo PHP.docx with formatted version!\n";
