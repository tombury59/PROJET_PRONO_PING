<h1 align="center">Prono Ping</h1>

<p align="center">
  <em>L'application de pronostics de tennis de table du club — pronostique, marque des points, grimpe au classement.</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Docker-ready-2496ED?logo=docker&logoColor=white" alt="Docker ready">
</p>

## Points forts

- **Pronostics par journée** : les joueurs prédisent les scores des rencontres, avec date limite et verrouillage automatique.
- **Classement automatique** : les points tombent dès que l'admin saisit un résultat (score exact = 3 pts, bon vainqueur = 1 pt).
- **Joker ×2 & questions bonus** : un joker par phase pour doubler ses points, plus des questions bonus à 5 pts.
- **Notifications push (Web Push)** : rappels avant la date limite et alerte quand un résultat est saisi.
- **Thème personnalisable** : l'admin règle les couleurs par zone (boutons, header, navbar, fond) et le logo du club.
- **Mode sombre** : bascule clair/sombre avec transition animée, mémorisée par appareil.
- **PWA** : installable sur mobile (« Ajouter à l'écran d'accueil »).

## Aperçu

**Prono Ping** est une application web développée avec **Laravel 11** pour animer un concours de pronostics au sein d'un club de tennis de table. L'administrateur crée les phases de la saison et les journées de rencontres ; les joueurs pronostiquent les scores avant la date limite ; une fois les résultats saisis, le classement et les points sont recalculés automatiquement et les joueurs sont notifiés.

Le projet tourne en production dans un conteneur **Docker** (Caddy + PHP-FPM + scheduler) avec une base **PostgreSQL**.

## Utilisation

Deux guides pas-à-pas sont fournis dans [`documentation/`](documentation/) :

- **[Guide du joueur](documentation/HOW-USER-USE.md)** — pronostiquer, poser un joker, répondre aux questions bonus, suivre le classement.
- **[Guide administrateur](documentation/HOW-ADMIN-USE.md)** — créer les phases et journées, saisir les résultats, valider les bonus, personnaliser l'apparence.

**Barème des points :**

| Situation | Points |
| --- | --- |
| Score exact | 3 |
| Bon vainqueur seulement | 1 |
| Mauvais pronostic | 0 |
| Pronostic avec joker | × 2 |
| Question bonus juste | 5 |

## Installation

### En local (développement)

Prérequis : PHP 8.4, Composer, Node.js 22, et une base MySQL ou PostgreSQL.

```bash
git clone https://github.com/tombury59/PROJET_PRONO_PING.git
cd PROJET_PRONO_PING

composer install
npm install

cp .env.example .env
php artisan key:generate
php artisan migrate --seed   # crée un compte admin de démo (admin / password)

npm run dev                  # dans un terminal
php artisan serve            # dans un autre
```

L'app est alors disponible sur http://localhost:8000.

### En production (Docker)

```bash
git clone https://github.com/tombury59/PROJET_PRONO_PING.git prono && cd prono
cp .env.docker.example .env.docker   # puis renseigner APP_KEY, APP_URL, VAPID...
docker compose up -d --build
docker compose exec app php artisan db:seed --class=UserSeeder --force
```

Le conteneur exécute automatiquement les migrations au démarrage (`RUN_MIGRATIONS=true`) et lance le scheduler (rappels de pronostics). Placer un reverse proxy (Caddy, Nginx…) devant pour le HTTPS.

## Stack technique

- **Backend** : Laravel 11, PHP 8.4, Eloquent
- **Frontend** : Blade, Tailwind CSS, Alpine.js, Vite
- **Base de données** : PostgreSQL (prod) / MySQL (dev)
- **Notifications** : Web Push (VAPID)
- **Déploiement** : Docker Compose (Caddy + PHP-FPM + Supervisor)

## Tests

```bash
php artisan test
```

## Contribuer

Ce projet est développé pour le club. Suggestions et retours bienvenus via les *issues* du dépôt. Pour une contribution, ouvre une *pull request* décrivant clairement le changement.

## 📄 Licence

Projet interne du club. Le framework Laravel sous-jacent est distribué sous licence [MIT](https://opensource.org/licenses/MIT).
