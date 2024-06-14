# Leeco

[![CI](https://github.com/mmarchois/leeco/actions/workflows/ci.yml/badge.svg)](https://github.com/mmarchois/leeco/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/mmarchois/leeco/graph/badge.svg?token=6CID087H72)](https://codecov.io/gh/mmarchois/leeco)

## Environnement technique

- [Docker](https://www.docker.com/) / [Compose](https://docs.docker.com/compose/)
- [PHP](https://www.php.net/)
- [Symfony](https://www.symfony.com/)
- [Twig](https://twig.symfony.com/)
- [Turbo](https://turbo.hotwired.dev/) / [Stimulus](https://stimulus.hotwired.dev/)
- [PostgreSQL](https://www.postgresql.org/)
- [Redis](https://redis.io/)
- [Sendinblue](https://brevo.com)
- [AWS S3](https://aws.amazon.com/fr/s3/)

## Démarrage du projet

ℹ️ Vous devez avoir **[Docker](https://www.docker.com/)** et **[Docker Compose](https://docs.docker.com/compose/)** d'installés sur votre machine.

Pour installer l'application, executez la commande suite :

```bash
make install
```

ou lancez la commande suivante si le projet avait déjà été installé :

```bash
make start
```

## Applications

- Web : http://leeco.localhost:8000
- Mail catcher : http://leeco.localhost:1085
- Mobile : https://github.com/mmarchois/leeco_app
