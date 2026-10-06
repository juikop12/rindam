with open(r'd:\laragon\www\rindam\resources\views\programs\index.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Find the start of the 4 STAT CARDS
stat_idx = -1
for i, line in enumerate(lines):
    if '<!-- 4 STAT CARDS -->' in line:
        stat_idx = i
        break

# Find the start and end of the FILTER SATDIK DROPDOWN
filter_start = -1
filter_end = -1
for i, line in enumerate(lines):
    if '<!-- FILTER SATDIK DROPDOWN -->' in line:
        filter_start = i
    if '<!-- MAIN PROGRAM DATA CARD -->' in line:
        filter_end = i
        break

if stat_idx != -1 and filter_start != -1 and filter_end != -1:
    dropdown_lines = lines[filter_start:filter_end]
    
    # Remove from original location
    del lines[filter_start:filter_end]
    
    # Insert before stat cards
    lines = lines[:stat_idx] + dropdown_lines + ['\n'] + lines[stat_idx:]
    
    with open(r'd:\laragon\www\rindam\resources\views\programs\index.blade.php', 'w', encoding='utf-8') as f:
        f.writelines(lines)
    print("Successfully moved dropdown above stat cards.")
else:
    print(f"Failed to find indices: stat_idx={stat_idx}, filter_start={filter_start}, filter_end={filter_end}")
