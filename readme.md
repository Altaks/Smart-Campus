# SAE 3.1 - Développement d'une application Web

### Récupération de la stack

Pour lancer la stack, vous aurez besoin de :

- Git
- Docker engine
- Docker compose
- Avoir les ports 8000, 3306 et 9000 de libres
- Un compte ayant accès au repository

Une fois que vous vous êtes assurés d'avoir ce qu'il faut, vous pouvez lancer la commande suivante dans un répertoire :

```bash
git clone https://forge.iut-larochelle.fr/2023-2024-but-info2-a-sae34/m2/m23/but-info2-a-sae3-docker-stack.git
```

Pour pouvoir modifier/ajouter/supprimer des fichiers du dossier sfapp une fois la stack lancée, vous devez configurer la stack : vous pouvez changer de compte en indiquant votre compte utilisateur Linux dans le fichier `.env` :

> Ce changement n'est nécessaire que sur Linux !

```env
# Uniquement sous linux
# Décommenter ces valeurs

USER_NAME=<username>
USER_ID=<userid>
GROUP_NAME=<groupname>
GROUP_ID=<groupid>
```

Exemple avec un compte `altaks` et le groupe par défaut : 

```env
# Uniquement sous linux
# Décommenter ces valeurs

USER_NAME=altaks
USER_ID=1000
GROUP_NAME=altaks
GROUP_ID=1000
```

### Lancement de la stack

Une fois le repository cloné et configuré, vous pouvez lancer la stack en utilisant la commande suivante :

```bash
docker compose up --build
```

> Si vous souhaitez lancer la stack sans vous bloquer votre terminal, vous pouvez lancer la commande suivante :
> ```bash
> docker compose up --build -d
> ```
> Vous aurez alors la stack lancée en arrière plan (le terminal est détaché)

### Lancer un bash interactif avec un container

Afin de vous rendre dans un container et effectuer des changements, vous pouvez utiliser la commande suivante dans le même dossier que la stack :

```bash
docker compose exec <sfapp/database/nginx> bash
```

Exemple, pour accéder au container où se situe Symfony, on utilise : 

```bash
docker compose exec sfapp bash
```

### Réinstallation des packages (dans le container `sfapp`)

Lorsque vous utilisez la stack pour la première fois dans un répertoire, si vous ne disposez pas des dossiers `sfapp/vendor` et `sfapp/node_modules`, vous pouvez faire télécharger leur contenu à la stack avec la commande suivante :

```shell
cd /app/sfapp && composer install && npm i
```

### Mise à jour automatique des fichiers de Tailwind CSS (dans le container `sfapp`)

Afin de mettre a jour le CSS qui sera utilisé pour les fichiers twig, vous pouvez utiliser la commande suivante : 

```shell
cd /app/sfapp && npm run build
```

Cependant, si vous souhaitez que les mises à jour s'effectuent d'elles même, vous pouvez lancer cette commande dans un terminal secondaire : 

```shell
cd /app/sfapp && npm run watch
```

### Règles de collaboration

Pour collaborer sur le projet, vous devez développer en répondant à une user story.

Vous devrez créer une branche en suivant la syntaxe suivante : 

```
develop-US<release>.<numero-US>-<DescriptionUS>
```

Exemple pour l'US 1.4

```
develop-US1.4-Modifier-SA
```

### Une fois vos changements prêts

Vous pouvez émettre une merge request avec un reviewer parmis Adrien, Arnaud, Luc ou Kevin et vos changements seront revus