Add-Type -AssemblyName System.Drawing
$dir = $PSScriptRoot
$utf8 = [System.Text.UTF8Encoding]::new($false)
$xml = [System.Text.StringBuilder]::new()
[void]$xml.Append('<mxfile host="app.diagrams.net" type="device">')
function Esc($s) { [System.Security.SecurityElement]::Escape($s) }
function Node($id,$text,$style,$x,$y,$w,$h) {
 [void]$xml.Append("<mxCell id='$id' value='$(Esc $text)' style='$style;whiteSpace=wrap;fontFamily=Arial;fontSize=18;' vertex='1' parent='1'><mxGeometry x='$x' y='$y' width='$w' height='$h' as='geometry'/></mxCell>")
}
function Edge($id,$source,$target,$points) {
 [void]$xml.Append("<mxCell id='$id' source='$source' target='$target' style='endArrow=none;strokeColor=#64748b;strokeWidth=1.3;' edge='1' parent='1'><mxGeometry relative='1' as='geometry'><Array as='points'>")
 foreach($p in $points) { [void]$xml.Append("<mxPoint x='$($p[0])' y='$($p[1])'/>") }
 [void]$xml.Append('</Array></mxGeometry></mxCell>')
}
function Text($s,$x,$y,$w,$h,$size=18) {
 $font=[System.Drawing.Font]::new('Arial',$size,[System.Drawing.FontStyle]::Regular,[System.Drawing.GraphicsUnit]::Pixel)
 $fmt=[System.Drawing.StringFormat]::new();$fmt.Alignment='Center';$fmt.LineAlignment='Center'
 $g.DrawString($s,$font,[System.Drawing.Brushes]::Black,[System.Drawing.RectangleF]::new($x,$y,$w,$h),$fmt)
 $font.Dispose();$fmt.Dispose()
}
function Actor($id,$name,$x,$y) {
 Node $id $name 'shape=umlActor;fillColor=#ffffff;strokeColor=#334155;' ($x-35) $y 70 100
 $g.DrawEllipse($pen,($x-12),$y,24,24);$g.DrawLine($pen,$x,($y+24),$x,($y+62));$g.DrawLine($pen,($x-25),($y+38),($x+25),($y+38));$g.DrawLine($pen,$x,($y+62),($x-23),($y+90));$g.DrawLine($pen,$x,($y+62),($x+23),($y+90))
 Text $name ($x-130) ($y+92) 260 35 21
}
function StartPage($name,$height) {
 [void]$xml.Append("<diagram name='$(Esc $name)'><mxGraphModel page='1' pageWidth='1800' pageHeight='$height'><root><mxCell id='0'/><mxCell id='1' parent='0'/>")
 $script:bmp=[System.Drawing.Bitmap]::new(1800,$height);$script:g=[System.Drawing.Graphics]::FromImage($bmp);$g.Clear([System.Drawing.Color]::White);$g.SmoothingMode='AntiAlias'
 $script:pen=[System.Drawing.Pen]::new([System.Drawing.ColorTranslator]::FromHtml('#475569'),1.8)
}
function EndPage($file) {
 [void]$xml.Append('</root></mxGraphModel></diagram>');$bmp.Save((Join-Path $dir $file),[System.Drawing.Imaging.ImageFormat]::Png);$g.Dispose();$bmp.Dispose();$pen.Dispose()
}
$groups=@(
 @('Nhân viên',@("Xem lộ trình và khóa học`ntheo chức danh","Học và hoàn thành bài học","Xem tiến độ học tập","Làm bài kiểm tra / thi lại","Xem điểm và lịch sử làm bài","Xem lịch, kết quả đánh giá`nđã được phê duyệt","Thực hiện nội dung đào tạo lại`ntheo từng vòng","Xác nhận thực hành / theo dõi`nkhi được giao phụ trách")),
 @('Trưởng ca',@("Theo dõi nhân viên, tiến độ học`nvà kết quả quiz trong phạm vi phụ trách","Xem nhắc hạn đánh giá`nvà cảnh báo nghiệp vụ","Gửi thông báo nhắc nhân viên","Ghi nhận lỗi nghiệp vụ","Tạo lịch đánh giá năng lực`nkhi đủ điều kiện","Đánh giá / đánh giá lại năng lực`nvà gửi kết quả chờ duyệt","Đề xuất nội dung đào tạo lại`nkhi đánh giá chưa đạt","Bổ sung đánh giá bị trả lại;`nxác nhận đào tạo khi được giao")),
 @('Quản lý cửa hàng',@("Xem tổng quan và báo cáo`nhọc tập / đánh giá tại cửa hàng","Theo dõi hồ sơ, học tập`nvà năng lực nhân viên","Tạo ca, phân ca và cập nhật`ntrạng thái làm việc","Phê duyệt / trả lại`nkết quả đánh giá năng lực","Duyệt / từ chối`nđề xuất đào tạo lại","Giao người phụ trách, nội dung`nvà thời hạn đào tạo lại","Theo dõi các vòng đào tạo lại;`ntạo lịch đánh giá lại đủ điều kiện","Xem thông báo và nhắc hạn`nđánh giá của cửa hàng"))
)
StartPage '01 - Nghiệp vụ chính' 1580
Node 'title' 'USE CASE — HỆ THỐNG ĐÀO TẠO BREWLEARN' 'text;fontStyle=1;' 180 10 1440 55
Text 'USE CASE — HỆ THỐNG ĐÀO TẠO BREWLEARN' 180 10 1440 55 28
Node 'system' 'BREWLEARN — HỌC TẬP, ĐÁNH GIÁ VÀ ĐÀO TẠO LẠI' 'rounded=0;fillColor=none;verticalAlign=top;spacingTop=15;' 120 240 1620 1180
$g.DrawRectangle($pen,120,240,1620,1180);Text 'BREWLEARN — HỌC TẬP, ĐÁNH GIÁ VÀ ĐÀO TẠO LẠI' 150 250 1560 45 23
for($c=0;$c -lt 3;$c++) {
 $x=235+$c*535;$ax=$x+205;Actor "a$c" $groups[$c][0] $ax 95
 for($r=0;$r -lt 8;$r++) {
  $y=325+$r*130;$id='UC'+('{0:D2}' -f ($c*8+$r+1));$label="$id`n$($groups[$c][1][$r])"
  Node $id $label 'ellipse;fillColor=#edf4ff;strokeColor=#4f75b0;align=center;' $x $y 410 90
  $trunk=$x-35-$r*5
  Edge "e$id" "a$c" $id @(@($trunk,225),@($trunk,($y+45)))
  $g.DrawLine($pen,$ax,225,$trunk,225);$g.DrawLine($pen,$trunk,225,$trunk,($y+45));$g.DrawLine($pen,$trunk,($y+45),$x,($y+45))
  $fill=[System.Drawing.SolidBrush]::new([System.Drawing.ColorTranslator]::FromHtml('#edf4ff'));$g.FillEllipse($fill,$x,$y,410,90);$g.DrawEllipse($pen,$x,$y,410,90);$fill.Dispose();Text $label ($x+30) ($y+5) 350 80 18
 }
}
$note="Liên kết liền: tác nhân tham gia use case. Các đường cùng tác nhân được bó gọn để dễ đọc.`nĐăng nhập là tiền điều kiện. Đề xuất đào tạo lại áp dụng khi chưa đạt; các bước tiếp theo phụ thuộc trạng thái được duyệt.`nChức năng chung cho cả 3 vai trò nằm ở trang 02. Sơ đồ thể hiện nghiệp vụ, không phải thứ tự xử lý."
Node 'note' $note 'text;fontColor=#475569;' 130 1440 1610 110;Text $note 130 1440 1610 110 18
EndPage 'BrewLearn-UseCase-fixed.png'
StartPage '02 - Chức năng chung' 920
Node 'title' 'BREWLEARN — CHỨC NĂNG CHUNG CHO 3 VAI TRÒ' 'text;fontStyle=1;' 100 20 1600 60;Text 'BREWLEARN — CHỨC NĂNG CHUNG CHO 3 VAI TRÒ' 100 20 1600 60 28
Node 'system' 'BREWLEARN' 'rounded=0;fillColor=none;verticalAlign=top;' 650 120 1050 660;$g.DrawRectangle($pen,650,120,1050,660);Text 'BREWLEARN' 650 130 1050 40 23
$shared=@("Đăng nhập / đăng xuất","Cập nhật tài khoản và mật khẩu","Xem và đánh dấu đã đọc thông báo","Tra cứu SOP, chính sách;`ngửi và xem yêu cầu hỗ trợ")
for($a=0;$a -lt 3;$a++) {Actor "a$a" $groups[$a][0] 235 (160+$a*205)}
for($r=0;$r -lt 4;$r++) {
 $y=210+$r*130;$id='UC'+(25+$r);$label="$id`n$($shared[$r])";Node $id $label 'ellipse;fillColor=#edf4ff;strokeColor=#4f75b0;' 970 $y 510 90
 for($a=0;$a -lt 3;$a++) {Edge "e$a-$r" "a$a" $id @();$g.DrawLine($pen,285,(210+$a*205),970,($y+45))}
 $fill=[System.Drawing.SolidBrush]::new([System.Drawing.ColorTranslator]::FromHtml('#edf4ff'));$g.FillEllipse($fill,970,$y,510,90);$g.DrawEllipse($pen,970,$y,510,90);$fill.Dispose();Text $label 1000 ($y+5) 450 80 21
}
Text 'Phạm vi: Nhân viên — Trưởng ca — Quản lý cửa hàng. Mỗi tác nhân chỉ truy cập dữ liệu theo quyền được cấp.' 100 810 1600 70 20
EndPage 'BrewLearn-UseCase-fixed-common.png'
[void]$xml.Append('</mxfile>')
[System.IO.File]::WriteAllText((Join-Path $dir 'BrewLearn-UseCase-fixed.drawio'),$xml.ToString(),$utf8)
Write-Output 'Created editable Draw.io and two PNG pages.'
