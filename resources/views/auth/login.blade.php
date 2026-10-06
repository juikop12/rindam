<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem — SIPANDU-WBK Rindam III/Siliwangi</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,1,0" rel="stylesheet">

    <style>
        :root {
            --o950: #0A1107;
            --o900: #10170C;
            --o800: #1D2A16;
            --o700: #2F4222;
            --o600: #3D5229;
            --o500: #556E3B;
            --o200: #C8D6B9;
            --o100: #E4ECE0;
            --o50: #F4F7F2;

            --gold: #C9A227;
            --gold2: #E8C862;
            --gold-bg: #FBF6E5;

            --bg: #0F172A;
            --card-bg: #FFFFFF;
            --text: #0F172A;
            --muted: #64748B;
            --line: #E2E8F0;

            --green: #15803D;
            --red: #DC2626;
            --blue: #2563EB;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0B140B 0%, #152210 50%, #0D160E 100%);
            color: #1E293B;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }

        /* Tactical Background Grid & Glows */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(201, 162, 39, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(46, 125, 50, 0.15) 0%, transparent 50%),
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 32px 32px, 32px 32px;
            pointer-events: none;
        }

        .ms {
            font-family: 'Material Symbols Rounded';
            font-weight: normal; font-style: normal;
            line-height: 1; vertical-align: middle;
            display: inline-block;
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1060px;
            display: grid;
            grid-template-columns: 1fr 1.15fr;
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        /* LEFT SIDE: BRANDING & ROLE SCOPING INFO */
        .login-hero {
            background: linear-gradient(160deg, #10170C 0%, #1D2A16 60%, #2A3D1E 100%);
            color: #FFFFFF;
            padding: 44px 38px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }
        .login-hero::after {
            content: '';
            position: absolute;
            right: 0; bottom: 0;
            width: 180px; height: 180px;
            background: radial-gradient(circle, rgba(201,162,39,0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-header {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .hero-logo-box {
            width: 58px; height: 58px;
            border-radius: 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            display: grid; place-items: center;
            box-shadow: 0 8px 16px rgba(0,0,0,0.25);
        }
        .hero-logo-box img {
            width: 46px; height: 46px;
            object-fit: contain;
        }
        .hero-title-box h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 21px;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: #FFFFFF;
            line-height: 1.1;
        }
        .hero-title-box h2 span { color: var(--gold2); }
        .hero-title-box p {
            font-size: 11px;
            font-weight: 700;
            color: var(--o200);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 3px;
        }

        .hero-content {
            margin: 36px 0;
        }
        .hero-content h3 {
            font-size: 20px;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            color: #FFFFFF;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        .hero-content p {
            font-size: 13px;
            color: #CBD5E1;
            line-height: 1.6;
        }

        .scope-feature-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 16px;
            margin-top: 22px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .scope-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12.5px;
            color: #E2E8F0;
        }
        .scope-feature-item .ms {
            color: var(--gold2);
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .hero-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 18px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 11.5px;
            color: #94A3B8;
        }
        .hero-footer .status-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(34, 197, 94, 0.15);
            color: #4ADE80;
            border: 1px solid rgba(34, 197, 94, 0.3);
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 11px;
        }
        .status-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #22C55E;
            box-shadow: 0 0 8px #22C55E;
        }

        /* RIGHT SIDE: LOGIN FORM & ROLE SELECTOR */
        .login-form-area {
            padding: 42px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
        }

        .form-header {
            margin-bottom: 26px;
        }
        .form-header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 900;
            color: #0F172A;
            letter-spacing: -0.02em;
        }
        .form-header p {
            font-size: 13.5px;
            color: #64748B;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 7px;
        }
        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            color: #94A3B8;
            font-size: 20px;
            pointer-events: none;
        }
        .form-input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            font-size: 14px;
            font-family: inherit;
            color: #0F172A;
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            outline: none;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            background: #FFFFFF;
            border-color: #1E293B;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.08);
        }
        .input-trailing-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            border-radius: 6px;
        }
        .input-trailing-btn:hover {
            color: #0F172A;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13px;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            cursor: pointer;
            font-weight: 500;
        }
        .checkbox-label input {
            accent-color: #1E293B;
            width: 16px; height: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 13px 20px;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: #FFFFFF;
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);
            transition: all 0.2s ease;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #1E293B 0%, #334155 100%);
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.25);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        /* ALERT NOTIFICATION */
        .alert-box {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.4;
        }
        .alert-error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }
        .alert-success {
            background: #F0FDF4;
            color: #166534;
            border: 1px solid #86EFAC;
        }

        /* QUICK DEMO ACCOUNTS ACCORDION / GRID */
        .demo-accounts-section {
            margin-top: 26px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }
        .demo-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .demo-section-title span {
            font-size: 11.5px;
            font-weight: 800;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .demo-section-title small {
            font-size: 11px;
            color: #94A3B8;
        }

        .demo-chips-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            max-height: 180px;
            overflow-y: auto;
            padding-right: 2px;
        }
        .demo-chip {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 8px 10px;
            cursor: pointer;
            text-align: left;
            transition: all 0.15s ease;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .demo-chip:hover {
            background: #F1F5F9;
            border-color: #CBD5E1;
            transform: translateY(-1px);
        }
        .demo-chip.active {
            border-color: #0F172A;
            background: #EFF6FF;
        }
        .demo-chip-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .demo-chip-role {
            font-size: 11px;
            font-weight: 800;
            color: #0F172A;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .demo-chip-badge {
            font-size: 9.5px;
            font-weight: 700;
            padding: 1.5px 6px;
            border-radius: 4px;
            background: #E2E8F0;
            color: #334155;
            flex-shrink: 0;
        }
        .demo-chip-email {
            font-size: 10px;
            color: #64748B;
            font-family: 'Fira Code', monospace;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 860px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }
            .login-hero {
                padding: 32px 24px;
            }
            .login-form-area {
                padding: 32px 24px;
            }
            .demo-chips-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    
    <!-- LEFT SIDE: STRATEGIC MILITARY OVERVIEW -->
    <div class="login-hero">
        <div>
            <div class="hero-header">
                <div class="hero-logo-box">
                    <img src="{{ asset('img/rindam-logo.png') }}" alt="Logo Rindam" onerror="this.outerHTML='<span class=\'ms\' style=\'font-size:32px; color:#fff;\'>shield_person</span>'">
                </div>
                <div class="hero-title-box">
                    <h2>SIPANDU<span>-WBK</span></h2>
                    <p>Rindam III/Siliwangi</p>
                </div>
            </div>

            <div class="hero-content">
                <h3>Sistem Partisi & Otoritas Berbasis Satdik</h3>
                <p>
                    Aplikasi manajemen data siswa dan rekam kesehatan terpartisi secara ketat antar Satuan Pendidikan jajaran Rindam III/Siliwangi.
                </p>

                <div class="scope-feature-box">
                    <div class="scope-feature-item">
                        <span class="ms">security</span>
                        <div>
                            <b>Peran Operator Satdik:</b> Terkunci otomatis pada satuan pendidikan masing-masing. Hanya dapat menginput, memperbarui, dan mengelola serdik di satdiknya.
                        </div>
                    </div>
                    <div class="scope-feature-item">
                        <span class="ms">military_tech</span>
                        <div>
                            <b>Peran Komandan / Pimpinan:</b> Akses komando terpadu ke seluruh 5 Satdik, statistik makro, dan modul manajemen akun pengguna.
                        </div>
                    </div>
                    <div class="scope-feature-item">
                        <span class="ms">policy</span>
                        <div>
                            <b>Peran Tim Integritas ZI:</b> Pengawasan audit trail enkripsi AES-256 data kependudukan siswa (Area 5 WBK).
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-footer">
            <div class="status-pill">
                <span class="status-dot"></span>
                <span>JARINGAN LAN AKTIF</span>
            </div>
            <span>v2.4.0 — WBK Standar TNI AD</span>
        </div>
    </div>

    <!-- RIGHT SIDE: FORM LOGIN & FAST ROLE PICKER -->
    <div class="login-form-area">
        <div class="form-header">
            <h1>Autentikasi Akun</h1>
            <p>Masukkan kredensial akun militer Anda untuk masuk ke sistem.</p>
        </div>

        @if(session('success'))
            <div class="alert-box alert-success">
                <span class="ms" style="font-size:20px;">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-box alert-error">
                <span class="ms" style="font-size:20px;">error</span>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <!-- EMAIL INPUT -->
            <div class="form-group">
                <label class="form-label" for="email">
                    <span>Email Dinas / NRP</span>
                </label>
                <div class="input-group">
                    <span class="ms input-icon">badge</span>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           class="form-input" 
                           placeholder="nama@rindam.mil.id"
                           value="{{ old('email', 'danrindam@rindam.mil.id') }}" 
                           required 
                           autofocus>
                </div>
            </div>

            <!-- PASSWORD INPUT -->
            <div class="form-group">
                <label class="form-label" for="password">
                    <span>Kata Sandi</span>
                </label>
                <div class="input-group">
                    <span class="ms input-icon">lock</span>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           class="form-input" 
                           placeholder="••••••••" 
                           value="password"
                           required>
                    <button type="button" class="input-trailing-btn" onclick="togglePasswordVisibility()" title="Lihat kata sandi">
                        <span class="ms" id="togglePasswordIcon">visibility</span>
                    </button>
                </div>
            </div>

            <!-- OPTIONS -->
            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" id="remember" checked>
                    <span>Ingat sesi perangkat ini</span>
                </label>
                <span style="font-size:12px; color:#94A3B8;">Zona Integritas WBK</span>
            </div>

            <!-- SUBMIT BUTTON -->
            <button type="submit" class="btn-submit" id="btnSubmit">
                <span class="ms">login</span>
                <span>Masuk ke Sistem Komando</span>
            </button>
        </form>

        <!-- FAST ROLE SIMULATOR / DEMO ACCOUNTS -->
        <div class="demo-accounts-section">
            <div class="demo-section-title">
                <span>Pilih Cepat Akun Simulasi Role</span>
                <small>Klik untuk mengisi form otomatis</small>
            </div>

            <div class="demo-chips-grid">
                @foreach($demoAccounts as $acc)
                    <div class="demo-chip" onclick="fillCredentials('{{ $acc['email'] }}', '{{ $acc['password'] }}', this)" title="{{ $acc['scope'] }}">
                        <div class="demo-chip-header">
                            <span class="demo-chip-role">{{ $acc['role'] }}</span>
                            <span class="demo-chip-badge" style="border-left: 2px solid {{ $acc['color'] }};">{{ $acc['badge'] }}</span>
                        </div>
                        <div class="demo-chip-email">{{ $acc['email'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

<script>
    function togglePasswordVisibility() {
        const pwdInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        if (pwdInput.type === 'password') {
            pwdInput.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            pwdInput.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    function fillCredentials(email, pwd, chipEl) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pwd;
        
        document.querySelectorAll('.demo-chip').forEach(c => c.classList.remove('active'));
        if (chipEl) {
            chipEl.classList.add('active');
        }

        // Animasi halus pada tombol login
        const btn = document.getElementById('btnSubmit');
        btn.style.transform = 'scale(1.02)';
        setTimeout(() => { btn.style.transform = ''; }, 150);
    }
</script>

</body>
</html>
