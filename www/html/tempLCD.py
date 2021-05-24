
def temperatuur():
    f = open("/var/www/html/" + "tempdata.txt", "r")
    temperature = f.readline()
    f.close()
    return temperature
