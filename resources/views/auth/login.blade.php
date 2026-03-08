<x-guest-layout>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

/* ── OVERRIDE guest-layout pour pleine page ── */
body, html { min-height: 100vh; }

.login-shell {
    min-height: 100vh;
    display: flex;
    font-family: 'DM Sans', sans-serif;
    background: #0f0e17;
    position: relative;
    overflow: hidden;
}

/* ── FOND ANIMÉ ── */
.bg-orbs {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
}

.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.18;
    animation: drift 12s ease-in-out infinite alternate;
}

.orb-1 {
    width: 500px; height: 500px;
    background: #667eea;
    top: -120px; left: -100px;
    animation-delay: 0s;
}

.orb-2 {
    width: 380px; height: 380px;
    background: #764ba2;
    bottom: -80px; right: -60px;
    animation-delay: -4s;
}

.orb-3 {
    width: 260px; height: 260px;
    background: #06b6d4;
    top: 40%; left: 55%;
    animation-delay: -8s;
}

@keyframes drift {
    from { transform: translate(0, 0) scale(1); }
    to   { transform: translate(30px, 20px) scale(1.08); }
}

/* ── PANNEAU GAUCHE (illustration) ── */
.login-left {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 3rem 4rem;
    position: relative;
    z-index: 1;
}

.brand {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 3.5rem;
}

.brand-icon {
    width: 44px; height: 44px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    box-shadow: 0 8px 20px rgba(102,126,234,0.4);
}

.brand-name {
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    font-size: 1.2rem;
    color: #fff;
    letter-spacing: -0.02em;
}

.hero-headline {
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    font-size: clamp(2rem, 3.5vw, 3rem);
    color: #fff;
    line-height: 1.15;
    margin-bottom: 1.2rem;
    letter-spacing: -0.03em;
}

.hero-headline span {
    background: linear-gradient(90deg, #818cf8, #c084fc);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-sub {
    font-size: 1rem;
    color: rgba(255,255,255,0.55);
    line-height: 1.6;
    max-width: 380px;
    margin-bottom: 3rem;
    font-weight: 300;
    font-style: italic;
}

.feature-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    color: rgba(255,255,255,0.7);
    font-size: 0.88rem;
    font-weight: 500;
}

.feature-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: linear-gradient(135deg, #818cf8, #c084fc);
    flex-shrink: 0;
    box-shadow: 0 0 10px rgba(129,140,248,0.6);
}

/* ── PANNEAU DROIT (formulaire) ── */
.login-right {
    width: 480px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
    z-index: 1;
}

.login-card {
    width: 100%;
    max-width: 400px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 24px;
    padding: 2.5rem;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    animation: fadeUp .6s ease both;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

.card-title {
    font-family: 'Syne', sans-serif;
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 0.4rem;
    letter-spacing: -0.02em;
}

.card-sub {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.45);
    margin-bottom: 2rem;
}

/* ── CHAMPS ── */
.field-group {
    margin-bottom: 1.2rem;
}

.field-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 8px;
}

.field-input {
    width: 100%;
    background: rgba(255,255,255,0.06);
    border: 1.5px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 12px 16px;
    color: #fff;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem;
    outline: none;
    transition: border-color .2s, background .2s, box-shadow .2s;
}

.field-input::placeholder { color: rgba(255,255,255,0.25); }

.field-input:focus {
    border-color: #818cf8;
    background: rgba(129,140,248,0.08);
    box-shadow: 0 0 0 4px rgba(129,140,248,0.12);
}

/* Erreurs Laravel */
.field-error {
    margin-top: 6px;
    font-size: 0.75rem;
    color: #f87171;
}

/* ── LIEN MOT DE PASSE ── */
.forgot-link {
    display: block;
    text-align: right;
    font-size: 0.75rem;
    color: rgba(255,255,255,0.4);
    text-decoration: none;
    margin-top: -8px;
    margin-bottom: 1.5rem;
    transition: color .2s;
}

.forgot-link:hover { color: #818cf8; }

/* ── BOUTON SUBMIT ── */
.btn-submit {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-family: 'Syne', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    letter-spacing: 0.02em;
    transition: opacity .2s, transform .2s, box-shadow .2s;
    box-shadow: 0 8px 24px rgba(102,126,234,0.35);
}

.btn-submit:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    box-shadow: 0 12px 28px rgba(102,126,234,0.45);
}

.btn-submit:active { transform: translateY(0); }

/* ── SÉPARATEUR ── */
.divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 1.5rem 0;
}

.divider::before, .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.1);
}

.divider span {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.3);
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

/* ── LIEN INSCRIPTION ── */
.register-row {
    text-align: center;
    font-size: 0.82rem;
    color: rgba(255,255,255,0.4);
}

.register-row a {
    color: #818cf8;
    font-weight: 600;
    text-decoration: none;
    transition: color .2s;
}

.register-row a:hover { color: #c084fc; }

/* ── SESSION STATUS ── */
.session-status {
    background: rgba(16,185,129,0.15);
    border: 1px solid rgba(16,185,129,0.3);
    border-radius: 10px;
    padding: 10px 14px;
    color: #6ee7b7;
    font-size: 0.82rem;
    margin-bottom: 1.2rem;
}

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
    .login-left { display: none; }
    .login-right { width: 100%; padding: 1.5rem; }
    .login-card { max-width: 440px; margin: auto; }
}

@media (max-width: 480px) {
    .login-right { padding: 1rem; align-items: flex-start; padding-top: 2.5rem; }
    .login-card { padding: 1.75rem 1.5rem; border-radius: 20px; }
    .card-title { font-size: 1.3rem; }
}
</style>

<div class="login-shell">

    <!-- Fond animé -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- ── Panneau gauche ── -->
    <div class="login-left">
        <div class="brand">
            <div class="brand-icon">⚡</div>
            <span class="brand-name">projet_web</span>
        </div>

        <h1 class="hero-headline">
            Gérez votre<br>
            inventaire <span>en temps réel</span>
        </h1>
        <p class="hero-sub">
            Suivez vos stocks, commandes et approvisionnements depuis une seule plateforme.
        </p>

        <div class="feature-list">
            <div class="feature-item"><div class="feature-dot"></div> Tableau de bord en temps réel</div>
            <div class="feature-item"><div class="feature-dot"></div> Alertes de stock bas automatiques</div>
            <div class="feature-item"><div class="feature-dot"></div> Suivi des commandes fournisseurs</div>
            <div class="feature-item"><div class="feature-dot"></div> Statistiques et flux détaillés</div>
        </div>
    </div>

    <!-- ── Panneau droit ── -->
    <div class="login-right">
        <div class="login-card">

            <!-- Brand (visible mobile uniquement) -->
            <div class="brand" style="display:none;" id="mobile-brand">
                <div class="brand-icon">⚡</div>
                <span class="brand-name">projet_web</span>
            </div>

            <div class="card-title">Connexion</div>
            <p class="card-sub">Bienvenue, entrez vos identifiants pour continuer.</p>

            <!-- Session status -->
            @if (session('status'))
            <div class="session-status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="field-group">
                    <label for="email" class="field-label">Adresse e-mail</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="field-input"
                        value="{{ old('email') }}"
                        placeholder="vous@exemple.com"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="field-group">
                    <label for="password" class="field-label">Mot de passe</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="field-input"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    />
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mot de passe oublié -->
                @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">
                    Mot de passe oublié ?
                </a>
                @endif

                <!-- Submit -->
                <button type="submit" class="btn-submit">
                    Se connecter →
                </button>

                <!-- Séparateur + inscription -->
                <div class="divider"><span>ou</span></div>

                <div class="register-row">
                    Nouveau client ?
                    <a href="{{ route('register') }}">Créer un compte</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Afficher le logo en mobile
if (window.innerWidth <= 900) {
    document.getElementById('mobile-brand').style.display = 'flex';
    document.getElementById('mobile-brand').style.marginBottom = '1.5rem';
}
</script>
</x-guest-layout>
