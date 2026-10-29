"""Sinh 4 sơ đồ hoạt động (UML) cho báo cáo BrewLearn: XML Draw.io + SVG xem trước.

Chạy: python docs/diagrams/create_activity_diagrams.py
"""
import html
import xml.etree.ElementTree as E
from pathlib import Path

BASE = Path(__file__).parent
FONT = 'fontFamily=Times New Roman;fontSize=13;fontColor=#1f2933;'
LANE_A = (20, 350)
LANE_B = (350, 1070)
COLUMNS = {'A': 185, 'c1': 470, 'c2': 690, 'c3': 900}
WIDTH = {'A': 210, 'c1': 190, 'c2': 210, 'c3': 170}
ANCHORS = {'t': (0.5, 0), 'b': (0.5, 1), 'l': (0, 0.5), 'r': (1, 0.5)}


class Diagram:
    def __init__(self, key, title, actor, height):
        self.key, self.title, self.actor, self.height = key, title, actor, height
        self.nodes, self.edges = {}, []

    def node(self, name, kind, column, cy, label=''):
        cx = COLUMNS[column]
        if kind == 'action':
            w, h = WIDTH[column], 44 if len(label) <= 30 else 56 if len(label) <= 62 else 70
        elif kind == 'decision':
            w, h = 170, 70
        elif kind == 'merge':
            w, h = 34, 34
        elif kind == 'start':
            w, h = 30, 30
        else:
            w, h = 34, 34
        self.nodes[name] = dict(kind=kind, label=label, x=cx - w / 2, y=cy - h / 2, w=w, h=h)

    def point(self, name, side):
        n = self.nodes[name]
        fx, fy = ANCHORS[side]
        return n['x'] + n['w'] * fx, n['y'] + n['h'] * fy

    def edge(self, source, target, exit='b', entry='t', label='', via=()):
        self.edges.append(dict(source=source, target=target, exit=exit, entry=entry, label=label, via=list(via)))

    def path(self, e):
        return [self.point(e['source'], e['exit']), *e['via'], self.point(e['target'], e['entry'])]


STYLES = {
    'action': 'rounded=1;arcSize=30;whiteSpace=wrap;html=1;fillColor=#FFF8EE;strokeColor=#6E4E30;strokeWidth=1.4;' + FONT,
    'decision': 'rhombus;whiteSpace=wrap;html=1;fillColor=#FFFFFF;strokeColor=#6E4E30;strokeWidth=1.4;' + FONT.replace('13', '12'),
    'merge': 'rhombus;html=1;fillColor=#FFFFFF;strokeColor=#6E4E30;strokeWidth=1.4;',
    'start': 'ellipse;html=1;fillColor=#2C190E;strokeColor=#2C190E;',
    'end': 'ellipse;html=1;shape=doubleEllipse;fillColor=#2C190E;strokeColor=#2C190E;strokeWidth=2;',
}
EDGE = 'edgeStyle=none;html=1;rounded=0;endArrow=open;endSize=9;strokeColor=#3b2a1e;strokeWidth=1.3;labelBackgroundColor=#FFFFFF;' + FONT.replace('13', '12')
LANE = 'swimlane;html=1;startSize=34;horizontal=1;fillColor=#F3ECE1;swimlaneFillColor=#FFFFFF;strokeColor=#6E4E30;fontStyle=1;' + FONT


def to_drawio(d, root):
    def cell(**attrs):
        return E.SubElement(root, 'mxCell', {k: str(v) for k, v in attrs.items()})

    for lane_id, (x0, x1), name in (('laneA', LANE_A, d.actor), ('laneB', LANE_B, 'Hệ thống BrewLearn')):
        c = cell(id=f'{d.key}-{lane_id}', value=name, style=LANE, vertex=1, parent=1)
        E.SubElement(c, 'mxGeometry', x=str(x0), y='20', width=str(x1 - x0), height=str(d.height - 40), attrib={'as': 'geometry'})
    for name, n in d.nodes.items():
        c = cell(id=f'{d.key}-{name}', value=n['label'], style=STYLES[n['kind']], vertex=1, parent=1)
        E.SubElement(c, 'mxGeometry', x=f"{n['x']:.0f}", y=f"{n['y']:.0f}", width=f"{n['w']:.0f}", height=f"{n['h']:.0f}", attrib={'as': 'geometry'})
    for i, e in enumerate(d.edges):
        (ex, ey), (nx, ny) = ANCHORS[e['exit']], ANCHORS[e['entry']]
        style = EDGE + f'exitX={ex};exitY={ey};exitDx=0;exitDy=0;entryX={nx};entryY={ny};entryDx=0;entryDy=0;'
        c = cell(id=f'{d.key}-e{i}', value=e['label'], style=style, edge=1, parent=1, source=f"{d.key}-{e['source']}", target=f"{d.key}-{e['target']}")
        g = E.SubElement(c, 'mxGeometry', x='-0.55' if e['label'] else '0', relative='1', attrib={'as': 'geometry'})
        if e['via']:
            points = E.SubElement(g, 'Array', attrib={'as': 'points'})
            for px, py in e['via']:
                E.SubElement(points, 'mxPoint', x=f'{px:.0f}', y=f'{py:.0f}')


def to_svg(d):
    out = [f'<svg xmlns="http://www.w3.org/2000/svg" width="1090" height="{d.height}" viewBox="0 0 1090 {d.height}" font-family="Times New Roman">',
           '<rect width="100%" height="100%" fill="#fff"/>',
           '<defs><marker id="a" markerWidth="10" markerHeight="10" refX="9" refY="5" orient="auto"><path d="M1,1 L9,5 L1,9" fill="none" stroke="#3b2a1e" stroke-width="1.4"/></marker></defs>']
    for (x0, x1), name in ((LANE_A, d.actor), (LANE_B, 'Hệ thống BrewLearn')):
        out.append(f'<rect x="{x0}" y="20" width="{x1 - x0}" height="{d.height - 40}" fill="none" stroke="#6E4E30"/>')
        out.append(f'<rect x="{x0}" y="20" width="{x1 - x0}" height="34" fill="#F3ECE1" stroke="#6E4E30"/>')
        out.append(f'<text x="{(x0 + x1) / 2}" y="42" text-anchor="middle" font-size="14" font-weight="bold">{html.escape(name)}</text>')
    for e in d.edges:
        pts = d.path(e)
        out.append('<polyline fill="none" stroke="#3b2a1e" stroke-width="1.3" marker-end="url(#a)" points="' + ' '.join(f'{x:.0f},{y:.0f}' for x, y in pts) + '"/>')
        if e['label']:
            (x1, y1), (x2, y2) = pts[0], pts[1]
            lx, ly = x1 + (x2 - x1) * 0.45, y1 + (y2 - y1) * 0.45
            out.append(f'<text x="{lx:.0f}" y="{ly - 4:.0f}" text-anchor="middle" font-size="12" fill="#7a3b0c" stroke="#fff" stroke-width="4" paint-order="stroke">{html.escape(e["label"])}</text>')
    for n in d.nodes.values():
        x, y, w, h = n['x'], n['y'], n['w'], n['h']
        if n['kind'] == 'action':
            out.append(f'<rect x="{x}" y="{y}" width="{w}" height="{h}" rx="14" fill="#FFF8EE" stroke="#6E4E30" stroke-width="1.4"/>')
        elif n['kind'] in ('decision', 'merge'):
            out.append(f'<polygon points="{x + w / 2},{y} {x + w},{y + h / 2} {x + w / 2},{y + h} {x},{y + h / 2}" fill="#fff" stroke="#6E4E30" stroke-width="1.4"/>')
        elif n['kind'] == 'start':
            out.append(f'<circle cx="{x + w / 2}" cy="{y + h / 2}" r="{w / 2}" fill="#2C190E"/>')
        else:
            out.append(f'<circle cx="{x + w / 2}" cy="{y + h / 2}" r="{w / 2}" fill="#fff" stroke="#2C190E" stroke-width="2"/><circle cx="{x + w / 2}" cy="{y + h / 2}" r="{w / 2 - 6}" fill="#2C190E"/>')
        if n['label']:
            words, lines, line = n['label'].split(), [], ''
            limit = 26 if n['kind'] == 'action' else 20
            for word in words:
                if len(line + ' ' + word) > limit and line:
                    lines.append(line)
                    line = word
                else:
                    line = (line + ' ' + word).strip()
            lines.append(line)
            start = y + h / 2 - (len(lines) - 1) * 7.5 + 4
            for i, text in enumerate(lines):
                out.append(f'<text x="{x + w / 2}" y="{start + i * 15:.0f}" text-anchor="middle" font-size="12.5">{html.escape(text)}</text>')
    out.append('</svg>')
    return '\n'.join(out)


def quiz():
    d = Diagram('ad1', 'Làm bài kiểm tra', 'Nhân viên', 920)
    d.node('s', 'start', 'A', 80)
    d.node('a1', 'action', 'A', 150, 'Chọn bài kiểm tra cần làm')
    d.node('s1', 'action', 'c2', 150, 'Hiển thị câu hỏi của bài kiểm tra')
    d.node('a2', 'action', 'A', 240, 'Chọn đáp án và nộp bài')
    d.node('d2', 'decision', 'c2', 330, 'Bài này đã được nộp trước đó?')
    d.node('sr', 'action', 'c3', 430, 'Giữ kết quả đã chấm, không lưu thêm')
    d.node('s2', 'action', 'c2', 430, 'Chấm điểm = số câu đúng / tổng số câu × 100')
    d.node('s3', 'action', 'c2', 525, 'So với điểm chuẩn; lưu kết quả lượt làm bài')
    d.node('m', 'merge', 'c2', 610)
    d.node('s4', 'action', 'c2', 690, 'Hiển thị điểm và kết quả Đạt / Chưa đạt')
    d.node('a3', 'action', 'A', 690, 'Xem kết quả bài kiểm tra')
    d.node('d3', 'decision', 'A', 780, 'Đạt điểm chuẩn?')
    d.node('e', 'end', 'A', 865)
    d.edge('s', 'a1')
    d.edge('a1', 's1', 'r', 'l')
    d.edge('s1', 'a2', 'b', 'r', via=[(690, 240)])
    d.edge('a2', 'd2', 'b', 'l', via=[(185, 330)])
    d.edge('d2', 'sr', 'r', 't', '[Đã nộp]', via=[(900, 330)])
    d.edge('d2', 's2', 'b', 't', '[Chưa nộp]')
    d.edge('s2', 's3')
    d.edge('s3', 'm')
    d.edge('sr', 'm', 'b', 'r', via=[(900, 610)])
    d.edge('m', 's4')
    d.edge('s4', 'a3', 'l', 'r')
    d.edge('a3', 'd3')
    d.edge('d3', 'e', 'b', 't', '[Đạt]')
    d.edge('d3', 'a1', 'l', 'l', '[Chưa đạt, làm lại]', via=[(45, 780), (45, 150)])
    return d


def scoring():
    d = Diagram('ad2', 'Chấm điểm năng lực theo năm tiêu chí', 'Trưởng ca', 980)
    d.node('s', 'start', 'A', 80)
    d.node('a1', 'action', 'A', 150, 'Mở lịch đánh giá cần chấm')
    d.node('a2', 'action', 'A', 230, 'Nhập điểm năm tiêu chí và nhận xét')
    d.node('s1', 'action', 'c2', 230, 'Tính D = 0,2N + 0,3K + 0,2Q + 0,15P + 0,15T')
    d.node('s2', 'action', 'c2', 315, 'Xác định kết quả: Đạt khi D ≥ 80 và mọi tiêu chí ≥ 70')
    d.node('d1', 'decision', 'c2', 405, 'Kết quả?')
    d.node('a3', 'action', 'A', 485, 'Chọn đề xuất xử lý và nhập nội dung')
    d.node('d2', 'decision', 'c1', 485, 'Đề xuất hợp lệ?')
    d.node('m', 'merge', 'c2', 580)
    d.node('d3', 'decision', 'c2', 670, 'Hồ sơ đang chờ đánh giá hoặc bị trả lại?')
    d.node('se', 'action', 'c3', 670, 'Báo hồ sơ đã gửi hoặc đã duyệt')
    d.node('ee', 'end', 'c3', 760)
    d.node('s3', 'action', 'c2', 765, 'Lưu điểm, kết quả, đề xuất; chuyển hồ sơ sang Chờ phê duyệt')
    d.node('s4', 'action', 'c2', 850, 'Gửi thông báo “Đánh giá chờ phê duyệt” cho quản lý cửa hàng')
    d.node('a4', 'action', 'A', 850, 'Nhận xác nhận đã gửi đánh giá')
    d.node('e', 'end', 'A', 930)
    d.edge('s', 'a1')
    d.edge('a1', 'a2')
    d.edge('a2', 's1', 'r', 'l')
    d.edge('s1', 's2')
    d.edge('s2', 'd1')
    d.edge('d1', 'a3', 'l', 't', '[Chưa đạt]', via=[(185, 405)])
    d.edge('a3', 'd2', 'r', 'l')
    d.edge('d2', 'a3', 'b', 'b', '[Không] báo lỗi', via=[(470, 545), (185, 545)])
    d.edge('d2', 'm', 'r', 'l', '[Hợp lệ]', via=[(600, 485), (600, 580)])
    d.edge('d1', 'm', 'b', 't', '[Đạt]')
    d.edge('m', 'd3')
    d.edge('d3', 'se', 'r', 'l', '[Không]')
    d.edge('se', 'ee')
    d.edge('d3', 's3', 'b', 't', '[Có]')
    d.edge('s3', 's4')
    d.edge('s4', 'a4', 'l', 'r')
    d.edge('a4', 'e')
    return d


def review():
    d = Diagram('ad3', 'Phê duyệt kết quả đánh giá năng lực', 'Quản lý cửa hàng', 1150)
    d.node('s', 'start', 'A', 80)
    d.node('a1', 'action', 'A', 150, 'Mở danh sách đánh giá chờ phê duyệt')
    d.node('a2', 'action', 'A', 230, 'Xem điểm, nhận xét, đề xuất và thông tin cảnh báo')
    d.node('d1', 'decision', 'A', 325, 'Quyết định?')
    d.node('sr', 'action', 'c3', 325, 'Chuyển hồ sơ sang Trả lại, lưu lý do; thông báo trưởng ca bổ sung')
    d.node('er', 'end', 'c3', 420)
    d.node('s1', 'action', 'c2', 415, 'Chuyển hồ sơ sang Đã duyệt; lưu người duyệt và thời điểm')
    d.node('d2', 'decision', 'c2', 505, 'Là đánh giá lại của vòng đào tạo lại?')
    d.node('d3', 'decision', 'c1', 600, 'Kết quả?')
    d.node('s2', 'action', 'c1', 690, 'Tạo yêu cầu đào tạo lại ở trạng thái Chờ giao')
    d.node('s3', 'action', 'c2', 600, 'Cập nhật kết quả của vòng đào tạo lại')
    d.node('d4', 'decision', 'c2', 690, 'Kết quả?')
    d.node('s4', 'action', 'c3', 690, 'Hoàn tất yêu cầu đào tạo lại')
    d.node('d5', 'decision', 'c2', 780, 'Đã đủ ba vòng?')
    d.node('s5', 'action', 'c3', 780, 'Chuyển yêu cầu về Chờ giao vòng tiếp theo')
    d.node('s6', 'action', 'c2', 870, 'Dừng đào tạo lại; thông báo quản lý xem xét')
    d.node('m', 'merge', 'c2', 955)
    d.node('mL', 'merge', 'c1', 955)
    d.node('mR', 'merge', 'c3', 955)
    d.node('s7', 'action', 'c2', 1030, 'Gửi kết quả chính thức cho nhân viên; thông báo trưởng ca')
    d.node('a3', 'action', 'A', 1030, 'Nhận xác nhận đã phê duyệt')
    d.node('e', 'end', 'A', 1105)
    d.edge('s', 'a1')
    d.edge('a1', 'a2')
    d.edge('a2', 'd1')
    d.edge('d1', 'sr', 'r', 'l', '[Trả lại, nhập lý do]')
    d.edge('sr', 'er')
    d.edge('d1', 's1', 'b', 'l', '[Duyệt]', via=[(185, 415)])
    d.edge('s1', 'd2')
    d.edge('d2', 'd3', 'l', 't', '[Không]', via=[(470, 505)])
    d.edge('d3', 's2', 'b', 't', '[Chưa đạt]')
    d.edge('d3', 'mL', 'l', 'l', '[Đạt]', via=[(375, 600), (375, 955)])
    d.edge('s2', 'mL', 'b', 't')
    d.edge('mL', 'm', 'r', 'l')
    d.edge('d2', 's3', 'b', 't', '[Có]')
    d.edge('s3', 'd4')
    d.edge('d4', 's4', 'r', 'l', '[Đạt]')
    d.edge('d4', 'd5', 'b', 't', '[Chưa đạt]')
    d.edge('d5', 's5', 'r', 'l', '[Chưa]')
    d.edge('d5', 's6', 'b', 't', '[Đủ]')
    d.edge('s4', 'mR', 'r', 'r', via=[(1030, 690), (1030, 955)])
    d.edge('s5', 'mR', 'b', 't')
    d.edge('mR', 'm', 'l', 'r')
    d.edge('s6', 'm')
    d.edge('m', 's7')
    d.edge('s7', 'a3', 'l', 'r')
    d.edge('a3', 'e')
    return d


def assign():
    d = Diagram('ad4', 'Giao vòng đào tạo lại', 'Quản lý cửa hàng', 1010)
    d.node('s', 'start', 'A', 80)
    d.node('a1', 'action', 'A', 150, 'Mở yêu cầu đào tạo lại đang chờ giao')
    d.node('a2', 'action', 'A', 240, 'Chọn người phụ trách, hạn hoàn thành, ngày đánh giá lại và nội dung')
    d.node('d1', 'decision', 'c2', 240, 'Thông tin hợp lệ?')
    d.node('s1', 'action', 'c2', 340, 'Tạo vòng đào tạo lại mới')
    d.node('s2', 'action', 'c2', 425, 'Đặt lại tiến độ các bài học được giao về 0%')
    d.node('s3', 'action', 'c2', 510, 'Yêu cầu làm lại bài kiểm tra (kết quả cũ không được tính)')
    d.node('s4', 'action', 'c2', 595, 'Chuyển yêu cầu sang Đang thực hiện')
    d.node('s5', 'action', 'c2', 685, 'Gửi thông báo kế hoạch cho nhân viên và trưởng ca phụ trách')
    d.node('a3', 'action', 'A', 685, 'Nhận xác nhận đã giao')
    d.node('e', 'end', 'A', 770)
    d.edge('s', 'a1')
    d.edge('a1', 'a2')
    d.edge('a2', 'd1', 'r', 'l')
    d.edge('d1', 'a2', 'b', 'b', '[Không] báo lỗi', via=[(690, 300), (185, 300)])
    d.edge('d1', 's1', 'r', 'r', '[Hợp lệ]', via=[(830, 240), (830, 340)])
    d.edge('s1', 's2')
    d.edge('s2', 's3')
    d.edge('s3', 's4')
    d.edge('s4', 's5')
    d.edge('s5', 'a3', 'l', 'r')
    d.edge('a3', 'e')
    return d


def main():
    diagrams = [quiz(), scoring(), review(), assign()]
    book = E.Element('mxfile', host='app.diagrams.net')
    for d in diagrams:
        single = E.Element('mxfile', host='app.diagrams.net')
        for target in (book, single):
            page = E.SubElement(target, 'diagram', id=d.key, name=d.title)
            model = E.SubElement(page, 'mxGraphModel', dx='1200', dy='900', grid='1', gridSize='10', guides='1', tooltips='1', connect='1', arrows='1', fold='1', page='1', pageScale='1', pageWidth='1090', pageHeight=str(d.height), math='0', shadow='0')
            root = E.SubElement(model, 'root')
            E.SubElement(root, 'mxCell', id='0')
            E.SubElement(root, 'mxCell', id='1', parent='0')
            to_drawio(d, root)
        stem = f'BrewLearn-Activity-{d.key[-1]}-' + {'ad1': 'Lam-bai-kiem-tra', 'ad2': 'Cham-diem', 'ad3': 'Phe-duyet', 'ad4': 'Giao-vong'}[d.key]
        E.indent(single)
        (BASE / f'{stem}.xml').write_text(E.tostring(single, encoding='unicode'), encoding='utf-8')
        (BASE / f'{stem}.svg').write_text(to_svg(d), encoding='utf-8')
    E.indent(book)
    (BASE / 'BrewLearn-Activity-4-so-do.drawio').write_text(E.tostring(book, encoding='unicode'), encoding='utf-8')
    print('ok')


if __name__ == '__main__':
    main()
