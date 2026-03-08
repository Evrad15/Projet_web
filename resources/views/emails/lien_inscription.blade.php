<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: auto; }
        .btn { display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; 
               text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { color: #888; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Vous êtes invité à rejoindre notre plateforme</h2>
        <p>Bonjour,</p>
        <p>Vous avez été invité à créer un compte client sur notre plateforme de gestion.</p>
        <p>Cliquez sur le bouton ci-dessous pour vous inscrire :</p>
        <a href="{{ $lien }}" class="btn">S'inscrire maintenant</a>
        <p>Ou copiez ce lien dans votre navigateur :</p>
        <p><a href="{{ $lien }}">{{ $lien }}</a></p>
        <div class="footer">
            <p>Si vous n'attendiez pas cet email, ignorez-le.</p>
        </div>
    </div>
</body>
</html>
