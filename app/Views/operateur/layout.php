<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard Opérateur - E-Money' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #66BB6A 0%, #4CAF50 100%);
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-brand {
            color: white;
            font-size: 20px;
            font-weight: 700;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            border-radius: 8px;
            margin: 0 10px;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.25);
            transform: translateX(5px);
        }
        .sidebar-menu i {
            font-size: 18px;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px 10px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            border-radius: 8px;
        }
        .sidebar-footer a:hover {
            background: rgba(255,255,255,0.25);
            transform: translateX(5px);
        }
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px 40px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-gear"></i>
            Administration
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?= base_url('operateur') ?>" class="<?= uri_string() === 'operateur' ? 'active' : '' ?>">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="<?= base_url('operateur/prefixes') ?>" class="<?= uri_string() === 'operateur/prefixes' ? 'active' : '' ?>">
                    <i class="bi bi-phone"></i>
                    Préfixes
                </a>
            </li>
            <li>
                <a href="<?= base_url('operateur/gains') ?>" class="<?= uri_string() === 'operateur/gains' ? 'active' : '' ?>">
                    <i class="bi bi-cash-coin"></i>
                    Gains
                </a>
            </li>
            <li>
                <a href="<?= base_url('operateur/comptes') ?>" class="<?= uri_string() === 'operateur/comptes' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    Comptes
                </a>
            </li>
            <li>
                <a href="<?= base_url('operateur/frais') ?>" class="<?= uri_string() === 'operateur/frais' ? 'active' : '' ?>">
                    <i class="bi bi-cash-stack"></i>
                    Frais
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="<?= base_url('client/login') ?>" class="sidebar-menu" style="display: block; margin-bottom: 10px;">
                <i class="bi bi-person"></i>
                Espace Client
            </a>
            <a href="<?= base_url('operateur/logout') ?>" class="sidebar-menu" style="display: block;">
                <i class="bi bi-box-arrow-right"></i>
                Déconnexion
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</div>

</body>
</html>
