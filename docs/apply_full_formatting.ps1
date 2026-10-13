$ErrorActionPreference = "Stop"

$docPath = "c:\laragon\www\CDPHP\docs\Báo Cáo PHP.docx"

# Ensure any previous WINWORD is closed
Get-Process WINWORD -ErrorAction SilentlyContinue | Stop-Process -Force
Start-Sleep -Milliseconds 500

Write-Host "Starting Word Application..."
$word = New-Object -ComObject Word.Application
$word.Visible = $false

try {
    Write-Host "Opening document: $docPath"
    $doc = $word.Documents.Open($docPath)
    
    # -------------------------------------------------------------
    # 1. CONFIGURE WORD STYLES ACCORDING TO GUIDELINES
    # -------------------------------------------------------------
    Write-Host "Configuring document styles..."
    $styles = $doc.Styles
    
    # Normal Style: Times New Roman, 13pt, Justified, 1.5 line spacing, 0pt before, 4pt after
    $sNormal = $styles.Item("Normal")
    $sNormal.Font.Name = "Times New Roman"
    $sNormal.Font.Size = 13
    $sNormal.Font.Bold = 0
    $sNormal.Font.Color = 0 # wdColorAutomatic / Black
    $sNormal.ParagraphFormat.Alignment = 3 # wdAlignParagraphJustify
    $sNormal.ParagraphFormat.LineSpacingRule = 1 # wdLineSpace1pt5
    $sNormal.ParagraphFormat.SpaceBefore = 0
    $sNormal.ParagraphFormat.SpaceAfter = 4

    # Heading 1: Times New Roman, 16pt, Bold, Left/Center, 1.5 line spacing, 12pt before, 6pt after
    $sH1 = $styles.Item("Heading 1")
    $sH1.Font.Name = "Times New Roman"
    $sH1.Font.Size = 16
    $sH1.Font.Bold = -1
    $sH1.Font.Color = 0
    $sH1.ParagraphFormat.Alignment = 0 # wdAlignParagraphLeft
    $sH1.ParagraphFormat.LineSpacingRule = 1
    $sH1.ParagraphFormat.SpaceBefore = 12
    $sH1.ParagraphFormat.SpaceAfter = 6

    # Heading 2: Times New Roman, 14pt, Bold, Left, 1.5 line spacing, 9pt before, 3pt after
    $sH2 = $styles.Item("Heading 2")
    $sH2.Font.Name = "Times New Roman"
    $sH2.Font.Size = 14
    $sH2.Font.Bold = -1
    $sH2.Font.Color = 0
    $sH2.ParagraphFormat.Alignment = 0
    $sH2.ParagraphFormat.LineSpacingRule = 1
    $sH2.ParagraphFormat.SpaceBefore = 9
    $sH2.ParagraphFormat.SpaceAfter = 3

    # Heading 3: Times New Roman, 13pt, Bold, Left, 1.5 line spacing, 6pt before, 3pt after
    $sH3 = $styles.Item("Heading 3")
    $sH3.Font.Name = "Times New Roman"
    $sH3.Font.Size = 13
    $sH3.Font.Bold = -1
    $sH3.Font.Color = 0
    $sH3.ParagraphFormat.Alignment = 0
    $sH3.ParagraphFormat.LineSpacingRule = 1
    $sH3.ParagraphFormat.SpaceBefore = 6
    $sH3.ParagraphFormat.SpaceAfter = 3

    # Heading 4: Times New Roman, 13pt, Bold, Left, 1.5 line spacing, 4pt before, 2pt after
    $sH4 = $styles.Item("Heading 4")
    $sH4.Font.Name = "Times New Roman"
    $sH4.Font.Size = 13
    $sH4.Font.Bold = -1
    $sH4.Font.Color = 0
    $sH4.ParagraphFormat.Alignment = 0
    $sH4.ParagraphFormat.LineSpacingRule = 1
    $sH4.ParagraphFormat.SpaceBefore = 4
    $sH4.ParagraphFormat.SpaceAfter = 2

    # TOC Styles
    for ($t = 1; $t -le 4; $t++) {
        try {
            $stoc = $styles.Item("TOC $t")
            $stoc.Font.Name = "Times New Roman"
            $stoc.Font.Size = 13
            $stoc.ParagraphFormat.LineSpacingRule = 1
        } catch {}
    }

    # -------------------------------------------------------------
    # 2. INSERT MISSING HEADINGS IN SECTION 3
    # -------------------------------------------------------------
    $sec3 = $doc.Sections.Item(3)

    # Insert 2.2.2 before 2.2.2.1
    foreach ($p in $sec3.Range.Paragraphs) {
        $t = $p.Range.Text.Trim()
        if ($t -match "^2\.2\.2\.1\s*Đặc tả use case đăng nhập") {
            Write-Host "Inserting '2.2.2 Đặc tả các Use case chính'..."
            $rng = $p.Range
            $rng.Collapse(1) # wdCollapseStart
            $rng.InsertBefore("2.2.2`tĐặc tả các Use case chính`r")
            $newP = $rng.Paragraphs.Item(1)
            $newP.Style = $doc.Styles.Item("Heading 3")
            break
        }
    }

    # Insert 2.2.2.2 before logout use case table
    $foundLoginCaption = $false
    foreach ($p in $sec3.Range.Paragraphs) {
        $t = $p.Range.Text.Trim()
        if ($t -match "^Bảng 2\.1\s*Luồng sự kiện chính use case đăng nhập") {
            $foundLoginCaption = $true
            continue
        }
        if ($foundLoginCaption) {
            Write-Host "Inserting '2.2.2.2 Đặc tả use case đăng xuất khỏi hệ thống.'..."
            $rng = $p.Range
            $rng.Collapse(1)
            $rng.InsertBefore("2.2.2.2`tĐặc tả use case đăng xuất khỏi hệ thống.`r")
            $newP = $rng.Paragraphs.Item(1)
            $newP.Style = $doc.Styles.Item("Heading 4")
            break
        }
    }

    # Helper function to safely set paragraph text and style
    function Set-HeadingSafe($p, [string]$newText, [string]$styleName) {
        $rng = $p.Range
        if ($rng.Characters.Count -gt 1) {
            $rng.End = $rng.End - 1
        }
        $rng.Text = $newText
        $p.Style = $doc.Styles.Item($styleName)
    }

    # -------------------------------------------------------------
    # 3. FORMAT SECTION 1 (COVER PAGE)
    # -------------------------------------------------------------
    Write-Host "Formatting Section 1 (Cover Page)..."
    $sec1 = $doc.Sections.Item(1)
    foreach ($p in $sec1.Range.Paragraphs) {
        $p.Range.Font.Name = "Times New Roman"
    }

    # -------------------------------------------------------------
    # 4. FORMAT SECTION 2 (PRELIMINARY PAGES & TOC)
    # -------------------------------------------------------------
    Write-Host "Formatting Section 2 (Preliminary Pages)..."
    $sec2 = $doc.Sections.Item(2)
    foreach ($p in $sec2.Range.Paragraphs) {
        $t = $p.Range.Text.Trim()
        if ($t.Length -eq 0) { continue }

        if ($t -match "^(LỜI CẢM ƠN|LỜI CAM ĐOAN|MỤC LỤC|DANH MỤC SƠ ĐỒ|DANH MỤC BẢNG|LỜI MỞ ĐẦU)") {
            $p.Range.Font.Name = "Times New Roman"
            $p.Range.Font.Size = 16
            $p.Range.Font.Bold = -1
            $p.Range.ParagraphFormat.Alignment = 1 # Center
            $p.Range.ParagraphFormat.SpaceBefore = 12
            $p.Range.ParagraphFormat.SpaceAfter = 6
        }
        elseif ($t -match "^(Sơ đồ:|Lược đồ:|Biểu đồ:|Hình:)$") {
            # Keep category label as Normal, 14pt, Bold
            $p.Style = $doc.Styles.Item("Normal")
            $p.Range.Font.Name = "Times New Roman"
            $p.Range.Font.Size = 14
            $p.Range.Font.Bold = -1
            $p.Range.ParagraphFormat.Alignment = 0
            $p.Range.ParagraphFormat.SpaceBefore = 6
            $p.Range.ParagraphFormat.SpaceAfter = 3
        }
        elseif ($p.Style.NameLocal -match "^TOC") {
            $p.Range.Font.Name = "Times New Roman"
            $p.Range.Font.Size = 13
        }
        else {
            $p.Range.Font.Name = "Times New Roman"
            $p.Range.Font.Size = 13
            $p.Range.ParagraphFormat.Alignment = 3
            $p.Range.ParagraphFormat.LineSpacingRule = 1
            $p.Range.ParagraphFormat.SpaceBefore = 0
            $p.Range.ParagraphFormat.SpaceAfter = 4
        }
    }

    # -------------------------------------------------------------
    # 5. FORMAT SECTION 3 (CHAPTERS 1-4 & APPENDIX)
    # -------------------------------------------------------------
    Write-Host "Formatting Section 3 (Body Chapters 1-4)..."
    foreach ($p in $sec3.Range.Paragraphs) {
        $t = $p.Range.Text.Trim()
        if ($t.Length -eq 0) { continue }

        # Chapter Headings (Heading 1)
        if ($t -match "^Chương\s*1\.") {
            Set-HeadingSafe $p "Chương 1. TỔNG QUAN TÀI LIỆU" "Heading 1"
            continue
        }
        if ($t -match "^Chương\s*2\.") {
            Set-HeadingSafe $p "Chương 2. PHƯƠNG PHÁP THỰC HIỆN" "Heading 1"
            continue
        }
        if ($t -match "^Chương\s*3\.") {
            Set-HeadingSafe $p "Chương 3. CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ" "Heading 1"
            continue
        }
        if ($t -match "^Chương\s*4[:\.]") {
            Set-HeadingSafe $p "Chương 4. KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN" "Heading 1"
            continue
        }
        if ($t -match "^PHỤ LỤC$") {
            Set-HeadingSafe $p "PHỤ LỤC" "Heading 1"
            continue
        }
        if ($t -match "^DANH MỤC TÀI LIỆU THAM KHẢO$") {
            Set-HeadingSafe $p "DANH MỤC TÀI LIỆU THAM KHẢO" "Heading 1"
            continue
        }

        # Heading 2
        if ($t -match "^1\.1\s*Khảo sát bài toán") {
            Set-HeadingSafe $p "1.1`tKhảo sát bài toán" "Heading 2"
            continue
        }
        if ($t -match "^1\.2\s*Cơ sở lý thuyết" -or $t -match "^1\.2Cơ sở lý thuyết") {
            Set-HeadingSafe $p "1.2`tCơ sở lý thuyết" "Heading 2"
            continue
        }
        if ($t -match "^2\.1\s*Phân tích hệ thống") {
            Set-HeadingSafe $p "2.1`tPhân tích hệ thống" "Heading 2"
            continue
        }
        if ($t -match "^2\.2\s*Các biểu đồ thiết kế hệ thống") {
            Set-HeadingSafe $p "2.2`tCác biểu đồ thiết kế hệ thống" "Heading 2"
            continue
        }
        if ($t -match "^2\.3\s*Cơ sở dữ liệu") {
            Set-HeadingSafe $p "2.3`tCơ sở dữ liệu" "Heading 2"
            continue
        }
        if ($t -match "^3\.1\s*Một số giao diện website") {
            Set-HeadingSafe $p "3.1`tMột số giao diện website" "Heading 2"
            continue
        }
        if ($t -match "^3\.2\s*Kết quả Kiểm thử") {
            Set-HeadingSafe $p "3.2`tKết quả Kiểm thử tự động (Automation Testing với PHPUnit)" "Heading 2"
            continue
        }
        if ($t -match "^3\.3\s*Đánh giá hiệu năng") {
            Set-HeadingSafe $p "3.3`tĐánh giá hiệu năng và an toàn bảo mật" "Heading 2"
            continue
        }
        if ($t -match "^4\.1\s*Kết luận") {
            Set-HeadingSafe $p "4.1`tKết luận" "Heading 2"
            continue
        }
        if ($t -match "^4\.2\s*Định hướng phát triển") {
            Set-HeadingSafe $p "4.2`tĐịnh hướng phát triển" "Heading 2"
            continue
        }

        # Heading 3 - Chapter 1
        if ($t -match "^1\.1\.1\.\s*Đặt vấn đề") {
            Set-HeadingSafe $p "1.1.1`tĐặt vấn đề" "Heading 3"
            continue
        }
        if ($t -match "^1\.1\.2\.\s*Giải pháp") {
            Set-HeadingSafe $p "1.1.2`tGiải pháp" "Heading 3"
            continue
        }
        if ($t -match "^1\.1\.3\.\s*Mục đích của đề tài") {
            Set-HeadingSafe $p "1.1.3`tMục đích của đề tài" "Heading 3"
            continue
        }
        if ($t -match "^1\.2\.1\.\s*HTML5") {
            Set-HeadingSafe $p "1.2.1`tHTML5" "Heading 3"
            continue
        }
        if ($t -match "^1\.2\.2\.\s*CSS") {
            Set-HeadingSafe $p "1.2.2`tCSS" "Heading 3"
            continue
        }
        if ($t -match "^1\.2\.3\.\s*Boostrap") {
            Set-HeadingSafe $p "1.2.3`tBootstrap & Tailwind CSS" "Heading 3"
            continue
        }
        if ($t -match "^1\.2\.4\.\s*Javascript") {
            Set-HeadingSafe $p "1.2.4`tJavascript" "Heading 3"
            continue
        }
        if ($t -match "^1\.2\.5\.\s*Jquery") {
            Set-HeadingSafe $p "1.2.5`tjQuery & WebSockets" "Heading 3"
            continue
        }
        if ($t -match "^1\.2\.6\.\s*PHP") {
            Set-HeadingSafe $p "1.2.6`tPHP" "Heading 3"
            continue
        }
        if ($t -match "^1\.2\.7\.\s*Laravel") {
            Set-HeadingSafe $p "1.2.7`tLaravel" "Heading 3"
            continue
        }
        if ($t -match "^1\.2\.8\.\s*MySQL") {
            Set-HeadingSafe $p "1.2.8`tMySQL Server" "Heading 3"
            continue
        }
        if ($t -match "^1\.2\.9\.\s*Visual Studio") {
            Set-HeadingSafe $p "1.2.9`tVisual Studio Code" "Heading 3"
            continue
        }
        if ($t -match "^1\.2\.10\.\s*Laragon") {
            Set-HeadingSafe $p "1.2.10`tLaragon" "Heading 3"
            continue
        }

        # Heading 3 - Chapter 2
        if ($t -match "^2\.1\.1\s*Quy trình nghiệp vụ") {
            Set-HeadingSafe $p "2.1.1`tQuy trình nghiệp vụ" "Heading 3"
            continue
        }
        if ($t -match "^2\.1\.2\s*Thông tin yêu cầu") {
            Set-HeadingSafe $p "2.1.2`tThông tin yêu cầu" "Heading 3"
            continue
        }
        if ($t -match "^2\.1\.3\s*Sơ đồ chức năng của hệ thống") {
            Set-HeadingSafe $p "2.1.3`tSơ đồ chức năng của hệ thống" "Heading 3"
            continue
        }
        if ($t -match "^2\.2\.1\s*Lượ[ct] đồ Use case") {
            Set-HeadingSafe $p "2.2.1`tLược đồ Use case" "Heading 3"
            continue
        }
        if ($t -match "^2\.2\.2\s*Lược đồ Activity") {
            Set-HeadingSafe $p "2.2.3`tLược đồ Activity (Activity Diagram)" "Heading 3"
            continue
        }
        if ($t -match "^2\.2\.3\s*Biểu đồ thành phần") {
            Set-HeadingSafe $p "2.2.4`tBiểu đồ thành phần (Component Diagram)" "Heading 3"
            continue
        }
        if ($t -match "^2\.2\.4\s*Biểu đồ triển khai") {
            Set-HeadingSafe $p "2.2.5`tBiểu đồ triển khai (Deployment Diagram)" "Heading 3"
            continue
        }
        if ($t -match "^Table Users") {
            Set-HeadingSafe $p "2.3.1`tBảng Users (Bảng tài khoản người dùng)" "Heading 3"
            continue
        }
        if ($t -match "^Table Categories") {
            Set-HeadingSafe $p "2.3.2`tBảng Categories (Bảng danh mục món ăn)" "Heading 3"
            continue
        }
        if ($t -match "^Table Dishes") {
            Set-HeadingSafe $p "2.3.3`tBảng Dishes (Bảng thông tin món ăn)" "Heading 3"
            continue
        }
        if ($t -match "^Table Orders") {
            Set-HeadingSafe $p "2.3.4`tBảng Orders (Bảng đơn đặt hàng)" "Heading 3"
            continue
        }
        if ($t -match "^Table Suppliers") {
            Set-HeadingSafe $p "2.3.5`tBảng Suppliers & Inventory_Items (Nhà cung cấp & Kho nguyên liệu)" "Heading 3"
            continue
        }

        # Heading 3 - Chapter 3
        if ($t -match "^3\.1\.1\s*Phần quản trị") {
            Set-HeadingSafe $p "3.1.1`tPhần quản trị (Admin Portal)" "Heading 3"
            continue
        }
        if ($t -match "^3\.1\.2\s*File excel xuất ra") {
            Set-HeadingSafe $p "3.1.2`tBáo cáo xuất file Excel từ hệ thống quản trị" "Heading 3"
            continue
        }
        if ($t -match "^3\.1\.3\s*Phần người dùng") {
            Set-HeadingSafe $p "3.1.3`tPhần người dùng (Client Web Application)" "Heading 3"
            continue
        }

        # Heading 3 - Chapter 4
        if ($t -match "^Kết luận đạt được") {
            Set-HeadingSafe $p "4.1.1`tKết quả đạt được" "Heading 3"
            continue
        }
        if ($t -match "^Lý thuyết, kỹ năng và kinh nghiệm") {
            Set-HeadingSafe $p "4.1.2`tLý thuyết, kỹ năng và kinh nghiệm tích lũy" "Heading 3"
            continue
        }
        if ($t -match "^Khuyết điểm") {
            Set-HeadingSafe $p "4.1.3`tHạn chế và khuyết điểm" "Heading 3"
            continue
        }
        if ($t -match "^Kinh nghiệm đúc kết") {
            Set-HeadingSafe $p "4.1.4`tBài học kinh nghiệm" "Heading 3"
            continue
        }
        if ($t -match "^Phát triển đề tài") {
            Set-HeadingSafe $p "4.2.1`tĐịnh hướng phát triển đề tài" "Heading 3"
            continue
        }
        if ($t -match "^Phát triển bản thân") {
            Set-HeadingSafe $p "4.2.2`tĐịnh hướng phát triển bản thân" "Heading 3"
            continue
        }

        # Heading 4 - Chapter 2 Use cases
        if ($t -match "^2\.2\.2\.1\s*Đặc tả use case đăng nhập") {
            Set-HeadingSafe $p "2.2.2.1`tĐặc tả use case đăng nhập vào hệ thống." "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.3\s*Đặc tả use case tìm kiếm") {
            Set-HeadingSafe $p "2.2.2.3`tĐặc tả use case tìm kiếm và xem chi tiết món ăn." "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.4\s*Đặc tả use case đặt món") {
            Set-HeadingSafe $p "2.2.2.4`tĐặc tả use case đặt món theo nhóm Realtime." "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.5\s*Đặc tả use case thanh toán") {
            Set-HeadingSafe $p "2.2.2.5`tĐặc tả use case thanh toán tự động SePay." "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.6\s*Đặc tả use case Trợ lý AI") {
            Set-HeadingSafe $p "2.2.2.6`tĐặc tả use case Trợ lý AI tư vấn món ăn." "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.7\s*Đặc tả use case điều phối") {
            Set-HeadingSafe $p "2.2.2.7`tĐặc tả use case điều phối chế biến tại Màn hình Bếp." "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.8\s*Đặc tả use case quản lý thực đơn" -or $t -match "^2\.2\.2\.8Đặc tả use case") {
            Set-HeadingSafe $p "2.2.2.8`tĐặc tả use case quản lý thực đơn món ăn." "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.9\s*Đặc tả use case quản lý kho") {
            Set-HeadingSafe $p "2.2.2.9`tĐặc tả use case quản lý kho nguyên liệu và nhà cung cấp." "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.10\s*Đặc tả use case quản lý khuyến mãi") {
            Set-HeadingSafe $p "2.2.2.10`tĐặc tả use case quản lý khuyến mãi và cấp voucher ví." "Heading 4"
            continue
        }

        # Heading 4 - Chapter 2 Activity diagrams
        if ($t -match "^2\.2\.2\.1\s*Lược đồ chức năng đăng nhập") {
            Set-HeadingSafe $p "2.2.3.1`tLược đồ hoạt động chức năng đăng nhập:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.3\s*Lược đồ chức năng reset password") {
            Set-HeadingSafe $p "2.2.3.2`tLược đồ hoạt động chức năng quên mật khẩu / reset password:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.4\s*Lược đồ tìm kiếm") {
            Set-HeadingSafe $p "2.2.3.3`tLược đồ hoạt động chức năng tìm kiếm món ăn:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.5\s*Lược đồ đặt món theo nhóm") {
            Set-HeadingSafe $p "2.2.3.4`tLược đồ hoạt động đặt món theo nhóm Realtime:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.6\s*Lược đồ thanh toán") {
            Set-HeadingSafe $p "2.2.3.5`tLược đồ hoạt động thanh toán tự động PayOS VietQR:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.7\s*Lược đồ điều phối Bếp") {
            Set-HeadingSafe $p "2.2.3.6`tLược đồ hoạt động điều phối Bếp FIFO:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.8\s*Lược đồ quản lý hóa đơn") {
            Set-HeadingSafe $p "2.2.3.7`tLược đồ hoạt động quản lý hóa đơn và in bill:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.9\s*Lược đồ quản lý khách hàng") {
            Set-HeadingSafe $p "2.2.3.8`tLược đồ hoạt động quản lý khách hàng:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.10\s*Lược đồ quản lý món ăn") {
            Set-HeadingSafe $p "2.2.3.9`tLược đồ hoạt động quản lý món ăn:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.11\s*Lược đồ quản lý nhà cung cấp") {
            Set-HeadingSafe $p "2.2.3.10`tLược đồ hoạt động quản lý nhà cung cấp:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.12\s*Lược đồ quản lý kho nguyên liệu") {
            Set-HeadingSafe $p "2.2.3.11`tLược đồ hoạt động quản lý kho nguyên liệu:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.2\.13\s*Lược đồ quản lý nhân viên") {
            Set-HeadingSafe $p "2.2.3.12`tLược đồ hoạt động quản lý nhân viên và phân quyền:" "Heading 4"
            continue
        }

        # Heading 4 - Chapter 2 Component diagrams
        if ($t -match "^2\.2\.4\.1\s*Biểu đồ thành phần hệ thống") {
            Set-HeadingSafe $p "2.2.4.1`tBiểu đồ thành phần tổng quan hệ thống:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.4\.2\s*Biểu đồ thành phần admin") {
            Set-HeadingSafe $p "2.2.4.2`tBiểu đồ thành phần phân hệ Admin:" "Heading 4"
            continue
        }
        if ($t -match "^2\.2\.4\.4\s*Biểu đồ thành phần khách hàng") {
            Set-HeadingSafe $p "2.2.4.3`tBiểu đồ thành phần phân hệ Khách hàng:" "Heading 4"
            continue
        }

        # Heading 4 - Chapter 3 Admin UI
        if ($t -match "^3\.1\.3\.1\s*Giao diện đăng nhập") {
            Set-HeadingSafe $p "3.1.1.1`tGiao diện đăng nhập:" "Heading 4"
            continue
        }
        if ($t -match "^3\.1\.3\.2\s*Giao diện Dashboard") {
            Set-HeadingSafe $p "3.1.1.2`tGiao diện Dashboard thống kê doanh thu:" "Heading 4"
            continue
        }
        if ($t -match "^3\.1\.3\.3\s*Giao diện quản lý thực đơn") {
            Set-HeadingSafe $p "3.1.1.3`tGiao diện quản lý thực đơn món ăn:" "Heading 4"
            continue
        }
        if ($t -match "^3\.1\.3\.4\s*Giao diện quản lý đơn đặt hàng") {
            Set-HeadingSafe $p "3.1.1.4`tGiao diện quản lý đơn đặt hàng:" "Heading 4"
            continue
        }
        if ($t -match "^3\.1\.3\.5\s*Giao diện quản lý khách hàng") {
            Set-HeadingSafe $p "3.1.1.5`tGiao diện quản lý khách hàng:" "Heading 4"
            continue
        }
        if ($t -match "^3\.1\.3\.6\s*Giao diện quản lý kho nguyên liệu") {
            Set-HeadingSafe $p "3.1.1.6`tGiao diện quản lý kho nguyên liệu:" "Heading 4"
            continue
        }
        if ($t -match "^3\.1\.3\.7\s*Giao diện quản lý nhà cung cấp") {
            Set-HeadingSafe $p "3.1.1.7`tGiao diện quản lý nhà cung cấp:" "Heading 4"
            continue
        }
        if ($t -match "^3\.1\.3\.8\s*Giao diện Màn hình Bếp FIFO") {
            Set-HeadingSafe $p "3.1.1.8`tGiao diện Màn hình Bếp FIFO:" "Heading 4"
            continue
        }
        if ($t -match "^3\.1\.3\.9\s*Giao diện bàn làm việ[cv] nhân viên") {
            Set-HeadingSafe $p "3.1.1.9`tGiao diện bàn làm việc nhân viên:" "Heading 4"
            continue
        }
        if ($t -match "^3\.1\.3\.10\s*Giao diện quên mật khẩu") {
            Set-HeadingSafe $p "3.1.1.10`tGiao diện quên mật khẩu:" "Heading 4"
            continue
        }

        # Heading 4 - Chapter 3 Client UI
        if ($t -match "^3\.1\.3\.1\s*Giao diện người dùng ở thiết bị màn hình lớn") {
            Set-HeadingSafe $p "3.1.3.1`tGiao diện người dùng ở thiết bị màn hình lớn:" "Heading 4"
            continue
        }
        if ($t -match "^3\.1\.3\.2\s*Giao diện người dùng ở thiết bị màn hình nhỏ") {
            Set-HeadingSafe $p "3.1.3.2`tGiao diện người dùng ở thiết bị màn hình nhỏ:" "Heading 4"
            continue
        }

        # Captions
        $styleName = $p.Style.NameLocal
        if ($styleName -match "^(sơ đồ|lược đồ|biểu đồ|hình|luồng xử lí chính|Caption)" -or $t -match "^(Hình\s|Bảng\s|Sơ đồ\s|Lược đồ\s|Biểu đồ\s)") {
            $p.Range.Font.Name = "Times New Roman"
            $p.Range.Font.Size = 12
            $p.Range.ParagraphFormat.Alignment = 1 # Center
            $p.Range.ParagraphFormat.SpaceBefore = 3
            $p.Range.ParagraphFormat.SpaceAfter = 6
            continue
        }

        # Table cell or regular body text
        if ($p.Range.Information([Microsoft.Office.Interop.Word.WdInformation]::wdWithInTable)) {
            $p.Range.Font.Name = "Times New Roman"
            if ($p.Range.Font.Size -lt 11 -or $p.Range.Font.Size -gt 13) {
                $p.Range.Font.Size = 12
            }
        }
        else {
            # Regular body paragraph
            $p.Range.Font.Name = "Times New Roman"
            $p.Range.Font.Size = 13
            $p.Range.ParagraphFormat.Alignment = 3 # Justify
            $p.Range.ParagraphFormat.LineSpacingRule = 1 # 1.5 lines
            $p.Range.ParagraphFormat.SpaceBefore = 0
            $p.Range.ParagraphFormat.SpaceAfter = 4
        }
    }

    # -------------------------------------------------------------
    # 6. HEADER & FOOTER FORMATTING
    # -------------------------------------------------------------
    Write-Host "Formatting Header and Footer in Section 3..."
    $h = $sec3.Headers.Item(1)
    $h.Range.Font.Name = "Times New Roman"
    $h.Range.Font.Size = 12

    $f = $sec3.Footers.Item(1)
    $f.Range.Font.Name = "Times New Roman"
    $f.Range.Font.Size = 12

    # -------------------------------------------------------------
    # 7. UPDATE TABLES OF CONTENTS & ALL FIELDS
    # -------------------------------------------------------------
    Write-Host "Updating Tables of Contents..."
    $toc1 = $doc.TablesOfContents.Item(1)
    $toc1.UpperHeadingLevel = 1
    $toc1.LowerHeadingLevel = 3
    $toc1.Update()
    Write-Host "TOC 1 (Main Mục Lục) updated."

    for ($t = 2; $t -le $doc.TablesOfContents.Count; $t++) {
        try {
            $doc.TablesOfContents.Item($t).Update()
            Write-Host "TOC $t updated."
        } catch {
            Write-Host "TOC $t update: $_"
        }
    }

    Write-Host "Updating all Fields in document..."
    $doc.Fields.Update()

    Write-Host "Saving document to $docPath..."
    $doc.Save()
    $doc.Close([Microsoft.Office.Interop.Word.WdSaveOptions]::wdSaveChanges)
    Write-Host "Document successfully formatted and saved!"
}
finally {
    $word.Quit()
}
