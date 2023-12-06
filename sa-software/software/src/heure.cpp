#include <Wire.h>
#include <RtcDS1307.h>

#include "heure.h"

RtcDS1307<TwoWire> Rtc(Wire);

void initHeure()
{
    Rtc.Begin(8,9);
    Rtc.SetDateTime(RtcDateTime(2024,1,1,0,0,0));
    Rtc.SetIsRunning(true);
}

void setDateHeure(short annee, short mois, short jour, short heure, short minute, short seconde)
{
    Rtc.SetDateTime(RtcDateTime(annee,mois,jour,heure,minute,seconde));
}

String getDate()
{
    RtcDateTime maintenant = Rtc.GetDateTime();

    String mois;
    if(maintenant.Month() < 10)
    {
        mois = "0"+String(maintenant.Month());
    }
    else
    {
        mois = String(maintenant.Month());
    }

    String jour;
    if(maintenant.Day() < 10)
    {
        jour = "0" + String(maintenant.Day());
    }
    else
    {
        jour = String(maintenant.Day());
    }

    String heure;
    if(maintenant.Hour() < 10)
    {
        heure = "0" + String(maintenant.Hour());
    }
    else
    {
        heure = String(maintenant.Hour());
    }

    String minute;
    if(maintenant.Minute() < 10)
    {
        minute = "0" + String(maintenant.Minute());
    }
    else
    {
        minute = String(maintenant.Minute());
    }

    String seconde;
    if(maintenant.Second() < 10)
    {
        seconde = "0"+ String(maintenant.Second());
    }
    else
    {
        seconde = String(maintenant.Second());
    }

    String maintenantStr  = String(maintenant.Year()) + "/" + mois + "/" + jour + " " + heure + ":" + minute + ":" + seconde;
    return maintenantStr;
}

short getAnnee()
{
    return Rtc.GetDateTime().Year();
}

short getMois()
{
    return Rtc.GetDateTime().Month();
}

String getMoisString()
{
    String moisStr;
    switch(Rtc.GetDateTime().Month())
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
    return "Error : getMoisString";

}

short getJour()
{
    return Rtc.GetDateTime().Day();
}

String getJourSemaine()
{
    String jour[7] = {"Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"};
    return jour[Rtc.GetDateTime().DayOfWeek()];
}

short getHeure()
{
    return Rtc.GetDateTime().Hour();
}

short getMinute()
{
    return Rtc.GetDateTime().Minute();
}

short getSeconde()
{
    return Rtc.GetDateTime().Second();
}
