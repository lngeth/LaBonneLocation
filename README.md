# LaBonneLocation

Site d'achats et de vente de voiture

## Installer le projet  
Utiliser votre IDE pour lancer le projet à la racine = Répertoire **/projet_web**   
Lancer la commande suivante pour lancer le serveur.  
```shell
symfony server:start
```

## Commande à lancer pour mettre en place la BD  

Ouvrir une invite de commande et lancer les commandes suivantes :  
```shell
php bin/console doctrine:query:sql "DROP TABLE IF EXISTS billing, car, user"  
php bin/console doctrine:migrations:migrate  
php bin/console doctrine:schema:update  --force  
php bin/console doctrine:fixtures:load
```

## Login pour accéder aux services  
- **Admin**  
  * email : laurent.dupont@gmail.fr  
  - mdp : ilie123

- **Loueur**  
  - email : le_roy999@yahoo.fr  
  - mdp : Le1357911

- **Client**  
  - email : savory.dupont@gmail.fr  
  - mdp : savsav40

## Auteurs
Ibrahime Ahbib  
Laurent Ngeth  
Yacine Bettayeb  
