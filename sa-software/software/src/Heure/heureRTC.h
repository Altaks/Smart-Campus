#ifndef HEURE_H
#define HEURE_H

// ATTENTION, l'utilisation de ces fonctions nécéssite l'utilisation du module RTC

void initHeureRTC();  // initialise l'heure
void setDateHeureRTC(short annee, short mois, short jour, short heure, short minute, short seconde); // initialise change la date

String getDateRTC(); // retourne la date au format "YYYY-MM-DD hh:mm:ss"

short getAnneeRTC(); // retourne le mois en tant qu'entier
short getMoisRTC(); // retourne le mois en tant qu'entier
String getMoisStringRTC(); // retourne l'heure en tant que string en français; E = {"Janvier","Fevrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Decembre"}
short getJourRTC(); // retourne le jour en tant qu'entier
String getJourSemaineRTC(); // retourne le jour en tant que string E = {"Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche"}

short getHeureRTC(); // retourne l'heure en cours en tant qu'entier
short getMinuteRTC(); // retourne la minute en cours en tant qu'entier
short getSecondeRTC(); // retourne la seconde en cours en tant qu'entier

#endif