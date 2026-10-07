@extends('layouts.app')

@section('title', 'Manajemen Akun Pengguna & Otoritas Satdik — SIPANDU')

@section('content')

<!-- HEADER BANNER -->
<div class="header-banner">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <span class="pdp-badge">
                <span class="ms" style="font-size:16px;">manage_accounts</span> MANAJERIAL AKUN PENGGUNA
            </span>
            <span class="pdp-badge" style="background:rgba(255,255,255,0.15); color:#fff; border-color:rgba(255,255,255,0.25);">
                <span class="ms" style="font-size:16px;">shield</span> ROLE-BASED ACCESS CONTROL (RBAC)
            </span>
        </div>
        <h1>Manajemen Akun & Otoritas Satuan Pendidikan</h1>
        <p>
            Konfigurasi akun personel militer, penetapan peran (Komandan, Operator Satdik, Tim ZI), serta penguncian hak akses input dan pengolahan data serdik per Satuan Pendidikan jajaran Rindam III/Siliwangi.
        </p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button type="button" class="btn btn-gold" onclick="openCreateUserModal()">
            <span class="ms">person_add</span> Tambah Akun Baru
        </button>
    </div>
</div>

<!-- STATS CARDS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #1E293B, #0F172A);">
            <span class="ms">group</span>
        </div>
        <div>
            <div class="stat-val" style="color:#0F172A;">{{ $stats['total'] }}</div>
            <div class="stat-lbl">Total Akun Terdaftar</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                Pengguna Sistem SIPANDU
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--gold), #8A680C);">
            <span class="ms">visibility</span>
        </div>
        <div>
            <div class="stat-val" style="color:var(--gold);">{{ $stats['pimpinan'] }}</div>
            <div class="stat-lbl">Danrindam (View Only)</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                Monitoring Seluruh 5 Satdik
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, #0284C7, #0369A1);">
            <span class="ms">edit_document</span>
        </div>
        <div>
            <div class="stat-val" style="color:#0284C7;">{{ $stats['operator_danrindam'] ?? 1 }}</div>
            <div class="stat-lbl">Operator Danrindam</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                Akses Penuh Seluruh 5 Satdik
            </small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg, var(--green), #166534);">
            <span class="ms">support_agent</span>
        </div>
        <div>
            <div class="stat-val" style="color:var(--green);">{{ $stats['operator'] }}</div>
            <div class="stat-lbl">Operator Satdik Terkunci</div>
            <small style="color:var(--muted); font-size:11px; display:block; margin-top:2px;">
                Secaba, Secata, Dodikjur, dll.
            </small>
        </div>
    </div>
</div>

<!-- FILTER & SEARCH BAR -->
<div class="filter-card" style="background:#fff; border:1px solid var(--line); border-radius:12px; padding:16px 20px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
    <form method="GET" action="{{ route('users.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
        
        <div style="display:flex; gap:12px; flex-wrap:wrap; flex:1;">
            <!-- Filter Satdik -->
            <div style="min-width:200px;">
                <select name="satdik_id" class="form-control" onchange="this.form.submit()" style="font-size:13px;">
                    <option value="">-- Semua Satuan Pendidikan --</option>
                    @foreach($satdiks as $s)
                        <option value="{{ $s->id }}" {{ $selectedSatdikId == $s->id ? 'selected' : '' }}>
                            {{ $s->code }} — {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Role -->
            <div style="min-width:180px;">
                <select name="role" class="form-control" onchange="this.form.submit()" style="font-size:13px;">
                    <option value="">-- Semua Peran / Role --</option>
                    <option value="super_admin" {{ $selectedRole === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="pimpinan" {{ $selectedRole === 'pimpinan' ? 'selected' : '' }}>Danrindam (View Only)</option>
                    <option value="operator_danrindam" {{ $selectedRole === 'operator_danrindam' ? 'selected' : '' }}>Operator Danrindam (Pusat)</option>
                    <option value="operator_satdik" {{ $selectedRole === 'operator_satdik' ? 'selected' : '' }}>Operator Satdik</option>
                    <option value="tim_zi" {{ $selectedRole === 'tim_zi' ? 'selected' : '' }}>Tim ZI / Inspektorat</option>
                </select>
            </div>

            <!-- Search -->
            <div style="flex:1; min-width:240px; position:relative;">
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari nama, email, nomor HP..." class="form-control" style="padding-left:36px; font-size:13px;">
                <span class="ms" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:18px;">search</span>
            </div>
        </div>

        <div style="display:flex; gap:8px;">
            <button type="submit" class="btn btn-outline" style="font-size:13px; padding:8px 14px;">
                <span class="ms">filter_alt</span> Saring
            </button>
            @if($selectedSatdikId || $selectedRole || $keyword)
                <a href="{{ route('users.index') }}" class="btn btn-outline" style="font-size:13px; padding:8px 14px; color:var(--red);">
                    <span class="ms">close</span> Reset
                </a>
            @endif
        </div>

    </form>
</div>

<!-- USER DATA TABLE -->
<div style="background:#fff; border:1px solid var(--line); border-radius:14px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13.5px; text-align:left;">
            <thead>
                <tr style="background:#F8FAFC; border-bottom:1px solid var(--line); color:#475569; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.04em;">
                    <th style="padding:14px 18px; width:45px;">No</th>
                    <th style="padding:14px 18px;">Nama Personel / Akun</th>
                    <th style="padding:14px 18px;">Email Dinas</th>
                    <th style="padding:14px 18px;">Peran (Role)</th>
                    <th style="padding:14px 18px;">Otoritas Satdik</th>
                    <th style="padding:14px 18px;">Kontak HP</th>
                    <th style="padding:14px 18px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $u)
                    <tr style="border-bottom:1px solid #F1F5F9; transition:background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                        <td style="padding:14px 18px; color:var(--muted); font-weight:600;">
                            {{ $users->firstItem() + $index }}
                        </td>
                        <td style="padding:14px 18px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="width:38px; height:38px; border-radius:10px; background:{{ $u->isSuperAdmin() ? '#0F172A' : ($u->isDanrindam() ? '#FEF3C7' : ($u->isOperatorDanrindam() ? '#E0F2FE' : ($u->isOperatorSatdik() ? '#DCFCE7' : '#E2E8F0'))) }}; color:{{ $u->isSuperAdmin() ? '#F8FAFC' : ($u->isDanrindam() ? '#92400E' : ($u->isOperatorDanrindam() ? '#0369A1' : ($u->isOperatorSatdik() ? '#166534' : '#334155'))) }}; display:grid; place-items:center; font-weight:800; font-size:15px; flex-shrink:0;">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-weight:700; color:#0F172A;">
                                        {{ $u->name }}
                                        @if($u->id === auth()->id())
                                            <span style="font-size:10px; background:#DBEAFE; color:#1E40AF; padding:2px 6px; border-radius:4px; margin-left:4px; font-weight:700;">(Akun Anda)</span>
                                        @endif
                                    </div>
                                    <div style="font-size:11.5px; color:#64748B;">Terdaftar: {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:14px 18px; font-family:'Fira Code', monospace; font-size:12.5px; color:#334155;">
                            {{ $u->email }}
                        </td>
                        <td style="padding:14px 18px;">
                            @if($u->isSuperAdmin())
                                <span class="badge" style="background:#0F172A; color:#F8FAFC; border:1px solid #334155; padding:4px 10px; font-size:11.5px; font-weight:700;">
                                    <span class="ms" style="font-size:14px;">admin_panel_settings</span> Super Admin
                                </span>
                            @elseif($u->isDanrindam())
                                <span class="badge" style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A; padding:4px 10px; font-size:11.5px; font-weight:700;">
                                    <span class="ms" style="font-size:14px;">visibility</span> Danrindam (View Only)
                                </span>
                            @elseif($u->isOperatorDanrindam())
                                <span class="badge" style="background:#E0F2FE; color:#0369A1; border:1px solid #BAE6FD; padding:4px 10px; font-size:11.5px; font-weight:700;">
                                    <span class="ms" style="font-size:14px;">edit_document</span> Operator Danrindam
                                </span>
                            @elseif($u->isOperatorSatdik())
                                <span class="badge" style="background:#DCFCE7; color:#166534; border:1px solid #BBF7D0; padding:4px 10px; font-size:11.5px; font-weight:700;">
                                    <span class="ms" style="font-size:14px;">support_agent</span> Operator Satdik
                                </span>
                            @elseif($u->isTimZi())
                                <span class="badge" style="background:#F1F5F9; color:#334155; border:1px solid #CBD5E1; padding:4px 10px; font-size:11.5px; font-weight:700;">
                                    <span class="ms" style="font-size:14px;">policy</span> Tim ZI Area 5
                                </span>
                            @else
                                <span class="badge" style="background:#E0E7FF; color:#3730A3; padding:4px 10px; font-size:11.5px; font-weight:700;">
                                    {{ $u->role_label }}
                                </span>
                            @endif
                        </td>
                        <td style="padding:14px 18px;">
                            @if($u->satdik)
                                <div style="display:inline-flex; align-items:center; gap:6px; background:#EFF6FF; border:1px solid #BFDBFE; color:#1E40AF; padding:4px 10px; border-radius:8px; font-weight:700; font-size:12px;">
                                    <span class="ms" style="font-size:15px;">lock</span>
                                    <span>{{ $u->satdik->code }}</span>
                                </div>
                                <div style="font-size:11px; color:#64748B; margin-top:2px;">{{ $u->satdik->name }}</div>
                            @else
                                <div style="display:inline-flex; align-items:center; gap:6px; background:#FEF3C7; border:1px solid #FDE68A; color:#854D0E; padding:4px 10px; border-radius:8px; font-weight:700; font-size:12px;">
                                    <span class="ms" style="font-size:15px;">public</span>
                                    <span>Seluruh Satdik (Pusat)</span>
                                </div>
                            @endif
                        </td>
                        <td style="padding:14px 18px; color:#475569; font-size:12.5px;">
                            {{ $u->phone ?: '-' }}
                        </td>
                        <td style="padding:14px 18px; text-align:right; white-space:nowrap;">
                            @php
                                $userData = [
                                    'id' => $u->id,
                                    'name' => $u->name,
                                    'email' => $u->email,
                                    'phone' => $u->phone,
                                    'role_code' => $u->role_code,
                                    'satdik_id' => $u->satdik_id,
                                ];
                            @endphp
                            <button type="button" class="btn btn-outline btn-sm" onclick="openEditUserModal(this)" data-user="{{ json_encode($userData) }}" style="color:#2563EB; border-color:#93C5FD; padding:6px 10px; font-size:12.5px;">
                                <span class="ms">edit</span> Edit
                            </button>
                            
                            @if($u->id !== auth()->id())
                                <button type="button" class="btn btn-outline btn-sm" onclick="openDeleteUserModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}')" style="color:#DC2626; border-color:#FCA5A5; padding:6px 10px; font-size:12.5px;">
                                    <span class="ms">delete</span>
                                </button>
                            @else
                                <button type="button" class="btn btn-outline btn-sm" disabled title="Tidak dapat menghapus akun Anda sendiri" style="opacity:0.4; padding:6px 10px; font-size:12.5px;">
                                    <span class="ms">delete</span>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:50px 20px; color:var(--muted);">
                            <span class="ms" style="font-size:48px; color:#CBD5E1; display:block; margin-bottom:8px;">manage_accounts</span>
                            <b>Tidak ada akun pengguna yang sesuai dengan filter.</b>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div style="padding:16px 20px; background:#F8FAFC; border-top:1px solid var(--line); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-size:13px; color:#64748B;">
            Menampilkan <b>{{ $users->firstItem() ?? 0 }}</b> - <b>{{ $users->lastItem() ?? 0 }}</b> dari <b>{{ $users->total() }}</b> akun pengguna
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 1. MODAL TAMBAH PENGGUNA BARU (CREATE) -->
<!-- ========================================== -->
<div class="modal-overlay" id="createUserModal">
    <div class="modal-card" style="max-width:540px;">
        <div class="modal-header" style="background:linear-gradient(135deg, #0F172A, #1E293B);">
            <div style="display:flex; align-items:center; gap:10px;">
                <span class="ms" style="color:var(--gold2); font-size:24px;">person_add</span>
                <div>
                    <b style="font-family:'Montserrat',sans-serif; font-size:16px; color:#fff;">Tambah Akun Pengguna Baru</b>
                    <div style="font-size:11.5px; color:#E2E8F0; opacity:0.85;">Pemberian Hak Akses & Penetapan Satdik</div>
                </div>
            </div>
            <button type="button" onclick="closeCreateUserModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:24px; line-height:1;">&times;</button>
        </div>

        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="modal-body" style="padding:22px 26px;">
                
                <!-- Nama Lengkap & Pangkat -->
                <div class="form-group">
                    <label class="form-label">Nama Lengkap & Pangkat <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Kapten Inf Bambang Wijaya" required>
                    <div class="form-hint">Sertakan pangkat militer bila personel aktif.</div>
                </div>

                <!-- Email Dinas -->
                <div class="form-group">
                    <label class="form-label">Email Dinas (Username Login) <span style="color:var(--red);">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="nama.satdik@rindam.mil.id" required>
                </div>

                <!-- Kata Sandi -->
                <div class="form-group">
                    <label class="form-label">Kata Sandi Awal <span style="color:var(--red);">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                </div>

                <!-- Role / Peran -->
                <div class="form-group">
                    <label class="form-label">Peran dalam Sistem (Role) <span style="color:var(--red);">*</span></label>
                    <select name="role_code" id="create_role_code" class="form-control" required onchange="handleRoleChange('create', this.value)">
                        <option value="operator_satdik" selected>Operator Satuan Pendidikan (Terkunci per Satdik)</option>
                        <option value="operator_danrindam">Operator Danrindam (Akses Penuh Seluruh 5 Satdik)</option>
                        <option value="pimpinan">Danrindam (Pimpinan Satuan - View Only 5 Satdik)</option>
                        <option value="super_admin">Super Administrator (Akses Penuh Pengaturan & Sistem)</option>
                        <option value="tim_zi">Tim Pengawasan Integritas / ZI Area 5</option>
                        <option value="poliklinik">Petugas Medis / Poliklinik</option>
                    </select>
                </div>

                <!-- Satdik Selector (Required if operator) -->
                <div class="form-group" id="create_satdik_group">
                    <label class="form-label">
                        <span>Satuan Pendidikan (Satdik) Terkunci</span>
                        <span style="color:var(--red);">*</span>
                    </label>
                    <select name="satdik_id" id="create_satdik_id" class="form-control" required>
                        <option value="">-- Pilih Satdik yang Dikelola --</option>
                        @foreach($satdiks as $s)
                            <option value="{{ $s->id }}">{{ $s->code }} — {{ $s->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-hint" style="color:var(--green);">
                        🔒 Akun ini hanya dapat menginput dan mengelola data serdik pada Satdik terpilih.
                    </div>
                </div>

                <!-- Nomor Telepon / HP -->
                <div class="form-group">
                    <label class="form-label">Nomor Kontak / WhatsApp (Opsional)</label>
                    <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                </div>

            </div>

            <div class="modal-footer" style="padding:14px 24px; background:#F8FAFC; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeCreateUserModal()">Batal</button>
                <button type="submit" class="btn btn-gold">
                    <span class="ms">save</span> Simpan Akun Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. MODAL EDIT PENGGUNA (UPDATE) -->
<!-- ========================================== -->
<div class="modal-overlay" id="editUserModal">
    <div class="modal-card" style="max-width:540px;">
        <div class="modal-header" style="background:linear-gradient(135deg, #1E3A8A, #1E40AF);">
            <div style="display:flex; align-items:center; gap:10px;">
                <span class="ms" style="color:#93C5FD; font-size:24px;">edit</span>
                <div>
                    <b style="font-family:'Montserrat',sans-serif; font-size:16px; color:#fff;">Edit Akun Pengguna</b>
                    <div id="editUserSub" style="font-size:11.5px; color:#DBEAFE;">Perbarui wewenang & data personel</div>
                </div>
            </div>
            <button type="button" onclick="closeEditUserModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:24px; line-height:1;">&times;</button>
        </div>

        <form id="editUserForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="modal-body" style="padding:22px 26px;">
                
                <!-- Nama Lengkap -->
                <div class="form-group">
                    <label class="form-label">Nama Lengkap & Pangkat <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>

                <!-- Email Dinas -->
                <div class="form-group">
                    <label class="form-label">Email Dinas (Username Login) <span style="color:var(--red);">*</span></label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>

                <!-- Ubah Kata Sandi (Opsional) -->
                <div class="form-group">
                    <label class="form-label">Kata Sandi Baru (Kosongkan bila tidak ingin diubah)</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" minlength="6">
                </div>

                <!-- Role / Peran -->
                <div class="form-group">
                    <label class="form-label">Peran dalam Sistem (Role) <span style="color:var(--red);">*</span></label>
                    <select name="role_code" id="edit_role_code" class="form-control" required onchange="handleRoleChange('edit', this.value)">
                        <option value="operator_satdik">Operator Satuan Pendidikan (Terkunci per Satdik)</option>
                        <option value="operator_danrindam">Operator Danrindam (Akses Penuh Seluruh 5 Satdik)</option>
                        <option value="pimpinan">Danrindam (Pimpinan Satuan - View Only 5 Satdik)</option>
                        <option value="super_admin">Super Administrator (Akses Penuh Pengaturan & Sistem)</option>
                        <option value="tim_zi">Tim Pengawasan Integritas / ZI Area 5</option>
                        <option value="poliklinik">Petugas Medis / Poliklinik</option>
                    </select>
                </div>

                <!-- Satdik Selector -->
                <div class="form-group" id="edit_satdik_group">
                    <label class="form-label">
                        <span>Satuan Pendidikan (Satdik) Terkunci</span>
                        <span style="color:var(--red);">*</span>
                    </label>
                    <select name="satdik_id" id="edit_satdik_id" class="form-control">
                        <option value="">-- Pilih Satdik yang Dikelola --</option>
                        @foreach($satdiks as $s)
                            <option value="{{ $s->id }}">{{ $s->code }} — {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Nomor HP -->
                <div class="form-group">
                    <label class="form-label">Nomor Kontak / WhatsApp</label>
                    <input type="text" name="phone" id="edit_phone" class="form-control">
                </div>

            </div>

            <div class="modal-footer" style="padding:14px 24px; background:#F8FAFC; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeEditUserModal()">Batal</button>
                <button type="submit" class="btn btn-gold">
                    <span class="ms">save</span> Perbarui Akun
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. MODAL HAPUS PENGGUNA (DELETE) -->
<!-- ========================================== -->
<div class="modal-overlay" id="deleteUserModal">
    <div class="modal-card" style="max-width:460px;">
        <div class="modal-header" style="background:#DC2626;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="ms" style="color:#fff; font-size:22px;">warning</span>
                <b style="font-family:'Montserrat',sans-serif; font-size:15px; color:#fff;">Konfirmasi Hapus Akun</b>
            </div>
            <button type="button" onclick="closeDeleteUserModal()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:20px;">&times;</button>
        </div>

        <form id="deleteUserForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="modal-body" style="padding:22px;">
                <p style="font-size:13.5px; color:#334155; line-height:1.5; margin-bottom:14px;">
                    Apakah Anda yakin ingin menghapus akun pengguna militer berikut dari sistem SIPANDU?
                </p>

                <div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:10px; padding:12px 16px;">
                    <div id="deleteUserName" style="font-weight:800; font-size:15px; color:#991B1B;">-</div>
                    <div id="deleteUserEmail" style="font-family:'Fira Code', monospace; font-size:12px; color:#7F1D1D; margin-top:3px;">-</div>
                </div>

                <p style="font-size:11.5px; color:#64748B; margin-top:14px;">
                    Tindakan ini permanen. Pengguna tidak akan dapat masuk kembali ke sistem.
                </p>
            </div>

            <div style="padding:14px 20px; background:#FAFBF9; border-top:1px solid var(--line); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeDeleteUserModal()">Batal</button>
                <button type="submit" class="btn" style="background:#DC2626; color:#fff;">
                    <span class="ms">delete</span> Hapus Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function handleRoleChange(prefix, role) {
        const satdikGroup = document.getElementById(`${prefix}_satdik_group`);
        const satdikSelect = document.getElementById(`${prefix}_satdik_id`);
        
        if (role === 'operator_satdik') {
            satdikGroup.style.display = 'block';
            satdikSelect.required = true;
        } else {
            satdikGroup.style.display = 'none';
            satdikSelect.required = false;
            satdikSelect.value = '';
        }
    }

    function openCreateUserModal() {
        document.getElementById('create_role_code').value = 'operator_satdik';
        handleRoleChange('create', 'operator_satdik');
        document.getElementById('createUserModal').classList.add('active');
    }

    function closeCreateUserModal() {
        document.getElementById('createUserModal').classList.remove('active');
    }

    function openEditUserModal(btnEl) {
        const u = JSON.parse(btnEl.getAttribute('data-user'));
        const form = document.getElementById('editUserForm');
        form.action = `/users/${u.id}`;

        document.getElementById('edit_name').value = u.name;
        document.getElementById('edit_email').value = u.email;
        document.getElementById('edit_phone').value = u.phone || '';
        document.getElementById('edit_role_code').value = u.role_code;
        document.getElementById('edit_satdik_id').value = u.satdik_id || '';
        document.getElementById('editUserSub').textContent = `ID: #${u.id} — ${u.name}`;

        handleRoleChange('edit', u.role_code);
        document.getElementById('editUserModal').classList.add('active');
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.remove('active');
    }

    function openDeleteUserModal(id, name, email) {
        document.getElementById('deleteUserForm').action = `/users/${id}`;
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('deleteUserEmail').textContent = email;
        document.getElementById('deleteUserModal').classList.add('active');
    }

    function closeDeleteUserModal() {
        document.getElementById('deleteUserModal').classList.remove('active');
    }
</script>
@endsection
