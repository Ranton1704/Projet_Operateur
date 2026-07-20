<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div style="margin-bottom: 30px;">
    <h2 style="font-size: 32px; font-weight: 700; color: #2c3e50; margin: 0;">
        <i class="bi bi-graph-up" style="color: #66BB6A;"></i> Situation des Gains
    </h2>
    <p style="color: #7f8c8d; margin-top: 8px; font-size: 16px;">Répartition des gains : Opérateur principal vs Autres opérateurs</p>
</div>

<div class="cards-grid">
    <!-- Gains Opérateur Principal -->
    <div class="operations-card" style="grid-column: span 12;">
        <div class="card-header">
            <i class="bi bi-building" style="color: #66BB6A;"></i>
            Gains de l'Opérateur Principal
        </div>
        <div class="card-body" style="padding: 25px;">
            <?php if(empty($gains['operateur'])): ?>
                <p style="text-align: center; color: #95a5a6; padding: 40px;">
                    <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 15px;"></i>
                    Aucun gain enregistré pour l'opérateur principal
                </p>
            <?php else: ?>
                <?php 
                    $totalOperateur = 0;
                    foreach($gains['operateur'] as $g) { $totalOperateur += $g['total_gains']; }
                ?>
                <div style="background: linear-gradient(135deg, #66BB6A 0%, #43A047 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 25px;">
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Total des gains</div>
                    <div style="font-size: 42px; font-weight: 700;">
                        <?= number_format($totalOperateur, 0, ',', ' ') ?> <span style="font-size: 18px;">Ar</span>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <?php foreach($gains['operateur'] as $index => $g): ?>
                        <?php
                            $colors = ['#66BB6A', '#2196F3', '#FF9800', '#9C27B0'];
                            $color = $colors[$index % count($colors)];
                            $icons = ['bi-download', 'bi-upload', 'bi-send', 'bi-currency-dollar'];
                            $icon = $icons[$index % count($icons)];
                        ?>
                        <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; border-left: 4px solid <?= $color ?>;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                                <div style="width: 45px; height: 45px; background: <?= $color ?>20; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi <?= $icon ?>" style="font-size: 22px; color: <?= $color ?>;"></i>
                                </div>
                                <div>
                                    <h6 style="margin: 0; color: #2c3e50; font-weight: 700; font-size: 15px;">
                                        <?= ucfirst(esc($g['nom'])) ?>
                                    </h6>
                                </div>
                            </div>
                            <div style="font-size: 28px; font-weight: 700; color: <?= $color ?>;">
                                <?= number_format($g['total_gains'], 0, ',', ' ') ?> <span style="font-size: 14px;">Ar</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Gains Autres Opérateurs -->
    <div class="operations-card" style="grid-column: span 12;">
        <div class="card-header">
            <i class="bi bi-building-exclamation" style="color: #FF9800;"></i>
            Gains des Autres Opérateurs
        </div>
        <div class="card-body" style="padding: 25px;">
            <?php if(empty($gains['autres_operateurs'])): ?>
                <p style="text-align: center; color: #95a5a6; padding: 40px;">
                    <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 15px;"></i>
                    Aucun gain enregistré pour les autres opérateurs
                </p>
            <?php else: ?>
                <?php 
                    $totalAutresOperateurs = 0;
                    $totalCommission = 0;
                    foreach($gains['autres_operateurs'] as $g) { 
                        $totalAutresOperateurs += $g['total_frais'];
                        $totalCommission += $g['total_commission'];
                    }
                ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); color: white; padding: 25px; border-radius: 12px;">
                        <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px;">Total Frais Transferts</div>
                        <div style="font-size: 32px; font-weight: 700;">
                            <?= number_format($totalAutresOperateurs, 0, ',', ' ') ?> <span style="font-size: 16px;">Ar</span>
                        </div>
                    </div>
                    <div style="background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%); color: white; padding: 25px; border-radius: 12px;">
                        <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px;">Total Commission Suppl.</div>
                        <div style="font-size: 32px; font-weight: 700;">
                            <?= number_format($totalCommission, 0, ',', ' ') ?> <span style="font-size: 16px;">Ar</span>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach($gains['autres_operateurs'] as $index => $g): ?>
                        <?php
                            $colors = ['#FF9800', '#9C27B0', '#F44336', '#3F51B5'];
                            $color = $colors[$index % count($colors)];
                        ?>
                        <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; border-left: 4px solid <?= $color ?>;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                                <div style="width: 45px; height: 45px; background: <?= $color ?>20; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-building" style="font-size: 22px; color: <?= $color ?>;"></i>
                                </div>
                                <div>
                                    <h6 style="margin: 0; color: #2c3e50; font-weight: 700; font-size: 15px;">
                                        <?= esc($g['operateur_nom']) ?>
                                    </h6>
                                    <span style="font-size: 12px; color: #7f8c8d;"><?= ucfirst(esc($g['type_nom'])) ?></span>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="color: #7f8c8d; font-size: 13px;">Frais:</span>
                                <span style="font-weight: 700; color: <?= $color ?>;"><?= number_format($g['total_frais'], 0, ',', ' ') ?> Ar</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #7f8c8d; font-size: 13px;">Commission:</span>
                                <span style="font-weight: 700; color: #9C27B0;"><?= number_format($g['total_commission'], 0, ',', ' ') ?> Ar</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
