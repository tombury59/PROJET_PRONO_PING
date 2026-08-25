// Formulaire de création d'une journée : plusieurs rencontres et jusqu'à
// 3 questions bonus saisies d'un coup, avec ajout/suppression de lignes.
window.Alpine.data('journeeForm', (rencontresInitiales, bonusInitial, defautDateHeure) => ({
    defautDateHeure: defautDateHeure || '',
    rencontres: [],
    bonus: [],

    init() {
        this.rencontres = (rencontresInitiales && rencontresInitiales.length)
            ? rencontresInitiales.map((r) => ({
                equipe_1: r.equipe_1 ?? '',
                equipe_2: r.equipe_2 ?? '',
                nb_matchs: r.nb_matchs ?? 18,
                date_heure: r.date_heure ?? this.defautDateHeure,
            }))
            : [this.ligneVide(), this.ligneVide(), this.ligneVide()];

        this.bonus = (bonusInitial && bonusInitial.length)
            ? bonusInitial.map((b) => ({ question: b.question ?? '', description: b.description ?? '' }))
            : [];
    },

    ligneVide() {
        return { equipe_1: '', equipe_2: '', nb_matchs: 18, date_heure: this.defautDateHeure };
    },

    ajouterRencontre() {
        this.rencontres.push(this.ligneVide());
    },

    supprimerRencontre(index) {
        this.rencontres.splice(index, 1);

        if (this.rencontres.length === 0) {
            this.rencontres.push(this.ligneVide());
        }
    },

    appliquerDefautAuxVides() {
        this.rencontres.forEach((r) => {
            if (! r.date_heure) {
                r.date_heure = this.defautDateHeure;
            }
        });
    },

    ajouterBonus() {
        if (this.bonus.length < 3) {
            this.bonus.push({ question: '', description: '' });
        }
    },

    supprimerBonus(index) {
        this.bonus.splice(index, 1);
    },
}));
