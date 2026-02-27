<x-mail::message>
# Invitation à vous inscrire

Bonjour,

Vous avez été invité à créer un compte sur notre plateforme. Cliquez sur le bouton ci-dessous pour finaliser votre inscription.

<x-mail::button :url="$link">
S'inscrire
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
