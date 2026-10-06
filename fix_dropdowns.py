import os
import re

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

    # Remove max height scrolling container from all dropdown lists to make them fixed
    content = content.replace('<div class="max-h-[320px] overflow-y-auto overscroll-contain">', '<div>')

    if 'dashboard\\index.blade.php' in filepath:
        # Remove mobile dropdown
        mobile_dropdown = re.search(r'<!-- Mobile View \(Dropdown\).*?</div>\s*<!-- Desktop View \(Beautiful Dropdown\) -->', content, flags=re.DOTALL)
        if mobile_dropdown:
            content = content.replace(mobile_dropdown.group(0), '<!-- Beautiful Dropdown (Mobile & Desktop) -->')
        
        # Make beautiful dropdown visible on mobile
        content = content.replace('<div class="hidden md:block relative group z-30" x-data="{ open: false }">', '<div class="relative group z-30" x-data="{ open: false }">')
        content = content.replace('w-[280px] flex items-center justify-between', 'w-full sm:w-[280px] flex items-center justify-between')
        content = content.replace('w-[320px] bg-white rounded-xl shadow-xl', 'w-full sm:w-[320px] bg-white rounded-xl shadow-xl')
        
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

print("Dropdowns updated successfully.")
