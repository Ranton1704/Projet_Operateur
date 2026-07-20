<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div style="margin-bottom: 30px;">
    <h2 style="font-size: 32px; font-weight: 700; color: #2c3e50; margin: 0;">
        <i class="bi bi-building" style="color: #66BB6A;"></i> Autres Opérateurs
    </h2>
    <p style="color: #7f8c8d; margin-top: 8px; font-size: 16px;">Gestion des opérateurs externes et de leurs préfixes</p>
</div>

<!-- Messages Alertes -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i>
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="cards-grid">
    <!-- Formulaire d'ajout d'opérateur -->
    <div class="operations-card" style="grid-column: span 6;">
        <div class="card-header">
            <i class="bi bi-plus-circle" style="color: #66BB6A;"></i>
            Ajouter un Opérateur
        </div>
        <div class="card-body" style="padding: 25px;">
            <form action="<?= base_url('operateur/ajouter-autre-operateur') ?>" method="POST">
                <div class="form-group">
                    <label class="form-label">Nom de l'opérateur</label>
                    <input type="text" name="nom" class="form-control" placeholder="Ex: Telma, Orange" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Commission supplémentaire (%)</label>
                    <input type="number" name="commission_pourcentage" class="form-control" placeholder="Ex: 5.0" step="0.1" min="0" max="100" required>
                    <small style="color: #7f8c8d; font-size: 12px;">Pourcentage appliqué sur les transferts vers cet opérateur</small>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Ajouter l'opérateur
                </button>
            </form>
        </div>
    </div>

    <!-- Formulaire d'ajout de préfixe -->
    <div class="operations-card" style="grid-column: span 6;">
        <div class="card-header">
            <i class="bi bi-phone" style="color: #2196F3;"></i>
            Ajouter un Préfixe
        </div>
        <div class="card-body" style="padding: 25px;">
            <form action="<?= base_url('operateur/ajouter-prefixe-autre-operateur') ?>" method="POST">
                <div class="form-group">
                    <label class="form-label">Opérateur</label>
                    <select name="id_autre_operateur" class="form-control" required>
                        <option value="">Sélectionner un opérateur</option>
                        <?php foreach($operateurs as $op): ?>
                            <option value="<?= $op['id'] ?>"><?= esc($op['nom']) ?> (<?= $op['commission_pourcentage'] ?>%)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Préfixe</label>
                    <input type="text" name="prefixe" class="form-control" placeholder="Ex: 032" maxlength="3" required>
                    <small style="color: #7f8c8d; font-size: 12px;">Les 3 premiers chiffres du numéro</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Ajouter le préfixe
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Liste des opérateurs et leurs préfixes -->
<div class="operations-card" style="margin-top: 30px;">
    <div class="card-header">
        <i class="bi bi-list-ul" style="color: #FF9800;"></i>
        Opérateurs Configurés
    </div>
    <div class="card-body" style="padding: 25px;">
        <?php if(empty($operateurs)): ?>
            <p style="text-align: center; color: #95a5a6; padding: 40px;">
                <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 15px;"></i>
                Aucun opérateur configuré
            </p>
        <?php else: ?>
            <?php foreach($operateurs as $operateur): ?>
                <div style="background: #f8f9fa; border-radius: 12px; padding: 25px; margin-bottom: 20px; border-left: 4px solid #66BB6A;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                        <div>
                            <h4 style="margin: 0; color: #2c3e50; font-size: 20px;">
                                <i class="bi bi-building" style="color: #66BB6A;"></i>
                                <?= esc($operateur['nom']) ?>
                            </h4>
                            <span style="color: #7f8c8d; font-size: 14px;">
                                Commission: <strong><?= $operateur['commission_pourcentage'] ?>%</strong>
                            </span>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button onclick="modifierOperateur(<?= $operateur['id'] ?>, '<?= esc($operateur['nom']) ?>', <?= $operateur['commission_pourcentage'] ?>)" 
                                    class="btn btn-secondary" style="padding: 8px 15px; font-size: 13px;">
                                <i class="bi bi-pencil"></i> Modifier
                            </button>
                            <a href="<?= base_url('operateur/supprimer-autre-operateur/' . $operateur['id']) ?>" 
                               class="btn btn-danger" style="padding: 8px 15px; font-size: 13px; text-decoration: none;"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet opérateur ?');">
                                <i class="bi bi-trash"></i> Supprimer
                            </a>
                        </div>
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <h6 style="color: #2c3e50; font-size: 14px; margin-bottom: 10px;">
                            <i class="bi bi-phone"></i> Préfixes associés:
                        </h6>
                        <?php if(empty($operateur['prefixes'])): ?>
                            <span style="color: #95a5a6; font-size: 13px;">Aucun préfixe configuré</span>
                        <?php else: ?>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <?php foreach($operateur['prefixes'] as $prefixe): ?>
                                    <span class="badge badge-transfert" style="padding: 8px 15px; font-size: 14px;">
                                        <i class="bi bi-phone"></i> <?= esc($prefixe['prefixe']) ?>
                                        <a href="<?= base_url('operateur/supprimer-prefixe-autre-operateur/' . $prefixe['id']) ?>" 
                                           style="color: white; margin-left: 8px; text-decoration: none;"
                                           onclick="return confirm('Supprimer ce préfixe ?');">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de modification -->
<div id="modalModification" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 500px;">
        <h3 style="margin: 0 0 20px 0; color: #2c3e50;">
            <i class="bi bi-pencil" style="color: #2196F3;"></i> Modifier l'opérateur
        </h3>
        <form action="<?= base_url('operateur/modifier-autre-operateur') ?>" method="POST">
            <input type="hidden" name="id" id="edit-id">
            <div class="form-group">
                <label class="form-label">Nom de l'opérateur</label>
                <input type="text" name="nom" id="edit-nom" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Commission (%)</label>
                <input type="number" name="commission_pourcentage" id="edit-commission" class="form-control" step="0.1" min="0" max="100" required>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-success" style="flex: 1;">
                    <i class="bi bi-check-lg"></i> Enregistrer
                </button>
                <button type="button" onclick="fermerModal()" class="btn btn-secondary" style="flex: 1;">
                    <i class="bi bi-x-lg"></i> Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function modifierOperateur(id, nom, commission) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nom').value = nom;
    document.getElementById('edit-commission').value = commission;
    document.getElementById('modalModification').style.display = 'flex';
}

function fermerModal() {
    document.getElementById('modalModification').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
