#include <WiFi.h>

#include "time.h"
#include "heureLocal.h"

void initHeure()
{
    configTzTime("CET-1CEST-2,M3.5.0/02:00:00,M10.5.0/03:00:00","0.europe.pool.ntp.org");
    //configTime(0,3600, "pool.ntp.org");
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        Serial.println("Connexion serveur NTP échouée");
    }
}

bool dateEstInitilaisee()
{
    struct tm timeinfo;
    return getLocalTime(&timeinfo, 0);
}

String getDate()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return "Date Error";
    }
    char date[20];
    strftime(date,20,"%Y-%m-%d %H:%M:%S",&timeinfo);
    return date;
}

short getAnnee()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return (short)-1;
    }
    char annee[5];
    strftime(annee,5,"%Y",&timeinfo);
    return atoi(annee);
}

short getMois()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return (short)-1;
    }
    char mois[3];
    strftime(mois,3,"%m",&timeinfo);
    return atoi(mois);
}

String getMoisString()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return "Date Error";
    }
    char mois[3];
    strftime(mois,3,"%m",&timeinfo);

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
    char jour[3];
    strftime(jour,3,"%d",&timeinfo);
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
    char heure[3];
    strftime(heure,3,"%H",&timeinfo);
    return atoi(heure);

}

short getMinute()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return (short)-1;
    }
    char minute[3];
    strftime(minute,3,"%M",&timeinfo);
    return atoi(minute);

}

short getSeconde()
{
    struct tm timeinfo;
    if(!getLocalTime(&timeinfo, 0))
    {
        return (short)-1;
    }
    char seconde[3];
    strftime(seconde,3,"%M",&timeinfo);
    return atoi(seconde);

}