<?php
namespace App\Models;
use CodeIgniter\Model;

class PrefixeAutreOperateurModel extends Model {
    protected $table = 'prefixes_autres_operateurs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_autre_operateur', 'id_prefixe'];

    // Récupérer les préfixes d'un opérateur spécifique
    public function getPrefixesByOperateur($idOperateur) {
        return $this->select('prefixes_autres_operateurs.id, prefixes.prefixe')
                    ->join('prefixes', 'prefixes.id = prefixes_autres_operateurs.id_prefixe')
                    ->where('id_autre_operateur', $idOperateur)
                    ->findAll();
    }

    // Vérifier si un préfixe appartient à un autre opérateur
    public function isAutreOperateur($prefixe) {
        $result = $this->select('id_autre_operateur, autres_operateurs.nom, autres_operateurs.commission_pourcentage')
                        ->join('autres_operateurs', 'autres_operateurs.id = prefixes_autres_operateurs.id_autre_operateur')
                        ->join('prefixes', 'prefixes.id = prefixes_autres_operateurs.id_prefixe')
                        ->where('prefixes.prefixe', $prefixe)
                        ->first();
        return $result;
    }
}
