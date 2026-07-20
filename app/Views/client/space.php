<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">Mobile Money Client</span>
        <div class="d-flex align-items-center">
            <span class="text-white me-3 fw-semibold">📱 <?= esc($compte['numero_telephone']) ?></span>
            <a href="<?= base_url('client/logout') ?>" class="btn btn-sm btn-outline-light">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Messages Alertes -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- BLOC SOLDE ACTUEL -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-white bg-success h-100">
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    <h6 class="text-uppercase text-white-50 fw-bold mb-1">Votre Solde Actuel</h6>
                    <h1 class="display-5 fw-bold mb-0"><?= number_format($compte['solde'], 2, ',', ' ') ?> <span class="fs-4">Ar</span></h1>
                </div>
            </div>
        </div>

        <!-- BLOC FAIRE UNE OPÉRATION -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-bold text-secondary border-0">Effectuer une opération</div>
                <div class="card-body">
                    <!-- Formulaires par onglets -->
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-depot-tab" data-bs-toggle="pill" data-bs-target="#pills-depot" type="button" role="tab">Dépôt</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-retrait-tab" data-bs-toggle="pill" data-bs-target="#pills-retrait" type="button" role="tab">Retrait</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-transfert-tab" data-bs-toggle="pill" data-bs-target="#pills-transfert" type="button" role="tab">Transfert</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <!-- Dépot -->
                        <div class="tab-pane fade show active" id="pills-depot" role="tabpanel">
                            <form action="<?= base_url('client/transaction') ?>" method="POST">
                                <input type="hidden" name="type" value="depot">
                                <div class="mb-3">
                                    <label class="form-label">Montant du dépôt (Ar)</label>
                                    <input type="number" name="montant" class="form-control" placeholder="Montant" required min="1">
                                </div>
                                <button type="submit" class="btn btn-success">Valider le dépôt (Automatique)</button>
                            </form>
                        </div>

                        <!-- Retrait -->
                        <div class="tab-pane fade" id="pills-retrait" role="tabpanel">
                            <form action="<?= base_url('client/transaction') ?>" method="POST">
                                <input type="hidden" name="type" value="retrait">
                                <div class="mb-3">
                                    <label class="form-label">Montant à retirer (Ar)</label>
                                    <input type="number" name="montant" class="form-control" placeholder="Montant" required min="100">
                                </div>
                                <button type="submit" class="btn btn-warning text-white">Confirmer le Retrait</button>
                            </form>
                        </div>

                        <!-- Transfert -->
                        <div class="tab-pane fade" id="pills-transfert" role="tabpanel">
                            <form action="<?= base_url('client/transaction') ?>" method="POST">
                                <input type="hidden" name="type" value="transfert">
                                <div class="mb-3">
                                    <label class="form-label">Numéro du destinataire</label>
                                    <input type="tel" name="destinataire" class="form-control" placeholder="Ex: 037XXXXXXX" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Montant à transférer (Ar)</label>
                                    <input type="number" name="montant" class="form-control" placeholder="Montant" required min="100">
                                </div>
                                <button type="submit" class="btn btn-primary">Envoyer l'argent</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LISTE HISTORIQUE -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 fw-bold text-secondary">Historique des transactions</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
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
                                <tr><td colspan="6" class="text-center text-muted py-4">Aucune transaction enregistrée.</td></tr>
                            <?php else: ?>
                                <?php foreach($historique as $op): ?>
                                    <tr>
                                        <td class="text-muted"><?= esc($op['date_operation']) ?></td>
                                        <td>
                                            <?php 
                                                if($op['id_type_operation'] == 1) echo '<span class="badge bg-success-subtle text-success">Dépôt</span>';
                                                elseif($op['id_type_operation'] == 2) echo '<span class="badge bg-warning-subtle text-warning">Retrait</span>';
                                                else echo '<span class="badge bg-primary-subtle text-primary">Transfert</span>';
                                            ?>
                                        </td>
                                        <td><?= esc($op['numero_expediteur']) ?></td>
                                        <td><?= $op['numero_destinataire'] ? esc($op['numero_destinataire']) : '-' ?></td>
                                        <td class="fw-bold"><?= number_format($op['montant'], 0, ',', ' ') ?></td>
                                        <td class="text-danger"><?= $op['frais'] > 0 ? '+ ' . number_format($op['frais'], 0, ',', ' ') : '0' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>