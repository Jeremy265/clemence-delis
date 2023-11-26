<?php

function isMethodAllowedOrThrow()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception("La méthode " . $_SERVER['REQUEST_METHOD'] . " n'est pas prise en charge", 405);
}

function checkParametersOrThow($parameterKeys)
{
    foreach ($parameterKeys as $parameterKey) {
        $param = isset($_POST[$parameterKey]) ? nl2br(htmlspecialchars($_POST[$parameterKey])) : "";
        switch ($parameterKey) {
            case 'number':
                if (!preg_match('/[0-9]{10}/', $param)) {
                    throw new Exception('Votre numéro de téléphone ne respecte pas le format de 10 chiffres', 400);
                }
                break;
            case 'mail':
                if (!filter_var($param, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Votre adresse e-mail ne respecte pas le format xx@yy.zz', 400);
                }
                break;
            default:
                if (empty($param)) {
                    throw new Exception('Un ou plusieurs champs sont vides', 400);
                }
        }
    }
}

function getParametersOrThrow($parameterKeys)
{
    checkParametersOrThow($parameterKeys);
    $parameters = array();
    foreach ($parameterKeys as $parameterKey) {
        $parameters[$parameterKey] = nl2br(htmlspecialchars($_POST[$parameterKey]));
    }
    return array_merge($parameters, [ 'name' => $parameters['firstName'] . ' ' . $parameters['lastName'], 'url' => 'https://psy-delis.fr', 'date' => date('d/m/Y'), 'heure' => date('H:i') ]);
}

function getMailTemplate($content)
{
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
                    .attribute {
                        font-weight: bold;
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

function getMailTemplateClemence($parameters)
{
    return getMailTemplate(
        "<p>Bonjour Clémence,</p>" .
        "<p>Vous avez reçu un nouveau message depuis le formulaire de contact du site <a href='{$parameters['url']}'>{$parameters['url']}</a>.</p>" .
        "<p><span class='attribute'>Expéditeur</span>: {$parameters['name']}, à contacter par téléphone <a href='tel:{$parameters['number']}'>{$parameters['number']}</a> ou par e-mail <a href='mailto:{$parameters['mail']}'>{$parameters['mail']}</a>.</p>" .
        "<p><span class='attribute'>Sujet</span>: {$parameters['subject']}</p>" .
        "<p><span class='attribute'>Message</span>: </p>" .
        "<p>{$parameters['message']}</p>" .
        "<p>Le {$parameters['date']} à {$parameters['heure']}<br><a href='{$parameters['url']}'>{$parameters['url']}</a></p>"
    );
}

function getMailTemplateUser($parameters)
{
    return getMailTemplate(
        "<p>Bonjour {$parameters['firstName']} {$parameters['lastName']},</p>" .
        "<p>Ceci est un message automatique accusant réception de votre message à Clémence Delis envoyé depuis le formulaire de contact du site <a href='{$parameters['url']}'>{$parameters['url']}</a>.</p>" .
        "<p><span class='attribute'>Expéditeur</span>: {$parameters['name']}, à contacter par téléphone <a href='tel:{$parameters['number']}'>{$parameters['number']}</a> ou par e-mail <a href='mailto:{$parameters['mail']}'>{$parameters['mail']}</a>.</p>" .
        "<p><span class='attribute'>Sujet</span>: {$parameters['subject']}</p>" .
        "<p><span class='attribute'>Message</span>:</p>" .
        "<p>{$parameters['message']}</p>" .
        "<p>Le {$parameters['date']} à {$parameters['heure']}<br><a href='{$parameters['url']}'>{$parameters['url']}</a></p>" .
        "<p class='italic'>Vos données personnelles (prénom, nom, numéro de téléphone et adresse e-mail) ne sont pas conservées. Si vous n'êtes pas à l'origine de ce message, vous pouvez ignorer cet e-mail.</p>"
    );
}

function sendMail($params)
{
    return mail(
        $params['to'],
        $params['subject'],
        $params['content'],
        array(
            'From' => $params['from'],
            'Reply-To' => $params['from'],
            'X-Mailer' => 'PHP/' . phpversion(),
            'Content-type' => 'text/html; charset=utf-8',
            'MIME-Version' => '1.0'
        )
    );
}

function sendMailClemenceOrThrow($parameters)
{
    if (
        !sendMail(
            array(
                'to' => 'clemence@psy-delis.fr',
                'from' => 'clemence@psy-delis.fr',
                'subject' => 'Contact depuis psy-delis.fr',
                'content' => getMailTemplateClemence($parameters)
            )
        )
    )
        throw new Exception('Une erreur est survenue à l\'envoi du message. Vous pouvez me joindre directement par e-mail ou par téléphone', 500);
}

function sendMailUserOrThrow($parameters)
{
    sendMail(
        array(
            'to' => $parameters["mail"],
            'from' => 'Clemence Delis<clemence@psy-delis.fr>',
            'subject' => 'Confirmation de réception de votre message',
            'content' => getMailTemplateUser($parameters)
        )
    );
}

$parameterKeys = [ 'firstName', 'lastName', 'subject', 'number', 'message', 'mail' ];
setlocale(LC_TIME, "fr_FR");
date_default_timezone_set('Europe/Paris');

try {
    isMethodAllowedOrThrow();
    $parameters = getParametersOrThrow($parameterKeys);
    sendMailClemenceOrThrow($parameters);
    sendMailUserOrThrow($parameters);
    http_response_code(204);
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo $e->getMessage();
}

?>