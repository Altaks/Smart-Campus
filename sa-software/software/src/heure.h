#ifndef HEURE_H
#define HEURE_H

void initHeure();  // initialise l'heure
void setDateHeure(short annee, short mois, short jour, short heure, short minute, short seconde); // initialise change la date

String getDate(); // retourne la date au format "YYYY-MM-DD hh:mm:ss"

short getAnnee(); // retourne le mois en tant qu'entier
short getMois(); // retourne le mois en tant qu'entier
String getMoisString(); // retourne l'heure en tant que string en français; E = {"Janvier","Fevrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Decembre"}
short getJour(); // retourne le jour en tant qu'entier
String getJourSemaine(); // retourne le jour en tant que string E = {"Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche"}

short getHeure(); // retourne l'heure en cours en tant qu'entier
short getMinute(); // retourne la minute en cours en tant qu'entier
short getSeconde(); // retourne la seconde en cours en tant qu'entier

#endif