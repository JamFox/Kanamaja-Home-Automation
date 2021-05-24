from Adafruit_CharLCD import Adafruit_CharLCD
from time import sleep, strftime
from datetime import datetime
import socket
from tempLCD import temperatuur
from vesi import main
import RPi.GPIO as GPIO

#Ukse magneti pin
GPIO.setmode(GPIO.BCM)
GPIO.setup(23, GPIO.IN, GPIO.PUD_UP)
GPIO.setup(27, GPIO.OUT)
#  Initialize LCD (must specify pinout and dimensions)
lcd = Adafruit_CharLCD(rs=26, en=19,
                       d4=13, d5=6, d6=5, d7=18,
                       cols=20, lines=4)


def ekraan():
    lcd.enable_display(True)
    lcd.clear()
    temp = temperatuur()
    vesi = main()
    lcd.message(datetime.now().strftime('%b %d  %H:%M:%S\n'))
    lcd.message("Temp: {}\n".format(temp))
    lcd.set_cursor(0,2)
    lcd.message("Vesi: ")
    lcd.message(str(vesi))
    sleep(6)
    lcd.clear()
    lcd.enable_display(False)


while True:
    try:
        if GPIO.input(23):
            GPIO.output(27,0)
            sleep(2)
            ekraan()
        else:
            lcd.clear()
            GPIO.output(27,1)
    except: continue

