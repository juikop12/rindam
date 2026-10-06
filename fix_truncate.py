import os

files = [
    r'd:\laragon\www\rindam\resources\views\health\index.blade.php',
    r'd:\laragon\www\rindam\resources\views\programs\index.blade.php',
    r'd:\laragon\www\rindam\resources\views\students\index.blade.php',
    r'd:\laragon\www\rindam\resources\views\dashboard\index.blade.php',
]

for filepath in files:
    if not os.path.exists(filepath):
        continue
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Expand the width of the dropdown menu from 320px to 380px
    content = content.replace('sm:w-[320px]', 'sm:w-[380px]')
    content = content.replace('w-[320px]', 'w-full sm:w-[380px]')

    # 2. Remove truncate and max-w constraints from the subtitle
    content = content.replace('class="text-[11px] text-slate-500 truncate max-w-[150px] sm:max-w-[180px]"', 'class="text-[11px] text-slate-500 whitespace-normal leading-tight"')
    content = content.replace('class="text-[11px] text-slate-500 truncate"', 'class="text-[11px] text-slate-500 whitespace-normal leading-tight"')

    # 3. For the main title, ensure it also doesn't get squished
    content = content.replace('truncate w-[140px] sm:w-[160px]', 'truncate w-[160px] sm:w-[220px]')
    content = content.replace('truncate w-[160px]', 'truncate w-[160px] sm:w-[220px]')

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

print("Dropdown texts and widths fixed successfully.")
