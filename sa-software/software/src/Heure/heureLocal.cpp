#include <WiFi.h>

#include "time.h"
#include "heureLocal.h"

void initHeure()
{
    configTime(0,3600, "pool.ntp.org");struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        Serial.println("Connexion serveur NTP échouée");
    }
}

String getDate()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return "Date Error";
    }
    char date[19];
    strftime(date,19,"%Y-%B-%d %H:%M:%S",&timeinfo);
    return date;
}

short getAnnee()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return (short)-1;
    }
    char annee[4];
    strftime(annee,4,"%Y",&timeinfo);
    return atoi(annee);
}

short getMois()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return (short)-1;
    }
    char mois[2];
    strftime(mois,2,"%B",&timeinfo);
    return atoi(mois);
}

String getMoisString()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return "Date Error";
    }
    char mois[2];
    strftime(mois,2,"%B",&timeinfo);

    switch(atoi(mois))
    {
        case 1:
            return "Janvier";
            break;
        case 2:
            return "Fevrier";
            break;
        case 3:
            return "Mars";
            break;
        case 4:
            return "Avril";
            break;
        case 5:
            return "Mai";
            break;
        case 6:
            return "Juin";
            break;
        case 7:
            return "Juillet";
            break;
        case 8:
            return "Aout";
            break;
        case 9:
            return "Septembre";
            break;
        case 10:
            return "Octobre";
            break;
        case 11:
            return "Novembre";
            break;
        case 12:
            return "Decembre";
            break;
    }
    return "Error month out of range";
}

short getJour()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return (short)-1;
    }
    char jour[2];
    strftime(jour,2,"%d",&timeinfo);
    return atoi(jour);
}

String getJourSemaine()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return "Date Error";
    }
    char jour[10];
    strftime(jour,10,"%A",&timeinfo);
    return jour;
}

short getHeure()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return (short)-1;
    }
    char heure[2];
    strftime(heure,2,"%H",&timeinfo);
    return atoi(heure);

}

short getMinute()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return (short)-1;
    }
    char minute[2];
    strftime(minute,2,"%M",&timeinfo);
    return atoi(minute);

}

short getSeconde()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return (short)-1;
    }
    char seconde[2];
    strftime(seconde,2,"%M",&timeinfo);
    return atoi(seconde);

}