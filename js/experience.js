class Experience {
    constructor(date, poste, lieu, missions) {
        this._date = date;
        this._poste = poste;
        this._lieu = lieu;
        this._missions = missions;
    }

    toString(leftOrRight = "right") {
        let className = "timeline-item wow ";
        switch (leftOrRight) {
            case "left":
                className += "left slideInLeft";
                break;
            case "right":
                className += "right slideInRight"
                break;
        }
        let text = "<div class='" + className + "' data-wow-delay='0.1s'>" +
"                        <div class='timeline-text'>" +
"                            <div class='timeline-date'>" + this._date + "</div>" +
"                            <h2>" + this._poste + "</h2>" +
"                            <h4>" + this._lieu + "</h4>" +
"                            <ul>";
        for (const mission of this._missions) {
            text += "<li>" + mission + "</li>";
        }
        text += "</ul>" +
"                        </div>" +
"                    </div>"
        return text;
    }
}


const experiences = [
    new Experience("Depuis 2019", "Psychologue clinicienne du travail consultante indépendante", "Lyon", [
        "Conseil et accompagnement des encadrants",
        "Animation de groupe d’analyse de la pratique",
        "Soutien des salariés suite à des événements traumatiques ou à des situations de souffrance au travail"
    ]),
    new Experience("2018", "Master 2 Psychologie du travail", "CNAM, Lyon", [
        "Mémoire : accompagner le développement d'un collectif de soignant"
    ]),
    new Experience("2017 - 2018", "Stage de Master 2 Psychologue du travail", "EHPAD, Lyon", [
        "Accompagner le développement de la cohésion des équipes",
        "Conduite d'entretien et analyse des difficultés",
        "Animation de groupes de travail et d'espaces de discussion"
    ]),
    new Experience("2013 - 2017", "Gouvernante d'EHPAD", "EHPAD, Lyon", [
        "Management d'une équipe de 12 agents de service",
        "Organisation et contrôle du travail et gestion des achats"
    ]),
    new Experience("2013 - 2016", "Animatrice de Groupes d'Analyse de la Pratique", "Relais d'Assistante Maternelle 1, 2, 3 Soleil, Lyon", [
        "Echange autour des pratiques des assistantes maternelles",
        "Recherche de solutions aux situations difficiles du travail quotidien"
    ]),
    new Experience("2011", "Formation au coaching des professionnels", "Cap Réussite, Lyon", [
        "Apprendre à accompagner les professionnels dans l'atteinte de leurs objectifs (recherche d'emploi, amélioration de la communication gestion des conflits...)"
    ]),
    new Experience("2008", "Formation gouvernante en hôtellerie", "AFPA, Saint Priest", []),
    new Experience("2006 - 2011", "Assistance gouvernante", "Hôtel Cité Internationale, Lyon", []),
    new Experience("2003", "Master 1 Psychologie Clinique", "Université Lumière Lyon 2", [
        "Mémoire : la souffrance psychologique des soignants"
    ]),
    new Experience("2000 - 2003", "Stage de Master 1 Psychologue Clinicienne", "HCL Service Pédiatrique, Lyon", [
        "Améliorer la qualité de vie au travail des soignants",
        "Conduite d'entretien individuels et collectifs",
        "Rapport et préconisations en organisation du travail"
    ]),
    new Experience("1991 - 2006", "Assistante maternelle","Lyon", []),
]

$(document).ready(() => {
    let experiencesHtml = "";
    experiences.forEach((value, index) => {
        experiencesHtml += value.toString(index%2 === 0 ? "left" : "right");
    });
    $(".timeline").html(experiencesHtml);
});
