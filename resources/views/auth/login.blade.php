<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — SIPANDU-WBK Rindam III/Siliwangi</title>

    <!-- PWA / Android Mobile Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0F172A">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIPANDU">
    <link rel="icon" type="image/png" sizes="192x192" href="/img/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/img/icons/icon-192x192.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@700;800;900&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,1,0" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
            background-image: 
                radial-gradient(at 0% 0%, rgba(201, 162, 39, 0.06) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 41, 59, 0.06) 0px, transparent 50%);
            color: #0F172A;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .ms {
            font-family: 'Material Symbols Rounded';
            font-weight: normal; font-style: normal;
            line-height: 1; vertical-align: middle;
            display: inline-block;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }

        /* HEADER BRANDING */
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-logo-wrap {
            width: 56px;
            height: 56px;
            margin: 0 auto 14px;
            background: #0F172A;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.15);
        }
        .brand-logo-wrap img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }
        .brand-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: #0F172A;
            line-height: 1.1;
        }
        .brand-title span {
            color: #B45309;
        }
        .brand-sub {
            font-size: 12px;
            font-weight: 600;
            color: #64748B;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* ALERT MESSAGES */
        .alert {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.4;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        /* FORM ELEMENTS */
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 6px;
        }
        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            left: 13px;
            color: #94A3B8;
            font-size: 19px;
            pointer-events: none;
        }
        .form-input {
            width: 100%;
            padding: 11px 14px 11px 40px;
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
            border-color: #0F172A;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
        }
        .toggle-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
        }
        .toggle-btn:hover {
            color: #0F172A;
        }

        /* REMEMBER & SUBMIT */
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            font-size: 13px;
        }
        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }
        .remember-wrap input {
            accent-color: #0F172A;
            width: 15px;
            height: 15px;
            cursor: pointer;
        }
        .lan-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            color: #15803D;
            background: #DCFCE7;
            padding: 2px 8px;
            border-radius: 99px;
        }
        .lan-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #16A34A;
        }

        .btn-submit {
            width: 100%;
            padding: 12px 16px;
            background: #0F172A;
            color: #FFFFFF;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-submit:hover {
            background: #1E293B;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        /* MINIMALIST DEMO ROLE SELECTOR */
        .demo-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0 16px;
            color: #94A3B8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .demo-divider::before, .demo-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E2E8F0;
        }

        .demo-selector-wrap {
            position: relative;
        }
        .demo-select {
            width: 100%;
            padding: 10px 14px 10px 36px;
            font-size: 13px;
            font-family: inherit;
            font-weight: 600;
            color: #334155;
            background: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            cursor: pointer;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2364748B'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 18px;
            transition: all 0.15s ease;
        }
        .demo-select:hover {
            border-color: #94A3B8;
            background-color: #F1F5F9;
        }
        .demo-select:focus {
            border-color: #0F172A;
            background-color: #FFFFFF;
        }
        .demo-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #B45309;
            font-size: 17px;
            pointer-events: none;
        }

        /* FOOTER */
        .card-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 11px;
            color: #94A3B8;
        }
    </style>
</head>
<body>

<div class="login-card">
    
    <!-- BRANDING -->
    <div class="brand-header">
        <div class="brand-logo-wrap">
            <img src="{{ asset('img/rindam-logo.png') }}" alt="Logo Rindam" onerror="this.outerHTML='<span class=\'ms text-white\' style=\'font-size:28px;\'>military_tech</span>'">
        </div>
        <h1 class="brand-title">SIPANDU<span>-WBK</span></h1>
        <p class="brand-sub">Rindam III/Siliwangi</p>
    </div>

    <!-- NOTIFIKASI -->
    @if(session('success'))
        <div class="alert alert-success">
            <span class="ms text-lg">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <span class="ms text-lg">error</span>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- LOGIN FORM -->
    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- EMAIL -->
        <div class="form-group">
            <label class="form-label" for="email">Email Dinas / NRP</label>
            <div class="input-wrap">
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

        <!-- PASSWORD -->
        <div class="form-group">
            <label class="form-label" for="password">Kata Sandi</label>
            <div class="input-wrap">
                <span class="ms input-icon">lock</span>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="form-input" 
                       placeholder="••••••••" 
                       value="password"
                       required>
                <button type="button" class="toggle-btn" onclick="togglePasswordVisibility()" title="Lihat kata sandi">
                    <span class="ms" id="togglePasswordIcon">visibility</span>
                </button>
            </div>
        </div>

        <!-- OPTIONS -->
        <div class="form-row">
            <label class="remember-wrap">
                <input type="checkbox" name="remember" id="remember" checked>
                <span>Ingat sesi saya</span>
            </label>
            <div class="lan-badge" title="Jaringan Lokal Terhubung">
                <span class="lan-dot"></span>
                <span>LAN AKTIF</span>
            </div>
        </div>

        <!-- SUBMIT -->
        <button type="submit" class="btn-submit" id="btnSubmit">
            <span class="ms text-lg">login</span>
            <span>Masuk ke Sistem</span>
        </button>
    </form>

    <!-- MINIMALIST DEMO ACCOUNTS SELECTOR -->
    <div class="demo-divider">Akun Demo Simulasi</div>

    <div class="demo-selector-wrap">
        <span class="ms demo-icon">bolt</span>
        <select id="demoRoleSelector" onchange="onSelectDemoAccount(this)" class="demo-select">
            <option value="" disabled>-- Pilih Akun Uji Coba --</option>
            @foreach($demoAccounts as $acc)
                <option value="{{ $acc['email'] }}|{{ $acc['password'] }}" {{ $acc['email'] === 'danrindam@rindam.mil.id' ? 'selected' : '' }}>
                    {{ $acc['role'] }} — {{ $acc['badge'] }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- FOOTER -->
    <div class="card-footer">
        &copy; {{ date('Y') }} Rindam III/Siliwangi &bull; Wilayah Bebas dari Korupsi
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

    function onSelectDemoAccount(selectEl) {
        if (!selectEl.value) return;
        const [email, pwd] = selectEl.value.split('|');
        const emailInput = document.getElementById('email');
        const pwdInput = document.getElementById('password');
        
        emailInput.value = email;
        pwdInput.value = pwd;

        // Efek visual lembut untuk feedback input terisi
        emailInput.style.backgroundColor = '#EFF6FF';
        pwdInput.style.backgroundColor = '#EFF6FF';
        setTimeout(() => {
            emailInput.style.backgroundColor = '';
            pwdInput.style.backgroundColor = '';
        }, 300);
    }
</script>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then((reg) => console.log('SIPANDU PWA Service Worker aktif:', reg.scope))
                .catch((err) => console.error('SIPANDU PWA gagal:', err));
        });
    }
</script>

</body>
</html>
