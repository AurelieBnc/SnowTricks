# SnowTricks
OCR - projet de site communautaire

# Projet perso SnowTricks

## Installation

### Cloner le projet
```
git clone git@github.com:AurelieBnc/SnowTricks.git
```

### Créer un fichier .env.local et réecrire les paramètres d'environnement dans le fichier .env (changer user_db et password_db et les identifiant du compte pour envoyer les mails)

```

MAILER_DSN=gmail://email:password@default?

DATABASE_URL="mysql://root:root@127.0.0.1:3306/demo?serverVersion=8"


```

### Déplacer le terminal dans le dossier cloné
```
cd snowtricks
```

### Installation des fixtures
```
Dans le dossier doc à la racine du projet :
- récupérer et dézippr le dossier uploads, puis copiez le dans public\images

- récupérer le fichier snowtricks.sql puis importer le dans votre application de gestion de base de données
```
