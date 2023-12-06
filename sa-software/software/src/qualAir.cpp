//
// Created by altaks on 06/12/23.
//

#include "qualAir.h"

/**
 * Fonction permettant de convertir 4 valeurs de 8 bits chacune en une valeur sur 32 bits
 * @param value l'adresse des 32 bits
 * @param array le tableau de 4 valeurs de 8 bits à convertir
 */
void array_to_u32(u32 *value, u8* array)
{
    (*value) = (*value) | (u32) array[0] << 24;
    (*value) = (*value) | (u32) array[1] << 16;
    (*value) = (*value) | (u32) array[2] << 8;
    (*value) = (*value) | (u32) array[3];
}

/**
 * Fonction permettant de convertir les valeurs contenues dans 32 bits en 4 valeurs de 8 bits chacune.
 * @param value l'adresse des 32 bits à convertir
 * @param array le tableau de 4 valeurs de 8 bits
 */
void u32_to_array(u32 value, u8* array)
{
    if(!array)
        return;
    array[0]=value>>24;
    array[1]=value>>16;
    array[2]=value>>8;
    array[3]=value;
}

/*
 * Reset baseline per hour, store it in EEPROM;
 */
void  store_baseline(void)
{
    static u32 i=0;
    u32 j=0;
    u32 iaq_baseline=0;
    u8 value_array[4]={0};
    i++;
    Serial.printf("Numéro de rapport : %d\n", i);
    if(i==3600)
    {
        i=0;
        if(sgp_get_iaq_baseline(&iaq_baseline)!=STATUS_OK)
        {
            Serial.println("get baseline failed!");
        }
        else
        {
            Serial.println(iaq_baseline, HEX);
            Serial.println("get baseline");
            u32_to_array(iaq_baseline,value_array);
            for(j=0;j<4;j++){
                EEPROM.write(j,value_array[j]);
                Serial.print(value_array[j]);
                Serial.println("...");
            }
            EEPROM.write(j,BASELINE_IS_STORED_FLAG);
        }
    }
    delay(LOOP_TIME_INTERVAL_MS);
}

/* Read baseline from EEPROM and set it.
 * If there is no value in EEPROM, return.
 * Another situation: When the baseline record in EEPROM is older than seven days, Discard it and return !!
 */
void set_baseline(void)
{
    u32 i=0;
    u8 baseline[5]={0};
    u32 baseline_value=0;
    for(i=0;i<5;i++)
    {
        baseline[i]= EEPROM.read(i);
        Serial.print(baseline[i],HEX);
        Serial.print("..");
    }
    Serial.println("!!!");
    if(baseline[4] != BASELINE_IS_STORED_FLAG)
    {
        Serial.println("There is no baseline value in EEPROM");
        return;
    }
    /*
    if(baseline record in EEPROM is older than seven days)
    {
     return;
     }
     */
    array_to_u32(&baseline_value,baseline);
    sgp_set_iaq_baseline(baseline_value);
    Serial.println(baseline_value,HEX);
}

void initQualAir()
{
    //Qualité de l'air
    s16 err;
    u16 scaled_ethanol_signal, scaled_h2_signal;
    Serial.begin(9600);
    Serial.println("serial start!!");

    /*
     *  Init module,Reset all baseline,The initialization takes up to around 15 seconds, during which
     *  all APIs measuring IAQ (Indoor air quality ) output will not change. Default value is 400(ppm) for co2, 0(ppb) for tvoc
     */
    while (sgp_probe() != STATUS_OK) {
        Serial.println("SGP failed");
        while(1);
    }

    /*
     * Read H2 and Ethanol signal in the way of blocking
     */
    err = sgp_measure_signals_blocking_read(&scaled_ethanol_signal,
                                            &scaled_h2_signal);
    if (err == STATUS_OK) {
        Serial.println("get ram signal!");
    } else {
        Serial.println("error reading signals");
    }
    // err = sgp_iaq_init();
    set_baseline();
}

void loopQualAir()
{
    s16 err=0;
    u16 tvoc_ppb, co2_eq_ppm;
    err = sgp_measure_iaq_blocking_read(&tvoc_ppb, &co2_eq_ppm);
    store_baseline();
    if (err == STATUS_OK) {
        co2 = int(co2_eq_ppm);
        Serial.printf("----- Qualité de l'air ------\n"
                      "CO2eq Concentration : %03d ppm\n"
                      "-----------------------------\n", co2);
    } else {
        Serial.println("error reading IAQ values\n");
        co2 = -1;
    }
}
