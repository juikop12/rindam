<?php

$filePath = __DIR__ . '/../resources/views/students/index.blade.php';
$content = file_get_contents($filePath);

// 1. Wrap top action buttons if not already wrapped
if (strpos($content, "@if(auth()->user()?->canModifyData())\n        <div class=\"flex items-center gap-2.5\">\n            <a href=\"{{ route('students.import'") === false) {
    $searchTop = <<<HTML
        <!-- Action Buttons -->
        <div class="flex items-center gap-2.5">
            <a href="{{ route('students.import', ['satdik_id' => \$selectedSatdikId]) }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <span class="ms text-[18px]">upload_file</span> Impor Format Excel
            </a>
            <button type="button" onclick="openCreateStudentModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <span class="ms text-[18px]">person_add</span> Tambah Siswa Baru
            </button>
        </div>
HTML;
    $replaceTop = <<<HTML
        <!-- Action Buttons -->
        @if(auth()->user()?->canModifyData())
        <div class="flex items-center gap-2.5">
            <a href="{{ route('students.import', ['satdik_id' => \$selectedSatdikId]) }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <span class="ms text-[18px]">upload_file</span> Impor Format Excel
            </a>
            <button type="button" onclick="openCreateStudentModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <span class="ms text-[18px]">person_add</span> Tambah Siswa Baru
            </button>
        </div>
        @endif
HTML;
    $content = str_replace($searchTop, $replaceTop, $content);
}

// 2. Wrap table action buttons (desktop)
$searchTable = <<<HTML
                                    <button type="button" class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openStatusModal({{ \$student->id }}, '{{ addslashes(\$student->full_name) }}', '{{ \$student->status }}')" title="Ubah Status">
                                        <span class="ms text-[18px]">swap_horiz</span>
                                    </button>
                                    <button type="button" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openEditStudentModal(this)" data-student="{{ json_encode(\$studentJson) }}" title="Edit Data">
                                        <span class="ms text-[18px]">edit</span>
                                    </button>
                                    <button type="button" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openDeleteStudentModal({{ \$student->id }}, '{{ addslashes(\$student->full_name) }}', '{{ \$student->nosik }}', '{{ addslashes(\$student->satdik->name ?? \'\') }}')" title="Hapus Data">
                                        <span class="ms text-[18px]">delete</span>
                                    </button>
HTML;
$replaceTable = <<<HTML
                                    @if(auth()->user()?->canModifyData())
                                    <button type="button" class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openStatusModal({{ \$student->id }}, '{{ addslashes(\$student->full_name) }}', '{{ \$student->status }}')" title="Ubah Status">
                                        <span class="ms text-[18px]">swap_horiz</span>
                                    </button>
                                    <button type="button" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openEditStudentModal(this)" data-student="{{ json_encode(\$studentJson) }}" title="Edit Data">
                                        <span class="ms text-[18px]">edit</span>
                                    </button>
                                    <button type="button" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openDeleteStudentModal({{ \$student->id }}, '{{ addslashes(\$student->full_name) }}', '{{ \$student->nosik }}', '{{ addslashes(\$student->satdik->name ?? \'\') }}')" title="Hapus Data">
                                        <span class="ms text-[18px]">delete</span>
                                    </button>
                                    @endif
HTML;
$content = str_replace($searchTable, $replaceTable, $content);

// 3. Wrap mobile card action buttons
$searchCard = <<<HTML
                            <button type="button" class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openStatusModal({{ \$student->id }}, '{{ addslashes(\$student->full_name) }}', '{{ \$student->status }}')" title="Ubah Status">
                                <span class="ms text-[18px]">swap_horiz</span>
                            </button>
                            <button type="button" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openEditStudentModal(this)" data-student="{{ json_encode(\$studentJson) }}" title="Edit Data">
                                <span class="ms text-[18px]">edit</span>
                            </button>
                            <button type="button" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openDeleteStudentModal({{ \$student->id }}, '{{ addslashes(\$student->full_name) }}', '{{ \$student->nosik }}', '{{ addslashes(\$student->satdik->name ?? \'\') }}')" title="Hapus Data">
                                <span class="ms text-[18px]">delete</span>
                            </button>
HTML;
$replaceCard = <<<HTML
                            @if(auth()->user()?->canModifyData())
                            <button type="button" class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openStatusModal({{ \$student->id }}, '{{ addslashes(\$student->full_name) }}', '{{ \$student->status }}')" title="Ubah Status">
                                <span class="ms text-[18px]">swap_horiz</span>
                            </button>
                            <button type="button" class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openEditStudentModal(this)" data-student="{{ json_encode(\$studentJson) }}" title="Edit Data">
                                <span class="ms text-[18px]">edit</span>
                            </button>
                            <button type="button" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-slate-100 transition-colors" onclick="openDeleteStudentModal({{ \$student->id }}, '{{ addslashes(\$student->full_name) }}', '{{ \$student->nosik }}', '{{ addslashes(\$student->satdik->name ?? \'\') }}')" title="Hapus Data">
                                <span class="ms text-[18px]">delete</span>
                            </button>
                            @endif
HTML;
$content = str_replace($searchCard, $replaceCard, $content);

// 4. Add @endif after deleteStudentModal
$searchDeleteEnd = <<<HTML
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                    <span class="ms text-[16px]">delete</span> Ya, Hapus Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- DATALISTS FOR AUTOCOMPLETE -->
HTML;
$replaceDeleteEnd = <<<HTML
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                    <span class="ms text-[16px]">delete</span> Ya, Hapus Data
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- DATALISTS FOR AUTOCOMPLETE -->
HTML;
$content = str_replace($searchDeleteEnd, $replaceDeleteEnd, $content);

// 5. Remove the conflict section (from <<<<<<< HEAD to >>>>>>> origin/main)
$headPos = strpos($content, '<<<<<<< HEAD');
$originPos = strpos($content, ">>>>>>> origin/main\r\n");
$offsetLen = strlen(">>>>>>> origin/main\r\n");
if ($originPos === false) {
    $originPos = strpos($content, ">>>>>>> origin/main\n");
    $offsetLen = strlen(">>>>>>> origin/main\n");
}

if ($headPos !== false && $originPos !== false) {
    $before = substr($content, 0, $headPos);
    $after = substr($content, $originPos + $offsetLen);
    $content = $before . $after;
    echo "Removed conflict block successfully.\n";
} else {
    echo "Conflict block markers not found!\n";
}

file_put_contents($filePath, $content);
echo "Done cleaning students/index.blade.php\n";
