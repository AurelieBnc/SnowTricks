# SnowTricks
OCR - projet de site communautaire

# Projet perso SnowTricks

## Installation

### Télécharger le projet et dézipez le
```
https://github.com/AurelieBnc/SnowTricks/archive/refs/heads/main.zip
```

### Créer un fichier .env.local et réecrire les paramètres d'environnement dans le fichier .env (changer user_db et password_db et les identifiant du compte pour envoyer les mails)

```

MAILER_DSN=gmail://email:password@default?

DATABASE_URL="mysql://root:@127.0.0.1:3306/demo?serverVersion=8"

JWT_SECRET='create-secret-key'


```

### Déplacer le terminal dans le dossier cloné
```
cd snowtricks
```

### Taper les commandes suivantes :
```
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Installation des fixtures 
(vous avez aussi la possibilité d'importer la structure de la base de donnée grace au fichier snowtricks_structure.sql)
```
Dans le dossier doc à la racine du projet :
- récupérer et dézippr le dossier uploads, puis copiez le dans public\images\

- récupérer l'imports sql snowtricks_data.sql puis importer le dans votre application de gestion de base de données
```

### Lancer/arrêter le serveur local Symfony
```
symfony server:start

symfony server:stop
```

### Passer en env de Prod
Lancer la commande:
```
symfony console cache:clear
```

Dans votre fichier .env.local, modifiez:
```
APP_ENV=prod
APP_DEBUG=0
```
