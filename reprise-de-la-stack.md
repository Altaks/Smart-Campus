# Reprise de la stack

> Les commandes qui vont suivre sont à lancer dans le container `sfapp` de la stack, connectés avec votre bon compte, pas root.

### Réinstallation des packages

Lorsque vous utilisez la stack pour la première fois dans un répertoire, si vous ne disposez pas des dossiers `sfapp/vendor` et `sfapp/nodeèmodules`, vous pouvez faire télécharger leur contenu à la stack avec la commande suivante :

```shell
cd /app/sfapp && composer install && npm i
```

### Mise à jour automatique des fichiers de Tailwind CSS

Afin de mettre a jour le CSS qui sera utilisé pour les fichiers twig, vous pouvez utiliser la commande suivante : 

```shell
cd /app/sfapp && npm run build
```

Cependant, si vous souhaitez que les mises à jour s'effectuent d'elles même, vous pouvez lancer cette commande dans un terminal secondaire : 

```shell
cd /app/sfapp && npm run watch
```