import RPi.GPIO as GPIO


GPIO.setmode(GPIO.BOARD)
GPIO.setup(16, GPIO.IN, GPIO.PUD_UP)

def door():
    if GPIO.input(16):
        return "open"
    else:
        return "closed"
        
