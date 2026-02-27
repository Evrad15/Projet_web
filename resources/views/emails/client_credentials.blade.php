<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vos identifiants de connexion</title>
</head>
<body style="font-family: sans-serif; color: #333; padding: 20px;">
    <h2>Bonjour {{ $name }},</h2>

    <p>Votre compte a été créé. Voici vos identifiants pour vous connecter :</p>

    <table style="border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="padding: 6px 12px; font-weight: bold;">Email :</td>
            <td style="padding: 6px 12px;">{{ $email }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px; font-weight: bold;">Mot de passe :</td>
            <td style="padding: 6px 12px;">{{ $password }}</td>
        </tr>
    </table>

    <p>Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe dès votre première connexion.</p>

    <p>Cordialement,<br>L'équipe</p>
</body>
</html>
