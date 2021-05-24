import time
import RPi.GPIO as GPIO
pin = 24 

def tempideaal():                      #function that returns the mean value from server
    f = open("/var/www/html/" + "tempideaal.txt", "r")  
    threshholdraw = f.readline()
    threshhold = int(threshholdraw)
    f.close()
    return threshhold
threshhold = tempideaal()               # give threshhold it's value before entering loop

def temp():                             #function that returns current temperature reading from "database"
        
    f = open("/var/www/html/" + "tempdata.txt", "r")
    temperatureraw = f.readline()
    f.close()
    temperature = float(temperatureraw)
    return temperature
def status():                               #function that returns status of override from "database" 
    
    f = open("/var/www/html/" + "rad3.txt", "r")
    status = f.readline()
    f.close()
    return status
staatus = status()                           #set status before loop
temperature = temp()                         #set temperature before loop

GPIO.setmode(GPIO.BCM)    #GPIO setup
GPIO.setup(pin, GPIO.OUT)
try:
     while True:   #infinate loop
                time.sleep(5)      #delay before each loop cycle
                if (temperature < threshhold + 3) and staatus == "Automatic":   # checks if temperature is low enough to heat
                    try:
                        threshhold = tempideaal()
                        GPIO.output(pin,1)
                        temperature = temp()           #update variables
                        staatus = status()
                        #print ("onauto")
                    except: continue
                if (temperature > threshhold- 3) and staatus == "Automatic":    #checks if temperature is high enough to turn off
                     try:
                        threshhold = tempideaal()
                        GPIO.output(pin,0)
                        temperature = temp()           #update variables
                        staatus = status()
                        #print ("onauto")
                     except: continue
                if staatus == "Manual ON":      #checks if user wants to manually turn on radiator
                    try:
                        GPIO.output(pin,1)
                        #print ("on")
                        staatus = status()
                    except: continue
                    
                if staatus == "Manual OFF":       #checks if user wants to manually turn off radiator
                     try:   
                        GPIO.output(pin,0)
                        #print ("off")
                        staatus = status()
                     except: continue
                    
                    
except KeyboardInterrupt:            #cleanup incase keyboard interrupt
     GPIO.cleanup()
     raise
except SystemExit:                   #cleanup incase sysexit
     GPIO.cleanup()
     raise
