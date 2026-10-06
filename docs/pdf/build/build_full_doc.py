import os
import re
import markdown
import subprocess

DOCS_DIR = r"D:\laragon\rindam\docs"
BUILD_DIR = r"D:\laragon\rindam\docs\pdf\build"
OUTPUT_HTML = os.path.join(BUILD_DIR, "dokumen_lengkap.html")
OUTPUT_PDF = r"D:\laragon\rindam\docs\pdf\SIPANDU_WBK_Dokumen_Komprehensif.pdf"

files = [
    ("01_Dokumen_Penelitian.md", "BAGIAN I: DOKUMEN PENELITIAN"),
    ("02_Blueprint_Sistem.md", "BAGIAN II: BLUEPRINT & ARSITEKTUR SISTEM"),
    ("05_Peta_ERD_Lengkap.md", "BAGIAN III: PETA RELASI ENTITAS BASIS DATA (ERD MAP)"),
    ("03_Kerangka_Kerja_Teknis.md", "BAGIAN IV: KERANGKA KERJA TEKNIS"),
    ("04_Timeline_Proyek.md", "BAGIAN V: RENCANA KERJA & TIMELINE 2 BULAN")
]

combined_body = []

md_converter = markdown.Markdown(extensions=['tables', 'fenced_code', 'toc', 'nl2br'])

for filename, part_title in files:
    filepath = os.path.join(DOCS_DIR, filename)
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Pre-process markdown:
    # 1. Turn ```mermaid into <div class="mermaid">...</div>
    def replace_mermaid(match):
        code = match.group(1).strip()
        return f'<div class="mermaid-box"><pre class="mermaid">\n{code}\n</pre></div>'

    content = re.sub(r'```mermaid\n(.*?)```', replace_mermaid, content, flags=re.DOTALL)

    # 2. Convert markdown to HTML
    md_converter.reset()
    html_part = md_converter.convert(content)

    # Wrap each file in a distinct chapter section
    section_html = f"""
    <div class="document-part">
      <div class="part-header-band">
        <span class="part-tag">SIPANDU-WBK · DOKUMENTASI PROYEK</span>
        <h1 class="part-title">{part_title}</h1>
      </div>
      <div class="part-content">
        {html_part}
      </div>
    </div>
    <div class="page-break"></div>
    """
    combined_body.append(section_html)

all_sections_html = "\n".join(combined_body)

# Post-process callouts like > [!NOTE], > [!IMPORTANT]
def clean_callouts(html):
    html = re.sub(r'<blockquote>\s*<p>\s*\[!NOTE\](.*?)</p>\s*</blockquote>', 
                  r'<div class="callout callout-note"><b>CATATAN:</b><p>\1</p></div>', html, flags=re.DOTALL)
    html = re.sub(r'<blockquote>\s*<p>\s*\[!IMPORTANT\](.*?)</p>\s*</blockquote>', 
                  r'<div class="callout callout-important"><b>PENTING:</b><p>\1</p></div>', html, flags=re.DOTALL)
    html = re.sub(r'<blockquote>\s*<p>\s*\[!TIP\](.*?)</p>\s*</blockquote>', 
                  r'<div class="callout callout-tip"><b>PETUNJUK:</b><p>\1</p></div>', html, flags=re.DOTALL)
    html = re.sub(r'<blockquote>\s*<p>\s*\[!WARNING\](.*?)</p>\s*</blockquote>', 
                  r'<div class="callout callout-warning"><b>PERINGATAN:</b><p>\1</p></div>', html, flags=re.DOTALL)
    return html

all_sections_html = clean_callouts(all_sections_html)

full_html = f"""<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Naskah Komprehensif SIPANDU-WBK</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&family=Montserrat:wght@600;700;800;900&display=block" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,1,0&display=block" rel="stylesheet">
<script src="mermaid.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {{
    mermaid.initialize({{
      startOnLoad: true,
      theme: 'neutral',
      themeVariables: {{
        primaryColor: '#E9EEDF',
        primaryTextColor: '#1D2516',
        primaryBorderColor: '#3D5229',
        lineColor: '#3D5229',
        secondaryColor: '#FBF5DF',
        tertiaryColor: '#F3F6EC'
      }},
      flowchart: {{ htmlLabels: true, curve: 'basis' }},
      sequence: {{ actorFontSize: 12, messageFontSize: 11 }}
    }});
  }});
</script>
<style>
:root {{
  --o900: #141C0E;
  --o800: #22301A;
  --o700: #3D5229;
  --o600: #4E6635;
  --o500: #6B7F4A;
  --o200: #D3DCC2;
  --o100: #E9EEDF;
  --o50: #F7F9F2;
  --gold: #C9A227;
  --gold2: #E6C766;
  --goldbg: #FBF5DF;
  --text: #20261D;
  --muted: #5A6650;
  --line: #D8DDCF;
  --red: #B23A2E;
  --green: #2E7D32;
}}

@page {{
  size: A4;
  margin: 22mm 18mm 20mm 20mm;
}}

@page:left {{
  margin: 22mm 18mm 20mm 20mm;
}}

@page:right {{
  margin: 22mm 18mm 20mm 20mm;
}}

* {{
  box-sizing: border-box;
}}

body {{
  font-family: 'Merriweather', serif;
  font-size: 10.5pt;
  line-height: 1.7;
  color: var(--text);
  background: #fff;
  margin: 0;
  padding: 0;
  -webkit-print-color-adjust: exact;
  print-color-adjust: exact;
}}

.ms {{
  font-family: 'Material Symbols Rounded';
  font-weight: normal;
  font-style: normal;
  line-height: 1;
  display: inline-block;
  vertical-align: middle;
}}

h1, h2, h3, h4, h5, h6, .sans {{
  font-family: 'Montserrat', 'Inter', sans-serif;
  color: var(--o800);
  line-height: 1.3;
  page-break-after: avoid;
  break-after: avoid;
}}

h1 {{
  font-size: 19pt;
  font-weight: 800;
  border-bottom: 2px solid var(--o700);
  padding-bottom: 6px;
  margin-top: 26pt;
  margin-bottom: 14pt;
}}

h2 {{
  font-size: 14.5pt;
  font-weight: 700;
  color: var(--o700);
  margin-top: 20pt;
  margin-bottom: 10pt;
  border-left: 4px solid var(--gold);
  padding-left: 10px;
}}

h3 {{
  font-size: 12pt;
  font-weight: 700;
  color: var(--o800);
  margin-top: 15pt;
  margin-bottom: 8pt;
}}

h4 {{
  font-size: 11pt;
  font-weight: 700;
  color: var(--o600);
  margin-top: 12pt;
  margin-bottom: 6pt;
}}

p {{
  margin-top: 0;
  margin-bottom: 10pt;
  text-align: justify;
}}

ul, ol {{
  margin-top: 0;
  margin-bottom: 10pt;
  padding-left: 20px;
}}

li {{
  margin-bottom: 4pt;
}}

.page-break {{
  page-break-after: always;
  break-after: page;
}}

/* COVER PAGE */
.cover-page {{
  height: 250mm;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  page-break-after: always;
  break-after: page;
  padding: 10mm 5mm;
  text-align: center;
}}

.cover-badge {{
  display: inline-block;
  font-family: 'Inter', sans-serif;
  font-weight: 700;
  font-size: 10pt;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--gold);
  background: var(--o800);
  padding: 6px 18px;
  border-radius: 999px;
  margin-bottom: 20px;
}}

.cover-title {{
  font-family: 'Montserrat', sans-serif;
  font-weight: 900;
  font-size: 32pt;
  line-height: 1.1;
  color: var(--o900);
  margin: 15px 0 10px;
  letter-spacing: -0.02em;
}}

.cover-title span {{
  color: var(--gold);
}}

.cover-sub {{
  font-family: 'Inter', sans-serif;
  font-size: 13pt;
  font-weight: 600;
  color: var(--o700);
  line-height: 1.45;
  max-width: 650px;
  margin: 0 auto 30px;
}}

.cover-art {{
  width: 100%;
  max-height: 240px;
  object-fit: cover;
  border-radius: 14px;
  box-shadow: 0 10px 25px rgba(20,28,14,0.18);
  margin: 10px auto;
  border: 1px solid var(--line);
}}

.cover-pills {{
  display: flex;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
  margin: 15px auto 25px;
}}

.cover-pill {{
  font-family: 'Inter', sans-serif;
  font-size: 8.5pt;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 6px;
  background: var(--o50);
  border: 1px solid var(--o200);
  color: var(--o800);
}}

.cover-meta {{
  border-top: 2px solid var(--o700);
  padding-top: 15px;
  font-family: 'Inter', sans-serif;
  font-size: 9.5pt;
  color: var(--muted);
  display: flex;
  justify-content: space-around;
  margin-top: 20px;
}}

.cover-meta div b {{
  display: block;
  font-size: 11pt;
  color: var(--o800);
  margin-top: 3px;
}}

/* VALIDATION SHEET */
.validation-sheet {{
  padding: 10mm 5mm;
  page-break-after: always;
  break-after: page;
}}

.val-title {{
  text-align: center;
  font-family: 'Montserrat', sans-serif;
  font-size: 15pt;
  font-weight: 800;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--o800);
  border-bottom: 2px solid var(--line);
  padding-bottom: 8px;
  margin-bottom: 25px;
}}

.sig-box-wrapper {{
  display: flex;
  justify-content: space-between;
  margin-top: 40px;
  page-break-inside: avoid;
}}

.sig-box {{
  width: 45%;
  text-align: center;
  font-family: 'Inter', sans-serif;
  font-size: 9.5pt;
}}

.sig-space {{
  height: 70px;
}}

.sig-line {{
  border-bottom: 1px solid #000;
  display: inline-block;
  width: 80%;
  margin-bottom: 4px;
}}

/* PART HEADER BAND */
.part-header-band {{
  background: linear-gradient(135deg, var(--o800), var(--o700));
  color: #fff;
  padding: 16px 20px;
  border-radius: 10px;
  margin-bottom: 20px;
  page-break-after: avoid;
  break-after: avoid;
}}

.part-tag {{
  font-family: 'Inter', sans-serif;
  font-size: 8pt;
  font-weight: 700;
  letter-spacing: 0.15em;
  color: var(--gold2);
  text-transform: uppercase;
}}

.part-title {{
  font-family: 'Montserrat', sans-serif;
  font-size: 16pt;
  font-weight: 800;
  color: #fff;
  margin: 5px 0 0;
  border-bottom: none;
  padding-bottom: 0;
}}

/* TABLES */
table {{
  width: 100%;
  border-collapse: collapse;
  font-family: 'Inter', sans-serif;
  font-size: 9pt;
  margin: 14pt 0;
  page-break-inside: avoid;
  background: #fff;
}}

table th {{
  background: var(--o800);
  color: #fff;
  font-weight: 700;
  text-align: left;
  padding: 7px 10px;
  border: 1px solid var(--o800);
}}

table td {{
  padding: 6px 10px;
  border: 1px solid var(--line);
  vertical-align: top;
}}

table tr:nth-child(even) td {{
  background: var(--o50);
}}

/* CALLOUTS */
.callout {{
  font-family: 'Inter', sans-serif;
  font-size: 9.5pt;
  padding: 10px 14px;
  border-radius: 8px;
  margin: 12pt 0;
  border-left: 4px solid var(--o700);
  background: var(--o50);
  page-break-inside: avoid;
}}

.callout b {{
  display: block;
  font-size: 9pt;
  letter-spacing: 0.06em;
  margin-bottom: 4px;
}}

.callout-note {{
  border-color: var(--o600);
  background: var(--o50);
}}
.callout-note b {{ color: var(--o700); }}

.callout-important {{
  border-color: var(--gold);
  background: var(--goldbg);
}}
.callout-important b {{ color: #8A6A0C; }}

.callout-warning {{
  border-color: var(--red);
  background: #FDF2F0;
}}
.callout-warning b {{ color: var(--red); }}

.callout-tip {{
  border-color: var(--green);
  background: #F1F8F2;
}}
.callout-tip b {{ color: var(--green); }}

/* CODE BLOCKS */
pre {{
  background: #1B2416;
  color: #EDF2E8;
  font-family: 'Consolas', 'Courier New', monospace;
  font-size: 8.5pt;
  padding: 10px 14px;
  border-radius: 8px;
  overflow-x: auto;
  line-height: 1.45;
  margin: 10pt 0;
  page-break-inside: avoid;
}}

code {{
  font-family: 'Consolas', 'Courier New', monospace;
  font-size: 9pt;
  background: var(--o100);
  color: var(--o900);
  padding: 2px 5px;
  border-radius: 4px;
}}

pre code {{
  background: transparent;
  color: inherit;
  padding: 0;
}}

/* MERMAID BOX */
.mermaid-box {{
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 14px;
  margin: 14pt 0;
  text-align: center;
  page-break-inside: avoid;
}}

.mermaid {{
  margin: 0 auto;
}}

/* HR */
hr {{
  border: none;
  border-top: 1px solid var(--line);
  margin: 20pt 0;
}}

/* BADGES IN TEXT */
.badge {{
  display: inline-block;
  font-family: 'Inter', sans-serif;
  font-size: 8pt;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 4px;
  background: var(--o100);
  color: var(--o800);
}}

/* SUMMARY STATS GRID */
.stat-grid {{
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
  margin: 14pt 0;
}}

.stat-card {{
  border: 1px solid var(--line);
  border-radius: 8px;
  padding: 10px;
  text-align: center;
  background: var(--o50);
}}

.stat-card .val {{
  font-family: 'Montserrat', sans-serif;
  font-size: 18pt;
  font-weight: 800;
  color: var(--o700);
}}

.stat-card .lbl {{
  font-family: 'Inter', sans-serif;
  font-size: 8pt;
  color: var(--muted);
  text-transform: uppercase;
  margin-top: 2px;
}}
</style>
</head>
<body>

<!-- HALAMAN SAMPUL (COVER) -->
<div class="cover-page">
  <div>
    <div class="cover-badge">NASKAH DOKUMEN KOMPREHENSIF PROYEK</div>
    <div class="cover-title">SIPANDU<span>-WBK</span></div>
    <div class="cover-sub">
      SISTEM INFORMASI PEMBINAAN SATUAN PENDIDIKAN TERPADU<br>
      BERBASIS WILAYAH BEBAS DARI KORUPSI (WBK)<br>
      DENGAN INTEGRASI E-LEARNING DAN PERPUSTAKAAN DIGITAL
    </div>
    
    <img src="cover.jpg" class="cover-art" alt="Cover Artwork">

    <div class="cover-pills">
      <span class="cover-pill">6 Komponen Binsat</span>
      <span class="cover-pill">Indeks Kesiapan Satuan</span>
      <span class="cover-pill">E-Learning & Ujian Daring</span>
      <span class="cover-pill">E-Pustaka Terintegrasi</span>
      <span class="cover-pill">Portal ZI & WBS Bertiket</span>
      <span class="cover-pill">Laravel & Filament</span>
    </div>
  </div>

  <div class="cover-meta">
    <div>
      INSTANSI PELAKSANA
      <b>Resimen Induk Daerah Militer (Rindam)</b>
    </div>
    <div>
      KERANGKA PENGEMBANGAN
      <b>Scrum 8 Minggu (Okt – Nov 2026)</b>
    </div>
    <div>
      STATUS DOKUMEN
      <b>Rancangan & Blueprint v1.0</b>
    </div>
  </div>
</div>

<!-- HALAMAN PENGESAHAN -->
<div class="validation-sheet">
  <div class="val-title">LEMBAR PENGESAHAN DOKUMEN PROYEK</div>
  <p style="text-align:center; font-family:'Inter',sans-serif; font-size:10pt; max-width:600px; margin:0 auto 20px;">
    Rancangan Bangun Sistem Informasi Pembinaan Satuan Pendidikan Terpadu Berbasis Wilayah Bebas dari Korupsi (SIPANDU-WBK) beserta Blueprint Teknis dan Timeline Pelaksanaan 2 Bulan ini telah disusun dan disepakati untuk dijadikan pedoman teknis pelaksanaan proyek.
  </p>

  <table style="width:90%; margin:20px auto 40px; font-size:9.5pt;">
    <tr><td style="width:30%; font-weight:bold;">Nama Program</td><td>Sistem Informasi Pembinaan Satuan Pendidikan Terpadu (SIPANDU-WBK)</td></tr>
    <tr><td style="font-weight:bold;">Sub-Sistem</td><td>SIM Binsat (6 Komponen), E-Learning (LMS), E-Pustaka, Portal Zona Integritas</td></tr>
    <tr><td style="font-weight:bold;">Basis Teknologi</td><td>Laravel, Filament Admin, Livewire 3, MySQL/MariaDB, Laragon Dev</td></tr>
    <tr><td style="font-weight:bold;">Durasi Proyek</td><td>8 Minggu / 40 Hari Kerja (05 Oktober 2026 – 27 November 2026)</td></tr>
    <tr><td style="font-weight:bold;">Sasaran Akhir</td><td>Aplikasi Teruji (Blackbox ≥ 95%, SUS ≥ 68) dan Siap Dioperasikan (Go-Live)</td></tr>
  </table>

  <div class="sig-box-wrapper">
    <div class="sig-box">
      Mengetahui,<br>
      <b>Product Owner / Pejabat Satuan</b><br>
      <div class="sig-space"></div>
      <div class="sig-line"></div><br>
      Pangkat / Korps / NRP
    </div>
    <div class="sig-box">
      Disusun oleh,<br>
      <b>Project Manager / Tim Pengembang</b><br>
      <div class="sig-space"></div>
      <div class="sig-line"></div><br>
      Lead System Architect
    </div>
  </div>
</div>

<!-- KATA PENGANTAR / RINGKASAN EKSEKUTIF -->
<div class="validation-sheet">
  <h2>Ringkasan Eksekutif</h2>
  <p>
    Kesiapan operasional satuan pendidikan militer (seperti Rindam) bertumpu pada keberhasilan <b>6 Komponen Pembinaan Satuan (Binsat)</b>: Organisasi, Personel, Materiil, Fasilitas/Pangkalan, Latihan, dan Doktrin/Piranti Lunak. Di era transformasi digital dan tuntutan reformasi birokrasi, satuan juga diwajibkan membangun <b>Zona Integritas (ZI) menuju Wilayah Bebas dari Korupsi (WBK)</b> sebagaimana diamanatkan dalam PermenPAN-RB No. 90 Tahun 2021.
  </p>
  <p>
    Selama ini, pengelolaan data Binsat dilakukan secara terpisah oleh masing-masing seksi (Pers, Log, Ops/Diklat), distribusi bahan ajar (hanjar) dan evaluasi belajar siswa masih konvensional, koleksi pustaka belum terdigitalisasi, serta sarana pengaduan dan keterbukaan informasi belum terpadu.
  </p>
  <p>
    <b>SIPANDU-WBK</b> hadir sebagai solusi terpadu satu atap (single platform) berbasis framework <b>Laravel</b> yang menyatukan lima pilar fungsional:
  </p>
  <ol>
    <li><b>SIM Binsat 6 Komponen:</b> Digitalisasi pencatatan, pemantauan riil vs TOP, jadwal pemeliharaan alat, aset pangkalan, kalender latihan, dan repositori naskah doktrin. Dilengkapi fitur unggulan <b>Indeks Kesiapan Binsat</b> (skor otomatis 0–100).</li>
    <li><b>E-Learning (LMS):</b> Pembelajaran model <i>blended learning</i>, distribusi hanjar digital, bank soal, kuis & ujian daring ber-timer dengan penilaian otomatis, presensi, rapor, dan sertifikat ber-QR Code.</li>
    <li><b>E-Pustaka (Digital Library):</b> Katalog koleksi fisik dan e-book dengan viewer interaktif (PDF.js) anti-unduh dilengkapi watermark dinamis (nama/NRP pembaca), serta sirkulasi barcode.</li>
    <li><b>Portal Zona Integritas (WBK):</b> Keterbukaan informasi publik, standar & maklumat pelayanan, Whistleblowing System (WBS) bertiket dengan pelacakan status dan alarm SLA tindak lanjut, pelaporan gratifikasi, serta Survei Kepuasan Masyarakat (SKM, IPAK, IPKP).</li>
    <li><b>Dashboard Pimpinan (Executive Command):</b> Instrumen visual untuk Danrindam dan para Komandan/Kepala Seksi untuk melihat radar kesiapan Binsat, statistik siswa, dan kinerja kepuasan publik secara seketika (real-time).</li>
  </ol>
  <p>
    Dokumen ini menghimpun seluruh perencanaan sistem secara utuh: Dokumen Penelitian Ilmiah (BAB I–V), Blueprint dan Arsitektur Sistem, Kerangka Kerja Teknis, serta Timeline Rencana Kerja 2 Bulan (Scrum 5 Sprint) dari Kick-off 05 Oktober 2026 hingga Go-Live 27 November 2026.
  </p>
</div>

<!-- KONTEN GABUNGAN EMPAT DOKUMEN -->
{all_sections_html}

</body>
</html>
"""

with open(OUTPUT_HTML, 'w', encoding='utf-8') as f:
    f.write(full_html)

print(f"HTML generated successfully at: {OUTPUT_HTML} (Length: {len(full_html)} chars)")
