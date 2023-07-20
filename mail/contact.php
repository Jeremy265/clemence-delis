<?php

require '../vendor/autoload.php';

function checkMethodAllowed() {
    $allowedMethods = array('POST');
    if (in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
        return;
    }
    throw new Exception("La méthode " . $_SERVER['REQUEST_METHOD'] . " n'est pas prise en charge", 405);
}

function checkForParameters($parameterKeys) {
    $error = "";
    foreach ($parameterKeys as $parameterKey) {
        $param = isset($_POST[$parameterKey]) ? nl2br(htmlspecialchars($_POST[$parameterKey])) : "";
        switch($parameterKey) {
            case 'number':
                if (!preg_match('/[0-9]{10}/', $param)) {
                    $error .= "Votre numéro de téléphone ne respecte pas le format de 10 chiffres.<br>";
                }
                break;
            case 'email':
                if (!filter_var($param, FILTER_VALIDATE_EMAIL)) {
                    $error .= "Votre adresse e-mail ne respecte pas le format xx@yy.zz.<br>";
                }
                break;
            default:
                if (empty($param)) {
                    $error .= "Un ou plusieurs champs sont vides.<br>";
                }
        }
    }
    if ($error != "") {
        throw new Exception($error, 400);
    }
}

function getParameters($parameterKeys) {
    $arrayOfParameter = array();
    foreach ($parameterKeys as $parameterKey) {
        $arrayOfParameter[$parameterKey] = nl2br(htmlspecialchars($_POST[$parameterKey]));
    }
    return $arrayOfParameter;
}

function getMailHtml($content) {
    return "<!DOCTYPE html>
            <html lang='fr'>
                <head>
                    <meta http-equiv='Content-Type' content='text/html' charset='utf-8'>
                </head>
                <style>
                    body {
                        font-family: 'Gill Sans', sans-serif;
                        font-size: 13pt;
                    }
                    a {
                        color: #0d6efd;
                        text-decoration: none;
                    }
                    .message {
                        border-top: 1px solid black;
                    }
                    .underline {
                        text-decoration: underline;
                    }
                    .italic {
                        font-style: italic;
                    }
                </style>
                <body>
                    {$content}
                </body>
            </html>";
}

function getMailClemence($parameters) {
    $date = date('d/m/Y');
    $heure = date('H:i');
    $name = $parameters['firstName'].' '.$parameters['lastName'];
    $number = rtrim(chunk_split($parameters['number'], 2, '.'), '.');
    return "<p>Bonjour Clémence,</p>".
            "<p class='italic'>Vous avez reçu un nouveau message provenant du formulaire de contact de votre site <a href='https://clemence-delis.herokuapp.com'>clemence-delis.herokuapp.com</a>. Ceci est un message automatique, merci de ne pas y répondre.</p>".
            "<div class='message'>
                 <p><span class='underline'>Expéditeur</span>: <strong>{$name}</strong>, à contacter par téléphone <a href='tel:{}'>{$number}</a> ou par e-mail <a href='mailto:{$parameters['email']}'>{$parameters['email']}</a>.</p>
                 <p><span class='underline'>Sujet</span>: {$parameters['subject']}</p>
                 <p><span class='underline'>Message</span>: </p>
                 <p>{$parameters['message']}</p>
                 Le {$date} à {$heure}
             </div>";
}

function getMailUser($parameters) {
    $date = date('d/m/Y');
    $heure = date('H:i');
    $name = $parameters['firstName'].' '.$parameters['lastName'];
    $number = rtrim(chunk_split($parameters['number'], 2, '.'), '.');
    return "<p>Bonjour {$parameters['firstName']} {$parameters['lastName']},</p>".
            "<p class='italic'>Ceci est un message automatique accusant réception de votre message à Clémence Delis envoyé depuis le formulaire de contact du site <a href='https://clemence-delis.herokuapp.com'>clemence-delis.herokuapp.com</a>. Merci de ne pas y répondre. Vos données personnelles (prénom, nom, numéro de téléphone et votre adresse e-mail) ne sont pas conservées. Si vous n'êtes pas à l'origine de ce message, vous pouvez ignorer cet e-mail.</p>".
            "<div class='message'>
                 <p><span class='underline'>Expéditeur</span>: <strong>{$name}</strong>, à contacter par téléphone <a href='tel:{}'>{$number}</a> ou par e-mail <a href='mailto:{$parameters['email']}'>{$parameters['email']}</a>.</p>
                <p><span class='underline'>Sujet</span>:{$parameters['subject']}</p>
                <p><span class='underline'>Message</span>:</p>
                <p>{$parameters['message']}</p>
                Le {$date} à {$heure}
             </div>";
            "<a href='https://clemence-delis.herokuapp.com'>clemence-delis.herokuapp.com</a>";
}

function sendMail($fromAdress, $fromName, $toAdress, $toName, $subject, $content) {
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom($fromAdress, $fromName);
    $email->setSubject($subject);
    $email->addTo($toAdress, $toName);
    $email->addContent("text/html", $content);
    $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
    $response = $sendgrid->send($email);
    $code = $response->statusCode();
    if ($code != 202) {
        exit(var_dump($code));
        throw new Exception("Désolé, le serveur de messagerie ne répond pas. Veuillez réessayer plus tard ou contactez-moi par téléphone ou par e-mail.", 503);
    }
}

function sendMailClemence($parameters) {
    return sendMail(
        "clem.delis@gmail.com",
        "PRISE DE CONTACT DEPUIS VOTRE SITE",
        "clem.delis@gmail.com",
        "Clémence Delis",
        "Nouveau message depuis le formulaire de votre site",
        getMailHtml(getMailClemence($parameters))
    );
}

function sendMailUser($parameters) {
    return sendMail(
        "clem.delis@gmail.com",
        "Clémence Delis",
        $parameters["email"],
        $parameters['firstName'].' '.$parameters['lastName'],
        "Réception de votre message",
        getMailHtml(getMailUser($parameters))
    );
}

$parameterKeys = ['firstName', 'lastName', 'subject', 'number', 'message', 'email'];
setlocale(LC_TIME, "fr_FR");
date_default_timezone_set('Europe/Paris');

try {
    checkMethodAllowed();
    checkForParameters($parameterKeys);
    $parameters = getParameters($parameterKeys);
    sendMailClemence($parameters);
    sendMailUser($parameters);
    http_response_code(204);
} catch (Exception $e) {
     http_response_code($e->getCode());
     echo $e->getMessage();
}

?>
