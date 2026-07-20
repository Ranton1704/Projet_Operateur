<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<!-- Header Dashboard -->
<div style="margin-bottom: 30px;">
    <h2 style="font-size: 32px; font-weight: 700; color: #2c3e50; margin: 0;">
        <i class="bi bi-speedometer2" style="color: #66BB6A;"></i> Tableau de Bord
    </h2>
    <p style="color: #7f8c8d; margin-top: 8px; font-size: 16px;">Vue d'ensemble de votre système E-Money</p>
</div>

<!-- KPI Cards -->
<div class="cards-grid" style="margin-bottom: 30px;">
    <?php 
        $totalGlobal = 0;
        if(!empty($gains)) { foreach($gains as $g) { $totalGlobal += $g['total_gains']; } }
    ?>
    
    <div class="balance-card" style="grid-column: span 3; padding: 35px; transition: all 0.3s ease;">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.3); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-cash-coin" style="font-size: 24px; color: white;"></i>
            </div>
            <span style="color: rgba(255,255,255,0.9); font-weight: 600;">Revenus Totaux</span>
        </div>
        <div style="font-size: 36px; font-weight: 700; color: white;">
            <?= number_format($totalGlobal, 0, ',', ' ') ?> <span style="font-size: 18px;">Ar</span>
        </div>
    </div>

    <div class="operations-card" style="grid-column: span 3; padding: 35px; transition: all 0.3s ease; border-left: 5px solid #66BB6A;">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
            <div style="width: 50px; height: 50px; background: #e8f5e9; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-phone" style="font-size: 24px; color: #66BB6A;"></i>
            </div>
            <span style="color: #2c3e50; font-weight: 600;">Préfixes Actifs</span>
        </div>
        <div style="font-size: 36px; font-weight: 700; color: #66BB6A;">
            <?= count($prefixes) ?>
        </div>
    </div>

    <div class="operations-card" style="grid-column: span 3; padding: 35px; transition: all 0.3s ease; border-left: 5px solid #2196F3;">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
            <div style="width: 50px; height: 50px; background: #e3f2fd; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-people" style="font-size: 24px; color: #2196F3;"></i>
            </div>
            <span style="color: #2c3e50; font-weight: 600;">Clients Actifs</span>
        </div>
        <div style="font-size: 36px; font-weight: 700; color: #2196F3;">
            <?= count($comptes) ?>
        </div>
    </div>

    <div class="operations-card" style="grid-column: span 3; padding: 35px; transition: all 0.3s ease; border-left: 5px solid #FF9800;">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
            <div style="width: 50px; height: 50px; background: #fff3e0; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-cash-stack" style="font-size: 24px; color: #FF9800;"></i>
            </div>
            <span style="color: #2c3e50; font-weight: 600;">Tranches Frais</span>
        </div>
        <div style="font-size: 36px; font-weight: 700; color: #FF9800;">
            <?= !empty($gains) ? count($gains) : 0 ?>
        </div>
    </div>
</div>

<!-- Mini Views Grid -->
<div class="cards-grid">
    <!-- Mini View: Préfixes -->
    <div class="operations-card" style="grid-column: span 6; min-height: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-phone" style="color: #66BB6A;"></i>
                Préfixes Valides
            </div>
            <a href="<?= base_url('operateur/prefixes') ?>" class="btn btn-primary" style="padding: 8px 20px; font-size: 13px; text-decoration: none;">
                Gérer <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="card-body" style="padding: 25px;">
            <div class="d-flex flex-wrap gap-2">
                <?php foreach(array_slice($prefixes, 0, 8) as $p): ?>
                    <span class="badge badge-transfert" style="padding: 10px 18px; font-size: 14px;">
                        <i class="bi bi-phone"></i>
                        <?= esc($p['prefixe']) ?>
                    </span>
                <?php endforeach; ?>
                <?php if(count($prefixes) > 8): ?>
                    <span class="badge" style="padding: 10px 18px; font-size: 14px; background: #95a5a6; color: white;">
                        +<?= count($prefixes) - 8 ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mini View: Gains -->
    <div class="operations-card" style="grid-column: span 6; min-height: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-graph-up" style="color: #2196F3;"></i>
                Répartition des Gains
            </div>
            <a href="<?= base_url('operateur/gains') ?>" class="btn btn-primary" style="padding: 8px 20px; font-size: 13px; text-decoration:none">
                Détails <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="card-body" style="padding: 25px;">
            <?php if(empty($gains)): ?>
                <p style="text-align: center; color: #95a5a6;">Aucun gain enregistré</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach(array_slice($gains, 0, 3) as $g): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background: #f8f9fa; border-radius: 8px;">
                            <span style="color: #2c3e50; font-weight: 500;"><?= ucfirst(esc($g['nom'])) ?></span>
                            <span style="color: #4CAF50; font-weight: 700;"><?= number_format($g['total_gains'], 0, ',', ' ') ?> Ar</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mini View: Comptes -->
    <div class="operations-card" style="grid-column: span 6; min-height: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-people" style="color: #FF9800;"></i>
        Comptes Récents
            </div>
            <a href="<?= base_url('operateur/comptes') ?>" class="btn btn-primary" style="padding: 8px 20px; font-size: 13px; text-decoration:none">
                Voir tous <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="card-body" style="padding: 25px;">
            <?php if(empty($comptes)): ?>
                <p style="text-align: center; color: #95a5a6;">Aucun compte enregistré</p>
            <?php else: ?>
                <table class="history-table" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th style="padding: 10px;">Numéro</th>
                            <th style="padding: 10px;">Solde</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(array_slice($comptes, 0, 4) as $c): ?>
                            <tr>
                                <td style="padding: 10px;"><?= esc($c['numero_telephone']) ?></td>
                                <td style="padding: 10px; color: #4CAF50; font-weight: 600;"><?= number_format($c['solde'], 0, ',', ' ') ?> Ar</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mini View: Frais -->
    <div class="operations-card" style="grid-column: span 6; min-height: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-cash-stack" style="color: #9C27B0;"></i>
                Barème des Frais
            </div>
            <a href="<?= base_url('operateur/frais') ?>" class="btn btn-primary" style="padding: 8px 20px; font-size: 13px; text-decoration: none;">
                Gérer <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="card-body" style="padding: 25px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div style="text-align: center; padding: 15px; background: #fff3e0; border-radius: 8px;">
                    <i class="bi bi-upload" style="font-size: 24px; color: #FF9800; margin-bottom: 8px;"></i>
                    <div style="font-weight: 700; color: #2c3e50;">Retraits</div>
                    <div style="font-size: 12px; color: #7f8c8d;">Barème actif</div>
                </div>
                <div style="text-align: center; padding: 15px; background: #e3f2fd; border-radius: 8px;">
                    <i class="bi bi-send" style="font-size: 24px; color: #2196F3; margin-bottom: 8px;"></i>
                    <div style="font-weight: 700; color: #2c3e50;">Transferts</div>
                    <div style="font-size: 12px; color: #7f8c8d;">Barème actif</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.balance-card:hover, .operations-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
}
</style>

<?= $this->endSection() ?>