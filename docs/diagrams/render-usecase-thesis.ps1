$ErrorActionPreference='Stop'
Add-Type -AssemblyName System.Drawing
$out=$PSScriptRoot
$doc=[System.Text.StringBuilder]::new('<mxfile host="app.diagrams.net" type="device">')
$pages=[System.Collections.Generic.List[object]]::new()
function Page($slug,$title,$w,$h,$bx,$by,$bw,$bh) {
 $script:p=[pscustomobject]@{slug=$slug;title=$title;w=$w;h=$h;boundary=@($bx,$by,$bw,$bh);nodes=[System.Collections.Generic.List[object]]::new();edges=[System.Collections.Generic.List[object]]::new()};$pages.Add($p)
}
function N($id,$text,$x,$y,$w,$h,$kind='uc') {$p.nodes.Add([pscustomobject]@{id=$id;text=$text;x=$x;y=$y;w=$w;h=$h;kind=$kind})}
function E($s,$t,$sx,$sy,$tx,$ty,$kind='assoc',$label='',$lx=0,$ly=0) {$p.edges.Add([pscustomobject]@{s=$s;t=$t;sx=$sx;sy=$sy;tx=$tx;ty=$ty;kind=$kind;label=$label;lx=$lx;ly=$ly})}
function A($id,$text,$x,$y) {N $id $text $x $y 80 140 'actor'}
function LeftCases($actor,$labels,$prefix,$x,$y,$step,$w=430) {
 $a=$p.nodes | Where-Object id -eq $actor
 for($i=0;$i -lt $labels.Count;$i++) { $id="$prefix$($i+1)";$yy=$y+$i*$step;N $id $labels[$i] $x $yy $w 94;E $actor $id ($a.x+70) ($a.y+48) $x ($yy+47) }
}
Page '01-tong-quat' 'SƠ ĐỒ USE CASE TỔNG QUÁT' 1640 1570 270 120 1100 1320
A 'nv' 'Nhân viên' 85 365;A 'tc' 'Trưởng ca' 85 990;A 'ql' "Quản lý`ncửa hàng" 1470 650
LeftCases 'nv' @("Học khóa đào tạo","Làm bài kiểm tra","Theo dõi kết quả cá nhân","Tham gia đào tạo lại") 'O-NV' 370 235 145 350
LeftCases 'tc' @("Theo dõi nhân viên","Ghi nhận lỗi nghiệp vụ","Lập lịch đánh giá","Đánh giá năng lực") 'O-TC' 370 850 145 350
$manager=@('Theo dõi tình hình cửa hàng','Quản lý ca làm việc','Cập nhật trạng thái làm việc','Xét duyệt kết quả đánh giá','Xét duyệt đề xuất đào tạo lại','Phân công đào tạo lại','Lập lịch đánh giá lại')
for($i=0;$i -lt $manager.Count;$i++) {$y=235+$i*145;N "O-QL$i" $manager[$i] 930 $y 350 94;E 'ql' "O-QL$i" 1480 698 1280 ($y+47)}
N 'caption' "Các chức năng tài khoản, thông báo và hỗ trợ được trình bày ở sơ đồ chức năng chung.`nCác use case tổng quát được làm rõ trong sơ đồ chi tiết tương ứng." 270 1470 1100 65 'note'
Page '02-hoc-tap' 'USE CASE CHI TIẾT — HỌC TẬP CỦA NHÂN VIÊN' 1500 1280 320 120 1080 1040
A 'nv' 'Nhân viên' 110 560
LeftCases 'nv' @("Xem lộ trình đào tạo","Học bài học","Xem tiến độ học tập","Làm bài kiểm tra","Xem kết quả kiểm tra","Xem lịch đánh giá","Xem kết quả đánh giá đã duyệt") 'NV' 650 215 125 550
N 'note' 'Tiền điều kiện: đã đăng nhập. Thi lại tuân theo điều kiện mở bài kiểm tra của hệ thống.' 330 1190 1060 55 'note'
Page '03-danh-gia' 'USE CASE CHI TIẾT — ĐÁNH GIÁ NĂNG LỰC' 1700 1330 300 120 1300 1060
A 'tc' 'Trưởng ca' 90 560
LeftCases 'tc' @("Theo dõi học tập của nhân viên","Xem nhắc hạn đánh giá","Gửi thông báo nhắc nhân viên","Ghi nhận lỗi nghiệp vụ","Lập lịch đánh giá năng lực","Đánh giá năng lực","Bổ sung kết quả bị trả lại") 'TC' 440 215 125 450
N 'DG01' "Chấm điểm theo`n5 tiêu chí" 1110 820 380 94
E 'TC6' 'DG01' 890 887 1110 867 'include' '«include»' 895 790
N 'DG02' "Đề xuất đào tạo lại" 1110 1010 380 94
E 'DG02' 'TC6' 1110 1057 890 905 'extend' "«extend»`n[kết quả chưa đạt]" 910 965
N 'note' "Điểm mở rộng của Đánh giá năng lực: xử lý kết quả chưa đạt trước khi gửi chờ phê duyệt.`nĐánh giá lại sử dụng cùng nghiệp vụ chấm điểm; lịch đánh giá lại do quản lý cửa hàng tạo." 320 1200 1270 80 'note'
Page '04-quan-ly' 'USE CASE CHI TIẾT — QUẢN LÝ CỬA HÀNG' 1500 1320 320 120 1080 1080
A 'ql' "Quản lý cửa hàng" 110 570
LeftCases 'ql' @("Xem báo cáo học tập tại cửa hàng","Xem hồ sơ năng lực nhân viên","Tạo ca làm việc","Phân ca làm việc","Cập nhật trạng thái làm việc","Xét duyệt kết quả đánh giá","Xem nhắc hạn đánh giá") 'QL' 650 215 130 550
N 'note' "Xét duyệt kết quả đánh giá gồm luồng phê duyệt và luồng trả lại kèm lý do.`nPhạm vi dữ liệu: cửa hàng của quản lý đăng nhập." 330 1220 1060 75 'note'
Page '05-dao-tao-lai' 'USE CASE CHI TIẾT — ĐÀO TẠO LẠI' 1680 1240 300 120 1080 1000
A 'ql' "Quản lý`ncửa hàng" 80 350
LeftCases 'ql' @('Xét duyệt đề xuất đào tạo lại','Phân công đào tạo lại','Theo dõi tiến độ đào tạo lại','Lập lịch đánh giá lại') 'DT' 400 215 160 430
A 'nv' 'Nhân viên' 1460 280;N 'DT05' 'Học lại bài học được giao' 925 270 360 94;E 'nv' 'DT05' 1470 328 1285 317
A 'owner' 'Trưởng ca' 1460 750;N 'DT06' "Xác nhận hoàn thành`nđào tạo thực hành (*)" 925 750 360 94;E 'owner' 'DT06' 1470 798 1285 797;E 'nv' 'DT06' 1470 328 1285 797
N 'note' "(*) Vai trò theo phân công: nhân viên hoặc trưởng ca được quản lý giao phụ trách từng vòng.`nLập lịch đánh giá lại chỉ thực hiện khi nội dung đào tạo của vòng hiện tại đã đủ điều kiện hoàn thành.`nĐào tạo thực hành diễn ra ngoài phần mềm; hệ thống ghi nhận phân công và xác nhận hoàn thành." 320 950 1040 125 'note'
N 'caption' 'Nhân viên hoặc trưởng ca chỉ xác nhận hoàn thành đối với vòng đào tạo được giao phụ trách.' 300 1150 1080 55 'note'
Page '06-chuc-nang-chung' 'USE CASE CHI TIẾT — CHỨC NĂNG CHUNG' 1580 1330 490 120 1000 1040
A 'user' "Người dùng" 235 490
LeftCases 'user' @('Đăng nhập','Đăng xuất','Cập nhật thông tin tài khoản','Đổi mật khẩu','Xem thông báo','Tra cứu quy trình và chính sách','Gửi yêu cầu hỗ trợ') 'HT' 790 205 125 500
A 'nv' 'Nhân viên' 50 1050;A 'tc' 'Trưởng ca' 220 1050;A 'ql' "Quản lý`ncửa hàng" 390 1050
E 'nv' 'user' 90 1050 250 630 'generalization';E 'tc' 'user' 260 1050 275 630 'generalization';E 'ql' 'user' 430 1050 300 630 'generalization'
N 'note' "Người dùng là tác nhân tổng quát cho các chức năng chung; ba vai trò kế thừa các tương tác này.`nQuan hệ kế thừa tác nhân không biểu diễn thứ bậc quản lý hoặc quyền truy cập dữ liệu." 490 1190 1000 90 'note'
function Escape($s) {[System.Security.SecurityElement]::Escape($s)}
foreach($page in $pages) {
 $bmp=[System.Drawing.Bitmap]::new(($page.w*2),($page.h*2));$g=[System.Drawing.Graphics]::FromImage($bmp);$g.Clear([System.Drawing.Color]::White);$g.ScaleTransform(2,2);$g.SmoothingMode='AntiAlias';$g.TextRenderingHint='AntiAliasGridFit'
 $pen=[System.Drawing.Pen]::new([System.Drawing.Color]::Black,1.5)
 function Txt($s,$x,$y,$w,$h,$size=22,$bold=$false) {$style=if($bold){[System.Drawing.FontStyle]::Bold}else{[System.Drawing.FontStyle]::Regular};$f=[System.Drawing.Font]::new('Times New Roman',$size,$style,[System.Drawing.GraphicsUnit]::Pixel);$fmt=[System.Drawing.StringFormat]::new();$fmt.Alignment='Center';$fmt.LineAlignment='Center';$g.DrawString($s,$f,[System.Drawing.Brushes]::Black,[System.Drawing.RectangleF]::new($x,$y,$w,$h),$fmt);$f.Dispose();$fmt.Dispose()}
 [void]$doc.Append("<diagram id='$($page.slug)' name='$(Escape $page.title)'><mxGraphModel grid='1' page='1' pageWidth='$($page.w)' pageHeight='$($page.h)'><root><mxCell id='0'/><mxCell id='1' parent='0'/>")
 function Cell($id,$text,$style,$x,$y,$w,$h) {[void]$doc.Append("<mxCell id='$id' value='$(Escape $text)' style='$style;fontFamily=Times New Roman;fontSize=22;whiteSpace=wrap;' vertex='1' parent='1'><mxGeometry x='$x' y='$y' width='$w' height='$h' as='geometry'/></mxCell>")}
 Cell 'title' $page.title 'text;fontStyle=1;' 100 20 ($page.w-200) 60;Txt $page.title 100 20 ($page.w-200) 60 30 $true
 $b=$page.boundary;Cell 'boundary' 'HỆ THỐNG BREWLEARN' 'rounded=0;fillColor=none;strokeColor=#000000;strokeWidth=1.5;verticalAlign=top;spacingTop=18;' $b[0] $b[1] $b[2] $b[3];$g.DrawRectangle($pen,$b[0],$b[1],$b[2],$b[3]);Txt 'HỆ THỐNG BREWLEARN' $b[0] ($b[1]+12) $b[2] 45 24 $true
 foreach($edge in $page.edges) {
  $a=$page.nodes | Where-Object id -eq $edge.s;$z=$page.nodes | Where-Object id -eq $edge.t
  $ex=($edge.sx-$a.x)/$a.w;$ey=($edge.sy-$a.y)/$a.h;$ix=($edge.tx-$z.x)/$z.w;$iy=($edge.ty-$z.y)/$z.h
  $arrow=if($edge.kind -eq 'generalization'){'block'}elseif($edge.kind -eq 'assoc'){'none'}else{'open'};$dash=if($edge.kind -in @('include','extend')){1}else{0}
  [void]$doc.Append("<mxCell id='e-$($edge.s)-$($edge.t)' value='$(Escape $edge.label)' source='$($edge.s)' target='$($edge.t)' edge='1' parent='1' style='endArrow=$arrow;endFill=0;dashed=$dash;strokeColor=#000000;strokeWidth=1.5;exitX=$ex;exitY=$ey;exitPerimeter=0;entryX=$ix;entryY=$iy;entryPerimeter=0;fontFamily=Times New Roman;fontSize=20;labelBackgroundColor=#ffffff;'><mxGeometry relative='1' as='geometry'/></mxCell>")
  $pen.DashStyle=if($dash){'Dash'}else{'Solid'};$g.DrawLine($pen,$edge.sx,$edge.sy,$edge.tx,$edge.ty);$pen.DashStyle='Solid'
  if($arrow -ne 'none') {
   $dx=$edge.tx-$edge.sx;$dy=$edge.ty-$edge.sy;$len=[Math]::Sqrt($dx*$dx+$dy*$dy);$ux=$dx/$len;$uy=$dy/$len;$size=if($arrow -eq 'block'){19}else{13};$spread=if($arrow -eq 'block'){10}else{6}
   $tip=[System.Drawing.PointF]::new($edge.tx,$edge.ty);$v1=[System.Drawing.PointF]::new(($edge.tx-$ux*$size-$uy*$spread),($edge.ty-$uy*$size+$ux*$spread));$v2=[System.Drawing.PointF]::new(($edge.tx-$ux*$size+$uy*$spread),($edge.ty-$uy*$size-$ux*$spread))
   if($arrow -eq 'block') {$pts=[System.Drawing.PointF[]]@($tip,$v1,$v2);$g.FillPolygon([System.Drawing.Brushes]::White,$pts);$g.DrawPolygon($pen,$pts)} else {$g.DrawLine($pen,$tip,$v1);$g.DrawLine($pen,$tip,$v2)}
  }
  if($edge.label) {$g.FillRectangle([System.Drawing.Brushes]::White,$edge.lx,$edge.ly,210,65);Txt $edge.label $edge.lx $edge.ly 210 65 20}
 }
 foreach($n in $page.nodes) {
  if($n.kind -eq 'uc') {Cell $n.id $n.text 'ellipse;fillColor=#ffffff;strokeColor=#000000;strokeWidth=1.5;' $n.x $n.y $n.w $n.h;$g.FillEllipse([System.Drawing.Brushes]::White,$n.x,$n.y,$n.w,$n.h);$g.DrawEllipse($pen,$n.x,$n.y,$n.w,$n.h);Txt $n.text ($n.x+24) ($n.y+8) ($n.w-48) ($n.h-16) 23}
  elseif($n.kind -eq 'actor') {Cell $n.id $n.text $(if($n.id -eq 'user') {'shape=umlActor;fillColor=#ffffff;strokeColor=#000000;verticalLabelPosition=top;verticalAlign=bottom;'} else {'shape=umlActor;fillColor=#ffffff;strokeColor=#000000;verticalLabelPosition=bottom;verticalAlign=top;'}) $n.x $n.y $n.w $n.h;$cx=$n.x+40;$yy=$n.y;$g.DrawEllipse($pen,($cx-14),$yy,28,28);$g.DrawLine($pen,$cx,($yy+28),$cx,($yy+77));$g.DrawLine($pen,($cx-30),($yy+48),($cx+30),($yy+48));$g.DrawLine($pen,$cx,($yy+77),($cx-28),($yy+112));$g.DrawLine($pen,$cx,($yy+77),($cx+28),($yy+112));if($n.id -eq 'user') {Txt $n.text ($n.x-75) ($yy-65) 230 55 24} else {Txt $n.text ($n.x-75) ($yy+117) 230 70 24}}
  else {Cell $n.id $n.text 'text;fontSize=20;' $n.x $n.y $n.w $n.h;Txt $n.text $n.x $n.y $n.w $n.h 20}
 }
 [void]$doc.Append('</root></mxGraphModel></diagram>');$bmp.SetResolution(300,300);$bmp.Save((Join-Path $out "UseCase-do-an-$($page.slug).png"),[System.Drawing.Imaging.ImageFormat]::Png);$pen.Dispose();$g.Dispose();$bmp.Dispose()
}
[void]$doc.Append('</mxfile>');[System.IO.File]::WriteAllText((Join-Path $out 'BrewLearn-UseCase-do-an.drawio'),$doc.ToString(),[System.Text.UTF8Encoding]::new($false))
Write-Output "Generated $($pages.Count) UML diagrams and editable Draw.io."


