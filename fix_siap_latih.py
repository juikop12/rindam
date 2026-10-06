import os
import re

# File paths to process
files = [
    r'd:\laragon\www\rindam\app\Models\StudentHealthRecord.php',
    r'd:\laragon\www\rindam\resources\views\health\index.blade.php',
    r'd:\laragon\www\rindam\resources\views\health\show.blade.php',
    r'd:\laragon\www\rindam\resources\views\health\edit.blade.php',
    r'd:\laragon\www\rindam\resources\views\students\index.blade.php',
    r'd:\laragon\www\rindam\resources\views\students\show.blade.php',
    r'd:\laragon\www\rindam\resources\views\students\create.blade.php',
    r'd:\laragon\www\rindam\resources\views\dashboard\index.blade.php',
    r'd:\laragon\www\rindam\resources\views\programs\show.blade.php',
]

for filepath in files:
    if not os.path.exists(filepath):
        continue
        
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Generic UI replacements for "Siap Latih" -> "Sehat"
    if 'StudentHealthRecord.php' in filepath:
        content = content.replace("'label' => 'Siap Latih'", "'label' => 'Sehat'")
    elif 'health\\index.blade.php' in filepath:
        content = content.replace("siap latih</span>", "sehat</span>")
        content = content.replace(">Siap Latih</option>", ">Sehat</option>")
        content = content.replace("'label' => 'Siap Latih'", "'label' => 'Sehat'")
        
        # Desktop Table adjustments
        content = content.replace('<div class="overflow-x-auto hidden md:block">', '<div class="hidden xl:block">')
        content = content.replace('table class="w-full text-left text-sm text-slate-600"', 'table class="w-full text-left text-sm text-slate-600 table-fixed"')
        
        # Fix table column headers
        content = content.replace('<th class="px-4 py-3 border-b border-slate-200 w-12 text-center">No</th>', '<th class="px-2 py-3 border-b border-slate-200 w-10 text-center">No</th>')
        content = content.replace('<th class="px-5 py-3 border-b border-slate-200">Siswa / Serdik</th>', '<th class="px-2 py-3 border-b border-slate-200 w-48">Siswa / Serdik</th>')
        content = content.replace('<th class="px-4 py-3 border-b border-slate-200">Satuan & Peleton</th>', '<th class="px-2 py-3 border-b border-slate-200 w-24">Satuan & Ton</th>')
        content = content.replace('<th class="px-4 py-3 border-b border-slate-200">Program Diklat</th>', '<th class="px-2 py-3 border-b border-slate-200 w-28">Program Diklat</th>')
        content = content.replace('<th class="px-4 py-3 border-b border-slate-200">Kodam / Kodim Asal</th>', '<th class="px-2 py-3 border-b border-slate-200 w-24">Asal</th>')
        content = content.replace('<th class="px-4 py-3 border-b border-slate-200">Status Serdik</th>', '<th class="px-2 py-3 border-b border-slate-200 w-20">Status</th>')
        content = content.replace('<th class="px-4 py-3 border-b border-slate-200">Kondisi Fisik / Kesiapan</th>', '<th class="px-2 py-3 border-b border-slate-200 w-32">Kondisi Fisik</th>')
        content = content.replace('<th class="px-4 py-3 border-b border-slate-200">Tanda Vital</th>', '<th class="px-2 py-3 border-b border-slate-200 w-20">Vital</th>')
        content = content.replace('<th class="px-4 py-3 border-b border-slate-200">Catatan Medis & Alergi</th>', '<th class="px-2 py-3 border-b border-slate-200 w-36">Catatan Medis</th>')
        content = content.replace('<th class="px-4 py-3 border-b border-slate-200 text-center">Aksi Rekam Medis</th>', '<th class="px-2 py-3 border-b border-slate-200 w-24 text-center">Aksi</th>')
        
        # Change mobile breakpoint from md to xl for the cards view to ensure it kicks in on smaller desktops/tablets
        content = content.replace('<div class="block md:hidden divide-y divide-slate-100">', '<div class="block xl:hidden divide-y divide-slate-100">')
        
        # Padding fixes on cells
        content = content.replace('px-4 py-3.5', 'px-2 py-2.5')
        content = content.replace('px-5 py-3.5', 'px-2 py-2.5')
        
    elif 'health\\show.blade.php' in filepath:
        content = content.replace("Siap Latih Penuh", "Sehat Penuh")
    elif 'health\\edit.blade.php' in filepath:
        content = content.replace("Siap Latih (Kondisi Prima & Siap Latihan Penuh)", "Sehat (Kondisi Prima & Siap Latihan Penuh)")
    elif 'students\\index.blade.php' in filepath:
        content = content.replace("Siap Latih Penuh (Normal & Prima)", "Sehat Penuh (Normal & Prima)")
        content = content.replace("Siap Latih — Terhitung", "Sehat — Terhitung")
    elif 'students\\show.blade.php' in filepath:
        content = content.replace("Aktif (Siap Latih)", "Aktif (Sehat)")
    elif 'students\\create.blade.php' in filepath:
        content = content.replace(">Siap Latih Penuh<", ">Sehat Penuh<")
    elif 'dashboard\\index.blade.php' in filepath:
        content = content.replace("siap latih</span>", "sehat</span>")
    elif 'programs\\show.blade.php' in filepath:
        content = content.replace("Siap Latih &bull;", "Sehat &bull;")

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

print("Text replaced and table fixed successfully.")
