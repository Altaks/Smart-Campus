//
// Created by altaks on 06/12/23.
//

#ifndef SOFTWARE_QUALAIR_H
#define SOFTWARE_QUALAIR_H

#include <Arduino.h>
#include <EEPROM.h>

#include "sensirion_common.h"
#include "sgp30.h"

#include "typeDef.h"

#define LOOP_TIME_INTERVAL_MS  1000
#define BASELINE_IS_STORED_FLAG  (0x55)

void initQualAir();
void loopQualAir();

#endif //SOFTWARE_QUALAIR_H
