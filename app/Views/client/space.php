<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace Client - E-Money</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-content">
        <div class="navbar-brand">
            <i class="bi bi-wallet2"></i>
            E-Money Client
        </div>
        <div class="navbar-user">
            <div class="user-phone">
                <i class="bi bi-phone"></i>
                <?= esc($compte['numero_telephone']) ?>
            </div>
            <a href="<?= base_url('client/logout') ?>" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="space-container">
    <!-- Messages Alertes -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <i class="bi bi-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="cards-grid">
        <!-- BLOC SOLDE ACTUEL -->
        <div class="balance-card">
            <div class="balance-label">
                <i class="bi bi-wallet"></i>
                Votre Solde Actuel
            </div>
            <div class="balance-amount">
                <?= number_format($compte['solde'], 2, ',', ' ') ?> <span class="balance-currency">Ar</span>
            </div>
        </div>

        <!-- BLOC FAIRE UNE OPÉRATION -->
        <div class="operations-card">
            <div class="card-header">
                <i class="bi bi-arrow-left-right"></i>
                Effectuer une opération
            </div>
            <div class="card-body">
                <!-- Formulaires par onglets -->
                <div class="tabs-nav">
                    <button class="tab-btn active" data-tab="depot">
                        <i class="bi bi-download"></i>
                        Dépôt
                    </button>
                    <button class="tab-btn" data-tab="retrait">
                        <i class="bi bi-upload"></i>
                        Retrait
                    </button>
                    <button class="tab-btn" data-tab="transfert">
                        <i class="bi bi-send"></i>
                        Transfert
                    </button>
                </div>

                <div class="tab-content active" id="depot">
                    <form action="<?= base_url('client/transaction') ?>" method="POST">
                        <input type="hidden" name="type" value="depot">
                        <div class="form-group">
                            <label class="form-label">Montant du dépôt (Ar)</label>
                            <input type="number" name="montant" class="form-control" placeholder="Montant" required min="1">
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i>
                            Valider le dépôt (Automatique)
                        </button>
                    </form>
                </div>

                <div class="tab-content" id="retrait">
                    <form action="<?= base_url('client/transaction') ?>" method="POST">
                        <input type="hidden" name="type" value="retrait">
                        <div class="form-group">
                            <label class="form-label">Montant à retirer (Ar)</label>
                            <input type="number" name="montant" class="form-control" placeholder="Montant" required min="100">
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i>
                            Confirmer le Retrait
                        </button>
                    </form>
                </div>

                <div class="tab-content" id="transfert">
                    <form action="<?= base_url('client/transaction') ?>" method="POST">
                        <input type="hidden" name="type" value="transfert">
                        <div class="form-group">
                            <label class="form-label">Numéro du destinataire</label>
                            <input type="tel" name="destinataire" class="form-control" placeholder="Ex: 037XXXXXXX" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Montant à transférer (Ar)</label>
                            <input type="number" name="montant" class="form-control" placeholder="Montant" required min="100">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                            Envoyer l'argent
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- LISTE HISTORIQUE -->
        <div class="history-card" style="grid-column: span 12;">
            <div class="card-header">
                <i class="bi bi-clock-history"></i>
                Historique des transactions
            </div>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date & Heure</th>
                        <th>Type</th>
                        <th>Expéditeur</th>
                        <th>Destinataire</th>
                        <th>Montant (Ar)</th>
                        <th>Frais Appliqués (Ar)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($historique)): ?>
                        <tr><td colspan="6" class="empty-state">Aucune transaction enregistrée.</td></tr>
                    <?php else: ?>
                        <?php foreach($historique as $op): ?>
                            <tr>
                                <td><?= esc($op['date_operation']) ?></td>
                                <td>
                                    <?php 
                                        if($op['id_type_operation'] == 1) echo '<span class="badge badge-depot"><i class="bi bi-download"></i> Dépôt</span>';
                                        elseif($op['id_type_operation'] == 2) echo '<span class="badge badge-retrait"><i class="bi bi-upload"></i> Retrait</span>';
                                        else echo '<span class="badge badge-transfert"><i class="bi bi-send"></i> Transfert</span>';
                                    ?>
                                </td>
                                <td><?= esc($op['numero_expediteur']) ?></td>
                                <td><?= $op['numero_destinataire'] ? esc($op['numero_destinataire']) : '-' ?></td>
                                <td class="fw-bold"><?= number_format($op['montant'], 0, ',', ' ') ?></td>
                                <td><?= $op['frais'] > 0 ? '+ ' . number_format($op['frais'], 0, ',', ' ') : '0' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="/assets/js/space.js"></script>
</body>
</html>