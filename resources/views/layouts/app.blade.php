<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Laravel App')</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --error-bg: #fee2e2;
            --error-text: #991b1b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header-container, .footer-container, .main-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1.5rem;
            width: 100%;
        }

        .site-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .nav-menu {
            display: flex;
            gap: 1.25rem;
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .nav-menu a:hover {
            color: var(--primary);
            background: #eff6ff;
        }

        .nav-menu a.active {
            color: var(--primary);
            background: #eff6ff;
            font-weight: 600;
        }

        main {
            flex: 1;
            padding: 2.5rem 0;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        h1, h2, h3 {
            color: var(--text);
            margin-bottom: 1rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .alert-success {
            background-color: var(--success-bg);
            color: var(--success-text);
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background-color: var(--error-bg);
            color: var(--error-text);
            border: 1px solid #fecaca;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text);
        }

        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 1rem;
            background: #fff;
            color: var(--text);
            transition: border-color 0.2s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn:hover {
            background: var(--primary-hover);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            text-align: left;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border);
        }

        th {
            background: #f1f5f9;
            font-weight: 600;
            color: var(--text);
        }

        tr:hover {
            background: #f8fafc;
        }

        .site-footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border);
            padding: 1.5rem 0;
            margin-top: auto;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .footer-sub {
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    @include('partials.header')

    <main>
        <div class="main-container">
            @yield('content')
        </div>
    </main>

    @include('partials.footer')
</body>
</html>
