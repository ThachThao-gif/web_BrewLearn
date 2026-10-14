$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.Drawing
$out = 'D:\wamp64\Brewlearn'
$groups = @(
    @{title='TÀI KHOẢN VÀ CÀI ĐẶT'; color='#2563EB'; items=@('Đăng nhập, đăng xuất','Xem và cập nhật hồ sơ cá nhân','Cập nhật thông tin liên hệ, đổi mật khẩu','Đăng xuất khỏi các thiết bị khác','Cài đặt thông báo và giao diện')},
    @{title='HỌC TẬP VÀ TIẾN ĐỘ'; color='#0891B2'; items=@('Xem lộ trình và khóa học theo chức danh','Xem bài học, tài liệu và video','Tìm kiếm nội dung, tiếp tục bài học','Ghi nhận tiến độ, hoàn thành bài học','Xem tiến độ từng khóa và tổng thể','Tra cứu SOP, nội quy và chính sách')},
    @{title='KIỂM TRA VÀ KẾT QUẢ'; color='#7C3AED'; items=@('Xem bài kiểm tra theo chức danh','Làm bài kiểm tra và nộp câu trả lời','Chấm điểm tự động và lưu kết quả','Làm lại bài kiểm tra, lưu lượt thi mới','Xem điểm, số lượt và lịch sử kiểm tra')},
    @{title='NHÂN SỰ VÀ TỔNG QUAN'; color='#0284C7'; items=@('Xem danh sách nhân viên theo ca, cửa hàng','Tìm kiếm và lọc nhân viên','Xem hồ sơ học tập, kiểm tra và đánh giá','Tạo ca, phân ca cho nhân viên','Cập nhật trạng thái làm việc','Xem dashboard, thống kê và nhắc hạn')},
    @{title='ĐÁNH GIÁ VÀ PHÊ DUYỆT'; color='#D97706'; items=@('Kiểm tra điều kiện trước khi tạo lịch','Tạo lịch đánh giá theo tiêu chuẩn','Chấm 5 tiêu chí và nhập nhận xét','Lập đề xuất cải thiện khi chưa đạt','Gửi đánh giá, bổ sung khi bị trả lại','Phê duyệt hoặc trả lại kèm lý do','Tra cứu trạng thái và kết quả đã duyệt')},
    @{title='ĐÀO TẠO LẠI NHIỀU VÒNG'; color='#059669'; items=@('Duyệt hoặc từ chối đề xuất đào tạo lại','Giao nội dung, người phụ trách và thời hạn','Lưu và tra cứu lịch sử từng vòng','Xác nhận hoàn thành nội dung được giao','Kiểm tra bài học, lượt kiểm tra mới và xác nhận','Tạo lịch và theo dõi đánh giá lại','Điều chỉnh, giao vòng tiếp theo khi chưa đạt','Đóng yêu cầu khi kết quả Đạt đã được duyệt')},
    @{title='DỰ ĐOÁN NGUY CƠ'; color='#DB2777'; items=@('Tổng hợp đặc trưng học tập và kiểm tra','Kiểm tra phạm vi, điều kiện dữ liệu','Chạy mô hình dự đoán bằng script độc lập','Lưu mức nguy cơ và thời điểm dự đoán','Hiển thị nguy cơ cho quản lý cửa hàng','Đối chiếu nguy cơ với kết quả đánh giá')},
    @{title='THÔNG BÁO VÀ HỖ TRỢ'; color='#475569'; items=@('Tạo và gửi nhắc đánh giá định kỳ','Gửi thông báo nhắc nhở cho nhân viên','Gửi lịch, kết quả đánh giá sau phê duyệt','Gửi giao việc, lịch đánh giá lại từng vòng','Xem, đánh dấu đã đọc và xóa thông báo','Ghi nhận lỗi, sự cố nghiệp vụ','Gửi và xem yêu cầu hỗ trợ cá nhân')}
)
$doc = New-Object System.Xml.XmlDocument
$mx = $doc.CreateElement('mxfile'); $mx.SetAttribute('host','app.diagrams.net'); $mx.SetAttribute('version','24.7.17'); $doc.AppendChild($mx) | Out-Null
$script:nodes = @(); $script:lines = @(); $script:counter = 0
function Add-Cell($id, $value, $style, $x, $y, $w, $h) {
    $c=$doc.CreateElement('mxCell'); $c.SetAttribute('id',$id); $c.SetAttribute('value',$value); $c.SetAttribute('style',$style); $c.SetAttribute('vertex','1'); $c.SetAttribute('parent','1')
    $geo=$doc.CreateElement('mxGeometry'); foreach($p in @{'x'=$x;'y'=$y;'width'=$w;'height'=$h}.GetEnumerator()) {$geo.SetAttribute($p.Key,[string]$p.Value)}; $geo.SetAttribute('as','geometry'); $c.AppendChild($geo)|Out-Null; $script:root.AppendChild($c)|Out-Null
}
function Box($id,$text,$x,$y,$w,$h,$color,$head=$false) {
    $fill=if($head){$color}else{'#FFFFFF'}; $font=if($head){'#FFFFFF'}else{'#172033'}; $size=if($head){20}else{18}; $align=if($head){'center'}else{'left'}
    Add-Cell $id $text "rounded=0;whiteSpace=wrap;html=0;fillColor=$fill;strokeColor=$color;strokeWidth=2;fontColor=$font;fontSize=$size;fontFamily=Arial;align=$align;verticalAlign=middle;spacing=14;fontStyle=$(if($head){1}else{0});" $x $y $w $h
    $script:nodes += @{id=$id;text=$text;x=$x;y=$y;w=$w;h=$h;color=$color;head=$head}
}
function Hub($id,$x,$y) { Add-Cell $id '' 'shape=ellipse;fillColor=none;strokeColor=none;opacity=0;resizable=0;' ($x-1) ($y-1) 2 2 }
function Line($a,$b,$x1,$y1,$x2,$y2,$color,$exitX=0.5,$exitY=0.5,$entryX=0.5,$entryY=0.5) {
    $script:counter++; $c=$doc.CreateElement('mxCell'); $c.SetAttribute('id',"e$script:counter"); $c.SetAttribute('edge','1'); $c.SetAttribute('parent','1'); $c.SetAttribute('source',$a); $c.SetAttribute('target',$b)
    $c.SetAttribute('style',"edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=0;html=0;endArrow=none;startArrow=none;strokeWidth=2;strokeColor=$color;exitX=$exitX;exitY=$exitY;exitPerimeter=0;entryX=$entryX;entryY=$entryY;entryPerimeter=0;")
    $g=$doc.CreateElement('mxGeometry');$g.SetAttribute('relative','1');$g.SetAttribute('as','geometry');$c.AppendChild($g)|Out-Null;$script:root.AppendChild($c)|Out-Null
    if($x1 -ne $x2 -and $y1 -ne $y2) {throw 'Non-orthogonal line'}
    $script:lines+=@{x1=$x1;y1=$y1;x2=$x2;y2=$y2;color=$color}
}
function Page($id,$name,$w,$h) {
    $d=$doc.CreateElement('diagram');$d.SetAttribute('id',$id);$d.SetAttribute('name',$name);$mx.AppendChild($d)|Out-Null
    $m=$doc.CreateElement('mxGraphModel');foreach($p in @{'dx'=1422;'dy'=900;'grid'=1;'gridSize'=10;'guides'=1;'tooltips'=1;'connect'=1;'arrows'=1;'fold'=1;'page'=1;'pageScale'=1;'pageWidth'=$w;'pageHeight'=$h;'math'=0;'shadow'=0}.GetEnumerator()) {$m.SetAttribute($p.Key,[string]$p.Value)};$d.AppendChild($m)|Out-Null
    $script:root=$doc.CreateElement('root');$m.AppendChild($script:root)|Out-Null
    $c=$doc.CreateElement('mxCell');$c.SetAttribute('id','0');$script:root.AppendChild($c)|Out-Null
    $c=$doc.CreateElement('mxCell');$c.SetAttribute('id','1');$c.SetAttribute('parent','0');$script:root.AppendChild($c)|Out-Null
    $script:nodes=@();$script:lines=@();$script:counter=0
}
function Branch($i,$x,$y,$w,$step) {
    $g=$groups[$i-1];$header="g$i"; Box $header "$i. $($g.title)" $x $y $w 80 $g.color $true
    $sx=$x+18; $cy=$y+112; Hub "h$i-0" $sx $cy; Hub "t$i" $sx ($y+80)
    Line $header "t$i" $sx ($y+80) $sx ($y+80) $g.color (18/$w) 1
    Line "t$i" "h$i-0" $sx ($y+80) $sx $cy $g.color
    for($j=0;$j -lt $g.items.Count;$j++) {
        $by=$y+112+$j*$step; $mid=$by+36; $hid="h$i-$($j+1)";Hub $hid $sx $mid
        $prev=if($j -eq 0){"h$i-0"}else{"h$i-$j"};$py=if($j -eq 0){$cy}else{$y+112+($j-1)*$step+36}
        Line $prev $hid $sx $py $sx $mid $g.color
        $bid="f$i-$($j+1)";Box $bid "$i.$($j+1) $($g.items[$j])" ($x+44) $by ($w-44) 72 $g.color
        Line $hid $bid $sx $mid ($x+44) $mid $g.color 0.5 0.5 0 0.5
    }
}
Page 'overview' '0. BFD tổng thể' 3240 1230
Box 'system' "0. HỆ THỐNG BREWLEARN`nĐào tạo và theo dõi năng lực nhân viên cửa hàng" 1200 40 840 90 '#172033' $true
Hub 'center' 1620 190;Line 'system' 'center' 1620 130 1620 190 '#64748B' 0.5 1
for($i=1;$i -le 8;$i++) { $x=40+($i-1)*400; $cx=$x+180;Hub "bus$i" $cx 190; if($i -gt 1){Line "bus$($i-1)" "bus$i" ($cx-400) 190 $cx 190 '#64748B'}; Branch $i $x 250 360 94;Line "bus$i" "g$i" $cx 190 $cx 250 '#64748B' 0.5 0.5 0.5 0 }
# Attach the central root to the shared horizontal bus without overlapping edges.
Line 'center' 'bus4' 1620 190 1420 190 '#64748B'
# The center-to-bus4 segment would duplicate the bus4-to-bus5 segment; replace it
# with a central junction in that segment.
$remove=$script:root.SelectSingleNode("mxCell[@source='bus4' and @target='bus5']");$script:root.RemoveChild($remove)|Out-Null
$script:lines=@($script:lines | Where-Object { -not ($_.x1 -eq 1420 -and $_.x2 -eq 1820 -and $_.y1 -eq 190) })
Line 'center' 'bus5' 1620 190 1820 190 '#64748B'
$note="BFD — Phân rã chức năng: đường nối thể hiện quan hệ cha–con, không thể hiện thứ tự xử lý.`nPhạm vi: Nhân viên · Trưởng ca · Quản lý cửa hàng. Dự đoán nguy cơ chạy độc lập; kết quả đánh giá gửi sau phê duyệt.`nĐào tạo lại lưu lịch sử từng vòng và chỉ hoàn tất khi kết quả đánh giá lại Đạt đã được duyệt."
Box 'note' $note 40 1140 3160 74 '#64748B'
$overviewNodes=$script:nodes; $overviewLines=$script:lines
for($i=1;$i -le 8;$i++) {Page "detail$i" "$i. $($groups[$i-1].title)" 1100 1160;Box 'system' '0. HỆ THỐNG BREWLEARN' 200 40 760 70 '#172033' $true;Branch $i 200 180 760 102;Line 'system' "g$i" 580 110 580 180 '#64748B' 0.5 1 0.5 0}
$doc.Save((Join-Path $out 'BFD-Brewlearn.xml'));$doc.Save((Join-Path $out 'BFD-Brewlearn.drawio'))
# Render a preview from the same geometry used in the editable XML.
$bmp=New-Object System.Drawing.Bitmap 3240,1230
$gfx=[System.Drawing.Graphics]::FromImage($bmp);$gfx.Clear([System.Drawing.Color]::White);$gfx.SmoothingMode='AntiAlias';$gfx.TextRenderingHint='AntiAliasGridFit'
foreach($l in $overviewLines) {$pen=New-Object System.Drawing.Pen ([System.Drawing.ColorTranslator]::FromHtml($l.color)),2;$gfx.DrawLine($pen,$l.x1,$l.y1,$l.x2,$l.y2);$pen.Dispose()}
foreach($n in $overviewNodes) {
    $c=[System.Drawing.ColorTranslator]::FromHtml($n.color);$fill=if($n.head){$c}else{[System.Drawing.Color]::White};$brush=New-Object System.Drawing.SolidBrush $fill;$pen=New-Object System.Drawing.Pen $c,2
    $gfx.FillRectangle($brush,$n.x,$n.y,$n.w,$n.h);$gfx.DrawRectangle($pen,$n.x,$n.y,$n.w,$n.h)
    $size=if($n.head){20}else{18};$style=if($n.head){[System.Drawing.FontStyle]::Bold}else{[System.Drawing.FontStyle]::Regular}
    $font=New-Object System.Drawing.Font 'Arial',$size,$style,([System.Drawing.GraphicsUnit]::Pixel);$fmt=New-Object System.Drawing.StringFormat;$fmt.LineAlignment='Center';$fmt.Alignment=if($n.head){'Center'}else{'Near'}
    $tc=if($n.head){[System.Drawing.Color]::White}else{[System.Drawing.ColorTranslator]::FromHtml('#172033')};$tb=New-Object System.Drawing.SolidBrush $tc
    $rect=New-Object System.Drawing.RectangleF ($n.x+14),($n.y+4),($n.w-28),($n.h-8);$gfx.DrawString($n.text,$font,$tb,$rect,$fmt)
    $brush.Dispose();$pen.Dispose();$font.Dispose();$fmt.Dispose();$tb.Dispose()
}
$bmp.Save((Join-Path $out 'BFD-Brewlearn.png'),[System.Drawing.Imaging.ImageFormat]::Png);$gfx.Dispose();$bmp.Dispose()
$check=New-Object System.Xml.XmlDocument;$check.Load((Join-Path $out 'BFD-Brewlearn.xml'))
Write-Output "Validated XML: $($check.mxfile.diagram.Count) pages. $($overviewNodes.Count) overview boxes. All connector segments orthogonal."
Write-Output $out
