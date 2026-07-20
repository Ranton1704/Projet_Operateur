<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Opérateur - E-Money</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-content">
        <div class="navbar-brand">
            <i class="bi bi-gear"></i>
            Administration Opérateur
        </div>
        <div class="navbar-user">
            <a href="<?= base_url('client/login') ?>" class="btn-logout" style="background: rgba(255,255,255,0.15);">
                <i class="bi bi-person"></i>
                Espace Client
            </a>
            <a href="<?= base_url('operateur/logout') ?>" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="space-container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="cards-grid">
        
        <!-- PRÉFIXES & GAINS -->
        <div class="operations-card" style="grid-column: span 4;">
            <div class="card-header">
                <i class="bi bi-phone"></i>
                Préfixes Valides
            </div>
            <div class="card-body">
                <form action="<?= base_url('operateur/prefixe') ?>" method="POST" class="d-flex gap-2 mb-3">
                    <input type="text" name="prefixe" class="form-control" placeholder="Ex: 034" required maxlength="5">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus"></i>
                        Ajouter
                    </button>
                </form>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach($prefixes as $p): ?>
                        <span class="badge badge-transfert">
                            <i class="bi bi-phone"></i>
                            <?= esc($p['prefixe']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="balance-card" style="grid-column: span 4;">
            <div class="balance-label">
                <i class="bi bi-cash-coin"></i>
                Situation globale des Gains
            </div>
            <div class="balance-amount">
                <?php 
                    $totalGlobal = 0;
                    if(!empty($gains)) { foreach($gains as $g) { $totalGlobal += $g['total_gains']; } }
                ?>
                <?= number_format($totalGlobal, 2, ',', ' ') ?> <span class="balance-currency">Ar</span>
            </div>
            <div style="margin-top: 15px;">
                <?php if(empty($gains)): ?>
                    <span style="opacity: 0.7;">Aucun frais perçu.</span>
                <?php else: ?>
                    <?php foreach($gains as $g): ?>
                        <div style="display: flex; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.3); padding: 8px 0;">
                            <span style="opacity: 0.7;">Frais <?= ucfirst(esc($g['nom'])) ?> :</span>
                            <span style="font-weight: 600;"><?= number_format($g['total_gains'], 0, ',', ' ') ?> Ar</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- SITUATION DES COMPTES (AVEC FILTRE/RECHERCHE) -->
        <div class="history-card" style="grid-column: span 4;">
            <div class="card-header">
                <i class="bi bi-people"></i>
                Situation des Comptes Clients
            </div>
            <div style="padding: 20px;">
                <input type="text" id="search-compte" class="form-control" placeholder="🔍 Rechercher un numéro..." style="margin-bottom: 15px;">
                <div style="max-height: 290px; overflow-y: auto;">
                    <table class="history-table" id="table-comptes">
                        <thead>
                            <tr>
                                <th>Numéro de Téléphone</th>
                                <th>Solde actuel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($comptes)): ?>
                                <tr><td colspan="2" class="empty-state">Aucun compte actif.</td></tr>
                            <?php else: ?>
                                <?php foreach($comptes as $c): ?>
                                    <tr>
                                        <td class="phone-number"><?= esc($c['numero_telephone']) ?></td>
                                        <td class="fw-bold" style="color: #4CAF50;"><?= number_format($c['solde'], 2, ',', ' ') ?> Ar</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- CRUD : BARÈME DES FRAIS SÉPARÉ ET FILTRABLE -->
        <div class="history-card" style="grid-column: span 12;">
            <div class="card-header">
                <i class="bi bi-cash-stack"></i>
                Gestion du Barème des Frais
                <select id="filter-bareme" class="form-control" style="width: auto; margin-left: auto;" onchange="switchBaremeView(this.value)">
                    <option value="all">Afficher Tout</option>
                    <option value="retrait">Frais de Retrait uniquement</option>
                    <option value="transfert">Frais de Transfert uniquement</option>
                </select>
            </div>
            <div class="card-body">
                <div class="cards-grid" style="margin-bottom: 0;">
                    <!-- Formulaire dynamique -->
                    <div class="operations-card" style="grid-column: span 4; min-height: auto;">
                        <div class="card-header" style="padding: 20px;">
                            <h6 id="form-title" style="margin: 0;">Ajouter une tranche</h6>
                        </div>
                        <div class="card-body" style="padding: 20px;">
                            <form action="<?= base_url('operateur/frais/enregistrer') ?>" method="POST" id="frais-form">
                                <input type="hidden" name="id" id="frais-id">
                                <div class="form-group">
                                    <label class="form-label">Type d'opération</label>
                                    <select name="id_type_operation" id="frais-type" class="form-control" required>
                                        <option value="2">Retrait</option>
                                        <option value="3">Transfert</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Montant Min (Ar)</label>
                                    <input type="number" name="montant_min" id="frais-min" class="form-control" required min="0">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Montant Max (Ar)</label>
                                    <input type="number" name="montant_max" id="frais-max" class="form-control" required min="1">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Frais (Ar)</label>
                                    <input type="number" name="frais" id="frais-val" class="form-control" required min="0">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" id="btn-submit" style="flex: 1;">Ajouter la tranche</button>
                                    <button type="button" class="btn btn-warning d-none" id="btn-cancel" onclick="resetFraisForm()">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Les Tableaux Séparés -->
                    <div style="grid-column: span 8;">
                        <!-- Bloc Retrait -->
                        <div id="section-retrait" class="mb-4">
                            <h6 style="font-weight: 700; color: #FF9800; margin-bottom: 15px;">
                                <i class="bi bi-upload"></i>
                                Barème des Retraits
                            </h6>
                            <div style="max-height: 200px; overflow-y: auto; border: 1px solid #f0f0f0; border-radius: 12px;">
                                <table class="history-table">
                                    <thead>
                                        <tr>
                                            <th>Tranche de montant</th>
                                            <th>Frais</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($baremes_retrait as $b): ?>
                                            <tr>
                                                <td>Entre <?= number_format($b['montant_min'], 0, ',', ' ') ?> et <?= number_format($b['montant_max'], 0, ',', ' ') ?> Ar</td>
                                                <td class="fw-bold" style="color: #e74c3c;"><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</td>
                                                <td>
                                                    <button class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="editFrais(<?= $b['id'] ?>, 2, <?= $b['montant_min'] ?>, <?= $b['montant_max'] ?>, <?= $b['frais'] ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="<?= base_url('operateur/frais/supprimer/'.$b['id']) ?>" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px; color: white;" onclick="return confirm('Supprimer ?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Bloc Transfert -->
                        <div id="section-transfert">
                            <h6 style="font-weight: 700; color: #2196F3; margin-bottom: 15px;">
                                <i class="bi bi-send"></i>
                                Barème des Transferts
                            </h6>
                            <div style="max-height: 200px; overflow-y: auto; border: 1px solid #f0f0f0; border-radius: 12px;">
                                <table class="history-table">
                                    <thead>
                                        <tr>
                                            <th>Tranche de montant</th>
                                            <th>Frais</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($baremes_transfert as $b): ?>
                                            <tr>
                                                <td>Entre <?= number_format($b['montant_min'], 0, ',', ' ') ?> et <?= number_format($b['montant_max'], 0, ',', ' ') ?> Ar</td>
                                                <td class="fw-bold" style="color: #e74c3c;"><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</td>
                                                <td>
                                                    <button class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="editFrais(<?= $b['id'] ?>, 3, <?= $b['montant_min'] ?>, <?= $b['montant_max'] ?>, <?= $b['frais'] ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="<?= base_url('operateur/frais/supprimer/'.$b['id']) ?>" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px; color: white;" onclick="return confirm('Supprimer ?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="/assets/js/dashboard.js"></script>
</body>
</html>