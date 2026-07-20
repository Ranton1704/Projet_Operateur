<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="cards-grid">
    <!-- PRÉFIXES -->
    <div class="operations-card" style="grid-column: span 12;">
        <div class="card-header">
            <i class="bi bi-phone"></i>
            Gestion des Préfixes Valides
        </div>
        <div class="card-body" style="padding: 50px;">
            <form action="<?= base_url('operateur/prefixe') ?>" method="POST" class="d-flex gap-4 mb-5">
                <input type="text" name="prefixe" class="form-control" placeholder="Ex: 034" required maxlength="5" style="padding: 18px 25px; font-size: 16px;">
                <button type="submit" class="btn btn-success" style="padding: 18px 35px; font-size: 16px;">
                    <i class="bi bi-plus"></i>
                    Ajouter
                </button>
            </form>
            <div style="padding-top: 30px; border-top: 2px solid #f0f0f0;">
                <h6 style="font-size: 16px; font-weight: 600; color: #2c3e50; margin-bottom: 20px;">
                    <i class="bi bi-list-check"></i> Liste des préfixes actifs
                </h6>
                <div class="d-flex flex-wrap gap-4">
                    <?php foreach($prefixes as $p): ?>
                        <span class="badge badge-transfert" style="padding: 15px 25px; font-size: 16px;">
                            <i class="bi bi-phone"></i>
                            <?= esc($p['prefixe']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
