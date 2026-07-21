<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="cards-grid">
    <!-- CRUD : BARÈME DES FRAIS -->
    <div class="history-card" style="grid-column: span 12;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-cash-stack"></i>
                Gestion du Barème des Frais
            </div>
            <button type="button" class="btn btn-success" onclick="toggleForm()" style="padding: 12px 25px; font-size: 14px;">
                <i class="bi bi-plus-circle"></i> Ajouter une tranche
            </button>
        </div>
        <div class="card-body">
            <!-- Formulaire caché par défaut -->
            <div id="frais-form-container" style="display: none; margin-bottom: 30px; padding: 30px; background: #f8f9fa; border-radius: 12px; border: 2px solid #e8f5e9;">
                <form action="<?= base_url('operateur/frais/enregistrer') ?>" method="POST" id="frais-form">
                    <input type="hidden" name="id" id="frais-id">
                    <div class="cards-grid" style="margin-bottom: 0;">
                        <div style="grid-column: span 3;">
                            <div class="form-group">
                                <label class="form-label">Type d'opération</label>
                                <select name="id_type_operation" id="frais-type" class="form-control" required>
                                    <option value="2">Retrait</option>
                                    <option value="3">Transfert</option>
                                </select>
                            </div>
                        </div>
                        <div style="grid-column: span 3;">
                            <div class="form-group">
                                <label class="form-label">Montant Min (Ar)</label>
                                <input type="number" name="montant_min" id="frais-min" class="form-control" required min="0">
                            </div>
                        </div>
                        <div style="grid-column: span 3;">
                            <div class="form-group">
                                <label class="form-label">Montant Max (Ar)</label>
                                <input type="number" name="montant_max" id="frais-max" class="form-control" required min="1">
                            </div>
                        </div>
                        <div style="grid-column: span 2;">
                            <div class="form-group">
                                <label class="form-label">Frais (Ar)</label>
                                <input type="number" name="frais" id="frais-val" class="form-control" required min="0">
                            </div>
                        </div>
                        <div style="grid-column: span 2;">
                            <div class="form-group">
                                <label class="form-label">Promotion même opérateur (%)</label>
                                <input type="number" name="promotion_pourcentage" id="frais-promo" class="form-control" required min="0" max="100" step="0.01" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary" id="btn-submit" style="flex: 1; padding: 15px;">Enregistrer</button>
                        <button type="button" class="btn btn-warning d-none" id="btn-cancel" onclick="resetFraisForm()" style="padding: 15px 30px;">Annuler</button>
                        <button type="button" class="btn btn-secondary" onclick="toggleForm()" style="padding: 15px 30px;">Fermer</button>
                    </div>
                </form>
            </div>

            <!-- Les Tableaux Séparés -->
            <div class="cards-grid" style="margin-bottom: 0;">
                <!-- Bloc Retrait -->
                <div style="grid-column: span 6;">
                    <div class="operations-card" style="min-height: auto;">
                        <div class="card-header">
                            <i class="bi bi-upload" style="color: #FF9800;"></i>
                            Barème des Retraits
                        </div>
                        <div class="card-body" style="padding: 25px;">
                            <div style="max-height: 400px; overflow-y: auto;">
                                <table class="history-table">
                                    <thead>
                                        <tr>
                                            <th>Tranche de montant</th>
                                            <th>Frais</th>
                                            <th>Promo même op.</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($baremes_retrait as $b): ?>
                                            <tr>
                                                <td>Entre <?= number_format($b['montant_min'], 0, ',', ' ') ?> et <?= number_format($b['montant_max'], 0, ',', ' ') ?> Ar</td>
                                                <td class="fw-bold" style="color: #e74c3c;"><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</td>
                                                <td><?= number_format($b['promotion_pourcentage'] ?? 0, 2, ',', ' ') ?>%</td>
                                                <td>
                                                    <button class="btn btn-primary" style="padding: 8px 15px; font-size: 13px;" onclick="editFrais(<?= $b['id'] ?>, 2, <?= $b['montant_min'] ?>, <?= $b['montant_max'] ?>, <?= $b['frais'] ?>, <?= $b['promotion_pourcentage'] ?? 0 ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="<?= base_url('operateur/frais/supprimer/'.$b['id']) ?>" class="btn btn-warning" style="padding: 8px 15px; font-size: 13px; color: white;" onclick="return confirm('Supprimer ?')">
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

                <!-- Bloc Transfert -->
                <div style="grid-column: span 6;">
                    <div class="operations-card" style="min-height: auto;">
                        <div class="card-header">
                            <i class="bi bi-send" style="color: #2196F3;"></i>
                            Barème des Transferts
                        </div>
                        <div class="card-body" style="padding: 25px;">
                            <div style="max-height: 400px; overflow-y: auto;">
                                <table class="history-table">
                                    <thead>
                                        <tr>
                                            <th>Tranche de montant</th>
                                            <th>Frais</th>
                                            <th>Promo même op.</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($baremes_transfert as $b): ?>
                                            <tr>
                                                <td>Entre <?= number_format($b['montant_min'], 0, ',', ' ') ?> et <?= number_format($b['montant_max'], 0, ',', ' ') ?> Ar</td>
                                                <td class="fw-bold" style="color: #e74c3c;"><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</td>
                                                <td><?= number_format($b['promotion_pourcentage'] ?? 0, 2, ',', ' ') ?>%</td>
                                                <td>
                                                    <button class="btn btn-primary" style="padding: 8px 15px; font-size: 13px;" onclick="editFrais(<?= $b['id'] ?>, 3, <?= $b['montant_min'] ?>, <?= $b['montant_max'] ?>, <?= $b['frais'] ?>, <?= $b['promotion_pourcentage'] ?? 0 ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="<?= base_url('operateur/frais/supprimer/'.$b['id']) ?>" class="btn btn-warning" style="padding: 8px 15px; font-size: 13px; color: white;" onclick="return confirm('Supprimer ?')">
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

<script>
function toggleForm() {
    const formContainer = document.getElementById('frais-form-container');
    formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
}

function editFrais(id, type, min, max, frais, promotion = 0) {
    document.getElementById('frais-id').value = id;
    document.getElementById('frais-type').value = type;
    document.getElementById('frais-min').value = min;
    document.getElementById('frais-max').value = max;
    document.getElementById('frais-val').value = frais;
    document.getElementById('frais-promo').value = promotion;
    document.getElementById('btn-submit').textContent = 'Modifier';
    document.getElementById('btn-cancel').classList.remove('d-none');
    document.getElementById('frais-form-container').style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetFraisForm() {
    document.getElementById('frais-form').reset();
    document.getElementById('frais-id').value = '';
    document.getElementById('btn-submit').textContent = 'Enregistrer';
    document.getElementById('btn-cancel').classList.add('d-none');
}
</script>

<?= $this->endSection() ?>
