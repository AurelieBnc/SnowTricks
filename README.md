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

DATABASE_URL="mysql://user_db:password_db@127.0.0.1:3306/snowtricks?serverVersion=8.0.34"


```

### Déplacer le terminal dans le dossier cloné
```
cd snowtricks
```
