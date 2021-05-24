""""

Automaatsed tuled p2ikset6usu ja p2ikseloojangu aegade j2rgi.
Kasutame Pythoni Astral paketti. Selle allat6mbamiseks on esiteks vaja pip alla t6mmata, mis v6ib juba olemas olla,
kui t6mbasite Pythoni, ja seej2rel command prompti sisestage:
windows:C:\> py -m pip3 install astral
rb: sudo pip3 install astral

K6ike mida astraliga teha saab on siin lehek6ljel: https://astral.readthedocs.io/en/latest/#sun

"""

from astral import LocationInfo
from astral.sun import sun
from datetime import datetime
import pytz
import RPi.GPIO as GPIO
import time

pin = 25
set(pytz.all_timezones_set)
utc=pytz.timezone('Europe/Tallinn')

city = LocationInfo("Tallinn", "Estonia", "Europe/Tallinn", 59.436962, 24.753574)
global tekst
def seis():
   f = open("/var/www/html/" + "tuleStaatus.txt", "r")
   tuleStaatus = f.readline()
   f.close()
   return tuleStaatus

tuleStaatus = seis()
#print(tuleStaatus)
GPIO.setmode(GPIO.BCM) 
GPIO.setup(pin, GPIO.OUT)
if (tuleStaatus != "light override off") and (tuleStaatus != "Manual ON") and (tuleStaatus != "Manual OFF"):
   tuleStaatus = "light override off" 
try:
    while True:
       
        s = sun(city.observer, date=datetime.now(), tzinfo=city.timezone)
        time_now =  utc.localize(datetime.now())
        sunrise = s["sunrise"]
        sunset = s["sunset"]
        sunsetBetter = sunset.strftime("%Y-%m-%d %H:%M")
        sunriseBetter = sunrise.strftime("%Y-%m-%d %H:%M")
        ajad = (
                f'Sunrise: {sunriseBetter}\n'
                f'Sunset:  {sunsetBetter}\n'
                )
        #try:
        #checkstatus = check()
        #except: continue
        time.sleep(5)
        if tuleStaatus == "Manual ON":
            #try:
                GPIO.output(pin, True) 
                tekst = "Manual ON\n"
                tuleStaatus = seis() 
          
            #except: continue
        if tuleStaatus == "Manual OFF":
            #try:    
                GPIO.output(pin, False)
                tekst = "Manual OFF\n"
                tuleStaatus = seis()
          
            #except: continue
            
        if (time_now > sunrise) and (time_now < sunset) and (tuleStaatus == "light override off"): # am I in between sunset and dusk?
                tekst = "Automatic - Day\n"
                GPIO.output(pin, True)
                tuleStaatus = seis() 
          

        if (time_now > sunrise) and (time_now > sunset) and (tuleStaatus == "light override off"):
                tekst = "Automatic- Night\n"                                              # öö enne südaööd
                GPIO.output(pin, False)
                tuleStaatus = seis()
                
        if (time_now < sunrise) and (tuleStaatus == "light override off"):
                tekst = "Automatic - Night\n"                                              # öö peale südaööd     
                GPIO.output(pin, False)
                tuleStaatus = seis()

        f = open("/var/www/html/" + "check.txt", "w")
        f.write(tekst)
        f.write(ajad)
        f.close()
            
except KeyboardInterrupt:
     GPIO.cleanup()
     raise
except SystemExit:
     GPIO.cleanup()
     raise



