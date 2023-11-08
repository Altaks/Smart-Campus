# Installation de la stack

### Se rendre dans la stack : 

```shell
docker compose exec sfapp bash
```

### Se rendre dans le dossier `sfapp` : 

```shell
cd sfapp
```

### Préparation classique de la stack

```shell
# Installation du bundle twig
composer require symfony/twig-bundle

# Installation du Doctrine ORM pack
composer require symfony/orm-pack # Répondre [n]
composer require --dev symfony/maker-bundle

# Installation du package PHPUnit pour les tests unitaires
composer require --dev symfony/test-pack

# Installation du package des fixtures
composer require orm-fixtures

# Installation du package pour les formulaires
composer require symfony/form
```

### Vérifier que NodeJS et NPM sont présents dans la stack 

```shell
node --version # Doit renvoyer le numéro de version de Node (e.g v18.13.0)
npm --version # Doit renvoyer le numéro de version de NPM (e.g 9.2.0)
```

### Installation du package Webpack Encore et configuration d'un loader SaSS fonctionnel

```shell
# Installation du package Webpack encore bundle
composer require symfony/webpack-encore-bundle
npm install --force # Installation des packages nécessaires au fonctionnement de Webpack Encore
```

Modifier la configuration de Webpack (`./webpack.config.js`, décommenter l'activation du SaSS loader (L57) :

```js
// enables Sass/SCSS support
.enableSassLoader()
```

Installer le SaSS Loader 

```shell
npm install sass-loader@^13.0.0 sass --save-dev
```

La commande `npm run build` doit fonctionner après ceci

Dans le dossier `assets` : 

- Changer l'import dans `app.js` pour qu'il importe un fichier `scss` :

```js
import './styles/app.scss';
```

- Changer l'extension du fichier situé dans `assets/styles/app.css` en `assets/styles/app.scss` /!\ **ATTENTION : NE PAS LAISSER PHPSTORM RENAME LES REFERENCES AU FICHIER** (ça m'a pris 3 essais pour comprendre que ça venait de là)

### Installation de Tailwind CSS & ApexCharts

```shell
# Installation des package Tailwind CSS, PostCSS et Autoprefixer 
npm install -D tailwindcss postcss postcss-loader autoprefixer 
```

Configuration de PostCSS : 

- Creer un fichier `postcss.config.js` dans `sfapp` et copiez dedans : 

```js
module.exports = {
    plugins: {
        autoprefixer: {}
    }
}
```

- Dans la configuration de Webpack (`./webpack.config.js`), activer (rajouter) le loader PostCSS à la suite de l'activation du SaSS loader : 

```js
// enables Sass/SCSS support
.enableSassLoader()
.enablePostCssLoader()
```

- Modifier la configuration de PostCSS (`postcss.config.js`) et ajouter le plugin tailwind : 

```js
module.exports = {
    plugins: {
        tailwindcss: {},
        autoprefixer: {}
    }
}
```

Mise en place du projet tailwind : 

```shell
# Initialisation d'un projet Tailwind CSS dans le dossier courant
npx tailwindcss init -p
```

Un fichier `tailwindcss.config.js` devrait apparaître dans `sfapp`

- Configurer Tailwind pour qu'il analyse les templates twig pour créer le fichier css le plus succinct : 

```js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./templates/**/*.html.twig'],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

Dans le fichier `assets/styles/app.scss`, **remplacer tout** le contenu du fichier par : 

```scss
@import "tailwindcss/base";
@import "tailwindcss/components";
@import "tailwindcss/utilities";
```

Lancer la commande `npm run build` et vérifier que tout fonctionne correctement (pas d'erreurs).

Si la commande fonctionne correctement, exécuter la commande `npm run watch` afin de tester la mise en place d'un component Tailwind CSS

### Installation de Flowbite

Installer Flowbite via NPM

```shell
npm install flowbite
```

Configurer `tailwind.config.js` afin que Tailwind utilise le plugin flowbite

```js
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
        "./node_modules/flowbite/**/*.js"
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('flowbite/plugin')
    ],
}


```

Ajoutez ceci au fichier `assets/app.js` : 

```js
// enable the interactive UI components from Flowbite
import 'flowbite'
```

### Installation d'ApexCharts

Modifiez la configuration de tailwind : 

```js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
      "./assets/**/*.js",
      "./templates/**/*.html.twig",
      "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('flowbite/plugin')({
        charts: true,
    }),
  ],
}
```


Rajoutez ceci dans la head de `base.html.twig` : 

```shell
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
```