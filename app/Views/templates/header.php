<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'ITSO Equipment Management') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 260px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 0;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 1.5rem 1rem;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header h4 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .nav-section {
            padding: 0.5rem 0;
        }
        .nav-section-title {
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255,255,255,0.6);
            font-weight: 600;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 0.75rem 1rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #60a5fa;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #60a5fa;
            font-weight: 500;
        }
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 0;
        }
        .top-navbar {
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-area {
            padding: 2rem 1.5rem;
        }
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background: white;
            border-bottom: 2px solid #e5e7eb;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }
        .btn-primary {
            background: #1e40af;
            border-color: #1e40af;
        }
        .btn-primary:hover {
            background: #1e3a8a;
            border-color: #1e3a8a;
        }
        .sidebar-toggle {
            display: none;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-toggle {
                display: inline-block;
            }
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: none;
                z-index: 999;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
        .badge-status {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .table thead {
            background: #f3f4f6;
        }
    </style>
</head>
<body>