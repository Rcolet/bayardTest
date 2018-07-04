BayardTest
==========

A Symfony project created on January 2, 2018, 4:14 pm.

## Installation

Pour lancer l'application, [installé Docker](https://docs.docker.com/install/) qui nous permettra de mettre en place l'environnement adéquate. Faite aussi une copie de `.env.dist` en `.env` en complètant les différentes variables. Puis, suivez les instructions qui nous conviens le mieux (Make ou Manuelle)

###Avec Make

Pour lancé l'installation de l'environnement et du projet, lancé la commande 

```shell
make
````

Si vous voulez la partie sélénium du projet, mettez en argument `ARG=selenium`à la suivante de cette commande.

Compléter les différentes instructions qu'il vous donnera. Et voilà!!!

###Manuelle

Lancer la commande suivante qui vous permettra de construire les différents environnements que votre projet aura besoin.

```shell
> docker-compose up -d --build --force-recreate --scale selenium=0
````

`--scale selenium=0`permet de ne pas lancer la partie 'selenium' du projet. Si vous voulez l\'utiliser, enlevez cette partie de la commande.

Après la finalisation de la création des container, lancer les commande suivante qui permettront de mettre en place la base de données.

```shell
docker-compose exec php gzip --dkf data/sql/*.sql.gz
docker-compose exec -T mysql mysql -h 127.0.0.1 -P <DATABASE_HOST_PORT> -u <DATABASE_USER> -p<DATABASE_PASSWORD> -D <DATABASE_NAME> < data/sql/*.sql
rm data/sql/bayard_test.sql
````

Enfin, installé les différents paquets dont le projet à besoin :

```sh
docker-compose exec php composer install
```

##Information utils

Voici la liste des utilisateurs présents en base de données.

| id | username  | password  | salt | roles                                                    |
|----|-----------|-----------|------|----------------------------------------------------------|
| 1  | Alexandre | Alexandre |      | a:1:{i:0;s:9:"ROLE_AUTEUR";}                             |
| 2  | Marine    | Marine    |      | a:1:{i:0;s:9:"ROLE_USER";}                               |
| 3  | Anna      | Anna      |      | a:1:{i:0;s:9:"ROLE_MODERATEUR";}                         |
| 4  | rcolet    | rcolet    |      | a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}                       |
| 5  | remi      | remi      |      | a:2:{i:0;s:10:"ROLE_ADMIN";i:1;s:16:"ROLE_SUPER_ADMIN";} |
| 6  | banane    | banane    |      | a:1:{i:0;s:15:"ROLE_MODERATEUR";}                        |

La route pour se connecter avec un utilisateur est `\login`
La route pour se créer un utilisateur `\registrer`