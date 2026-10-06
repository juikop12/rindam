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

    # Revert the button title container width that caused the arrow to overflow
    content = content.replace('truncate w-[160px] sm:w-[220px]', 'truncate w-[140px] sm:w-[160px]')
    content = content.replace('truncate w-[160px] sm:w-[220px]', 'truncate w-[160px]') # In case dashboard used w-[160px]

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

print("Button title widths reverted.")
