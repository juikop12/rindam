<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dokumen Resmi Kedinasan') — Rindam III/Siliwangi</title>
    <link rel="icon" type="image/png" href="{{ asset('img/icons/icon-192x192.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,1,0" rel="stylesheet">
    <style>
        /* =========================================================
           STANDAR TATA NASKAH KEDINASAN MILITER — RINDAM III/SILIWANGI
           ========================================================= */
        @page {
            size: @yield('page_size', 'A4 portrait');
            margin: 12mm 15mm 15mm 15mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.35;
            color: #000000;
            background-color: #E2E8F0;
            min-height: 100vh;
        }

        .screen-container {
            max-width: @yield('container_width', '210mm');
            margin: 20px auto 40px auto;
            background: #FFFFFF;
            padding: 15mm 18mm 20mm 18mm;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            position: relative;
        }

        /* FLOATING ACTION TOOLBAR (HANYA DITAMPILKAN DI LAYAR BROWSER) */
        .no-print-toolbar {
            position: sticky;
            top: 10px;
            z-index: 9999;
            max-width: @yield('container_width', '210mm');
            margin: 10px auto 12px auto;
            background: #0F172A;
            color: #FFFFFF;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 13px;
        }

        .btn-toolbar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12.5px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-print {
            background: #F59E0B;
            color: #0F172A;
        }
        .btn-print:hover {
            background: #D97706;
        }

        .btn-back {
            background: #334155;
            color: #F8FAFC;
        }
        .btn-back:hover {
            background: #475569;
        }

        /* KOP SURAT RESMI MILITER */
        .kop-wrapper {
            margin-bottom: 14px;
            position: relative;
        }

        .kop-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .kop-unit {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.25;
            text-align: center;
            width: fit-content;
            border-bottom: 1.5px solid #000;
            padding-bottom: 2px;
        }

        .kop-classification {
            text-align: right;
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            font-weight: 900;
            color: #C00000;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .kop-divider-double {
            margin-top: 8px;
            margin-bottom: 16px;
            border-top: 2px solid #000000;
            border-bottom: 0.75px solid #000000;
            height: 3px;
        }

        /* JUDUL DOKUMEN */
        .doc-title-box {
            text-align: center;
            margin-bottom: 18px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }

        .doc-number {
            font-size: 10pt;
            font-weight: normal;
            margin-top: 3px;
            font-family: 'Times New Roman', Times, serif;
        }

        /* TABEL MILITER FORMAL */
        table.mil-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 16px;
            page-break-inside: auto;
        }

        table.mil-table th,
        table.mil-table td {
            border: 1px solid #000000;
            padding: 5px 7px;
            vertical-align: top;
        }

        table.mil-table th {
            background-color: #F1F5F9;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9.5pt;
        }

        table.mil-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* TABEL IDENTITAS 2 KOLOM */
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
            margin-bottom: 14px;
        }

        table.info-table td {
            padding: 3px 4px;
            vertical-align: top;
        }

        /* WATERMARK DOKUMEN RESMI */
        .watermark-bg {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 48pt;
            font-weight: 900;
            color: rgba(0, 0, 0, 0.035);
            letter-spacing: 6px;
            text-transform: uppercase;
            pointer-events: none;
            white-space: nowrap;
            z-index: 0;
            font-family: 'Arial', sans-serif;
        }

        /* KOLOM TANDA TANGAN / PENGESAHAN */
        .signature-wrapper {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }

        .signature-grid {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 250px;
            text-align: center;
            font-size: 10.5pt;
        }

        .signature-space {
            height: 65px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-rank {
            font-size: 10pt;
        }

        /* REKAPITULASI KOTAK RINGKASAN MILITER */
        .summary-box-grid {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .summary-box-item {
            flex: 1;
            border: 1px solid #000000;
            padding: 6px 8px;
            text-align: center;
            background-color: #F8FAFC;
        }

        .summary-box-num {
            font-size: 13pt;
            font-weight: bold;
            font-family: 'Arial', sans-serif;
            color: #000000;
        }

        .summary-box-label {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #334155;
            margin-top: 2px;
        }

        /* PAS FOTO SERDIK */
        .student-photo-box {
            border: 1.5px solid #000000;
            width: 105px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 8.5pt;
            background: #F8FAFC;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
        }

        .student-photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* BADGE & UTILITY DOKUMEN */
        .section-header {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 14px 0 6px 0;
            border-bottom: 1px solid #000000;
            padding-bottom: 2px;
        }

        .badge-print {
            display: inline-block;
            padding: 1px 6px;
            border: 1px solid #000000;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            background: #FFFFFF;
        }

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .font-bold { font-weight: bold !important; }
        .font-mono { font-family: 'Courier New', Courier, monospace !important; }

        /* FOOTER KEDINASAN */
        .doc-footer {
            margin-top: 30px;
            padding-top: 8px;
            border-top: 0.5px solid #94A3B8;
            font-family: 'Arial', sans-serif;
            font-size: 8pt;
            color: #64748B;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* MEDIA PRINT SPESIFIKASI CETAK / PDF */
        @media print {
            body {
                background: #FFFFFF !important;
                color: #000000 !important;
                font-size: 10.5pt !important;
            }

            .screen-container {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .no-print,
            .no-print-toolbar {
                display: none !important;
            }

            .page-break {
                page-break-after: always;
            }

            .watermark-bg {
                color: rgba(0, 0, 0, 0.04) !important;
            }
        }
    </style>
</head>
<body>

    <!-- FLOATING TOOLBAR (HANYA DITAMPILKAN DI TAMPILAN LAYAR) -->
    <div class="no-print-toolbar">
        <div style="display:flex; align-items:center; gap:10px;">
            <a href="javascript:window.history.back()" class="btn-toolbar btn-back">
                <span class="material-symbols-rounded" style="font-size:18px;">arrow_back</span>
                Kembali
            </a>
            <div>
                <b>Format Dokumen Resmi Cetak / PDF</b>
                <div style="font-size:11px; opacity:0.8;">Standar Tata Naskah Militer Rindam III/Siliwangi</div>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <button onclick="window.print()" class="btn-toolbar btn-print">
                <span class="material-symbols-rounded" style="font-size:18px;">print</span>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- HALAMAN KERTAS DOKUMEN -->
    <div class="screen-container">
        <div class="watermark-bg">SIPANDU-WBK</div>

        <!-- KOP SURAT RESMI MILITER -->
        <div class="kop-wrapper">
            <div class="kop-header">
                <div class="kop-unit">
                    @yield('kop_unit', 'KOMANDO DAERAH MILITER III/SILIWANGI<br>RESIMEN INDUK')
                </div>
                <div class="kop-classification">
                    @yield('classification', 'TERBATAS')
                </div>
            </div>
            <div class="kop-divider-double"></div>
        </div>

        <!-- ISI KONTEN DOKUMEN -->
        @yield('content')

        <!-- FOOTER DOKUMEN -->
        <div class="doc-footer">
            <div>
                Sistem SIPANDU-WBK Rindam III/Siliwangi &bull; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
            </div>
            <div>
                Otentikasi: User ID #{{ auth()->id() ?? 'SYS' }} ({{ auth()->user()->role_code ?? 'SYSTEM' }})
            </div>
        </div>
    </div>

    <script>
        // Otomatis buka dialog print jika terdapat parameter ?print=1
        if (new URLSearchParams(window.location.search).get('print') === '1') {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 350);
            });
        }
    </script>
</body>
</html>
