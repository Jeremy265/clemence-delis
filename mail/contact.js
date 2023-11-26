$(function () {

    $("#contactForm input, #contactForm textarea").jqBootstrapValidation({
        preventSubmit: true,
        submitError: function () {
        },
        submitSuccess: function (_, event) {
            event.preventDefault();
            const firstName = $("input#firstName").val();
            const lastName = $("input#lastName").val();
            const email = $("input#email").val();
            const number = $("input#number").val();
            const subject = $("input#subject").val();
            const message = $("textarea#message").val();

            $("#sendMessageButton").attr("disabled", "disabled");
            $("#feedback").html("");

            $.ajax({
                url: "mail/index.php",
                type: "POST",
                data: {
                    firstName: firstName,
                    lastName: lastName,
                    mail: email,
                    number: number,
                    subject: subject,
                    message: message
                },
                cache: false,
                success: function () {
                    $('#feedback').append(
                        "<div class='alert alert-success'>" +
                            "<strong>Votre message a bien été envoyé. </strong>" +
                        "</div>"
                    );
                    $('#contactForm').trigger("reset");
                },
                error: function (response) {
                    let msg = response.responseText;
                    if (msg === "") {
                        msg = "Erreur " + response.statusCode + " : Une erreur s'est produite. Veuillez réessayer plus tard ou contactez-moi par téléphone ou par e-mail.";
                    }
                    $('#feedback').append(
                        "<div class='alert alert-danger'>" +
                            "<strong>" + msg + "</strong>" +
                        "</div>"
                    );
                },
                complete: function () {
                    $("#sendMessageButton").removeAttr("disabled");
                }
            });
        },
        filter: function () {
            return $(this).is(":visible");
        },
    });

    $("a[data-toggle=\"tab\"]").click(function (e) {
        e.preventDefault();
        $(this).tab("show");
    });
});

$("#contact input").on("input", () => {
    if ($("#feedback").html() === "") {
        return;
    }
    $("#feedback").html("");
})
