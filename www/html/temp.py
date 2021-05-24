import time
import Adafruit_DHT
sensor = Adafruit_DHT.DHT11
pin = 22
def temperatuur():              #function to determine current temperature and save it in a "database"
    humidity, temperature = Adafruit_DHT.read_retry(sensor, pin)
    
    if temperature is None:
         humidity, temperature = Adafruit_DHT.read_retry(sensor, pin)
    if temperature is not None:
        temperature = str(temperature)
        file = open("/var/www/html/" + "tempdata.txt","w")
        file.write(temperature)
        #print(temperature)
        file.close()
        time.sleep(30)
        

while True:         #runs on a loop and updates database with delay to save hardware
    try:
        temperatuur()
    except:
        continue
    

