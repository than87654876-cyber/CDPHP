<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
preg_match_all('/(?:Giảng viên|GVHD|KS\.|ThS\.)[^<]{0,100}/iu', $xml, $m);
print_r(array_unique($m[0]));
