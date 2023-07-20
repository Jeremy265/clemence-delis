class Service {

    constructor(icon, title, text) {
        this._icon = icon;
        this._title = title;
        this._text = text;
    }

    toString(delay = 1) {
        return "<div class='wow fadeInUp' data-wow-delay='" + delay + "'>" +
"                        <div class='service-item'>" +
"                            <div class='service-icon'>" +
"                                <i class='" + this._icon + "'></i>" +
"                            </div>" +
"                            <div class='service-text'>" +
"                               <h3>" + this._title + "</h3>" +
"                               <div>" + this._text + "</div>" +
"                            </div>" +
"                        </div>" +
"                    </div>";

    }

}

const services = [
    new Service("fa fa-user-friends", "Pour les particuliers",
        "<p>Un suivi psychologique aux particuliers de tous secteurs professionnels, en activité, en reconversion ou en recherche d’emploi, par téléphone ou par visio-conférence</p>"
    ),
    new Service("fa fa-building", "Pour les entreprises",
        "<p>Accompagnement individuel ou collectif des encadrants et de leurs équipes, sur site ou par visio-conférence :</p>" +
        "<ul>" +
            "<li>Conseil en management et organisation du travail;</li>" +
            "<li>Soutien suite à des évènements traumatiques ou à des situations de souffrances au travail;</li>" +
            "<li>Rétablir la communication et la coopération entre professionnels;</li>" +
            "<li>Animation de groupes d'analyse des pratiques.</li>" +
        "</ul>"
    ),
]

$(document).ready(() => {
    let servicesHtml = "";
    services.forEach((value, index) => {
        servicesHtml += value.toString();
    });
    $(".services").html(servicesHtml);
});
