document.addEventListener('DOMContentLoaded', function() {
    // 1. Filtrer dynamiquement l'affichage des sections Retrait / Transfert
    window.switchBaremeView = function(value) {
        const retrait = document.getElementById('section-retrait');
        const transfert = document.getElementById('section-transfert');
        
        if (value === 'retrait') {
            retrait.style.display = 'block';
            transfert.style.display = 'none';
        } else if (value === 'transfert') {
            retrait.style.display = 'none';
            transfert.style.display = 'block';
        } else {
            retrait.style.display = 'block';
            transfert.style.display = 'block';
        }
    };

    // 2. Recherche dynamique en temps réel pour la liste des comptes clients
    const searchCompte = document.getElementById('search-compte');
    if (searchCompte) {
        searchCompte.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#table-comptes tbody tr');
            
            rows.forEach(row => {
                let phoneCell = row.querySelector('.phone-number');
                if (phoneCell) {
                    let text = phoneCell.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                }
            });
        });
    }

    // Fonctions CRUD existantes adaptées
    window.editFrais = function(id, type, min, max, frais) {
        document.getElementById('frais-id').value = id;
        document.getElementById('frais-type').value = type;
        document.getElementById('frais-min').value = min;
        document.getElementById('frais-max').value = max;
        document.getElementById('frais-val').value = frais;

        document.getElementById('form-title').innerText = "Modifier la tranche #" + id;
        document.getElementById('btn-submit').innerText = "Enregistrer les modifications";
        document.getElementById('btn-submit').className = "btn btn-success";
        document.getElementById('btn-submit').style.flex = "1";
        document.getElementById('btn-cancel').classList.remove('d-none');
    };

    window.resetFraisForm = function() {
        document.getElementById('frais-form').reset();
        document.getElementById('frais-id').value = "";
        document.getElementById('form-title').innerText = "Ajouter une tranche";
        document.getElementById('btn-submit').innerText = "Ajouter la tranche";
        document.getElementById('btn-submit').className = "btn btn-primary";
        document.getElementById('btn-submit').style.flex = "1";
        document.getElementById('btn-cancel').classList.add('d-none');
    };
});
