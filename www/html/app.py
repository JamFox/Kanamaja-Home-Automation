import sys
from flask import (
    Flask,
    render_template,
    url_for,
    redirect,
    request,
    session,
)

from passlib.hash import sha256_crypt
sys.path.append('/lib/python3/dist-packages')
import mysql.connector as mariadb
from uks import door


app = Flask(__name__)
app.secret_key = 'KASS'  # temp secret key for dev, change when deploying

# mariadb connection info, using read only user ruberg
conn = mariadb.connect(
    user='ruberg', password='ruberg', database='auth')


@app.route('/')
def home():
    if 'loggedin' in session:
        return redirect(url_for('dashboard'))
    else:
        return redirect(url_for('login'))


@app.route('/login', methods=['GET', 'POST'])
def login():
    msg = ''
    if request.method == 'POST' and 'username' in request.form and 'password' in request.form:
        conn.reconnect()
        username = request.form['username']
        password = request.form['password']

        cur = conn.cursor()  # conn.cursor(buffered=True)
        selectaccount = "SELECT * FROM accounts WHERE username = '" + username + "'"
        # executes database command
        cur.execute(selectaccount)
        # stores result in a list where account = [id, "username", "password"]
        account = cur.fetchone()
        verifiedOK = False
        try:
            # compares password from POST to database result
            if sha256_crypt.verify(password, account[2]):
                verifiedOK = True
        # TypeError exception for when database doesnt return anything (no entry with entered username found etc)
        except TypeError:
            msg = 'Incorrect username/password!'

        if verifiedOK:
            session['loggedin'] = True
            session['id'] = account[0]
            session['username'] = account[1]
            return redirect(url_for('dashboard'))
        else:
            msg = 'Incorrect username/password!'
    return render_template('login.html', msg=msg)


@app.route('/logout')
def logout():
    session.pop('loggedin', None)
    session.pop('id', None)
    session.pop('username', None)
    return redirect(url_for('login'))


@app.route('/register', methods=['GET', 'POST'])
def register():
    hashpw = ''
    if request.method == 'POST' and 'password' in request.form:
        hashpw = request.form['password']
        hashpw = sha256_crypt.hash(hashpw)
    return render_template('reg.html', password=hashpw)


@app.route('/dashboard')
def dashboard():
    if 'loggedin' in session:
        username = session['username']
        tempfile = open("/var/www/html/" + "tempdata.txt", "r")  # gets current temperature from database to display
        tempv = tempfile.readline()
        tempfile.close()
        f = open("/var/www/html/" + "rad3.txt", "r")
        tekstrad = f.readline()  # show last value on dashboard even after refresh
        f.close()
        f = open("/var/www/html/" + "tempideaal.txt", "r")
        threshhold = f.readline()  # show last value on dashboard even after refresh
        f.close()
        uks = door()  # gets information whether the door is open from script
        f = open("/var/www/html/" + "tuleStaatus.txt", "r")
        rida = f.readline()
        f.close()
        file1 = open("/var/www/html/" + "check.txt", "r")
        
        if rida == 'light override off':  # if light override is off, then get the line from check.txt
            rida = file1.readline()
        else:
            buf = file1.readline()  # if not put the first line into buffer which is not used later
        ajad1 = file1.readline()  # sunrise
        ajad2 = file1.readline()  # sunset

        file1.close()
        return render_template('dashboard.html', username=username, tekst=rida, ajad1=ajad1, ajad2=ajad2, tekstrad=tekstrad, tempv=tempv, threshhold=threshhold, uks=uks)
    else:
        return redirect(url_for('noaccess'))

@app.route('/radautomatic', methods=["POST"])  #route for override status
def radautomatic():
    if 'loggedin' in session:
        teksttemp = "Automatic"
        f = open("/var/www/html/" + "rad3.txt", "w")
        f.write(teksttemp)
        f.close()
        return str(teksttemp)
    else:
        return redirect(url_for('noaccess'))

@app.route('/ton', methods=["POST"])           #route for override status
def ton():
    if 'loggedin' in session:
        teksttemp = "Manual ON"
        f = open("/var/www/html/" + "rad3.txt", "w")
        f.write(teksttemp)
        f.close()
        return str(teksttemp)
    else:
        return redirect(url_for('noaccess'))

@app.route('/toff', methods=["POST"])          #route for override status
def toff():
    if 'loggedin' in session:
        teksttemp = "Manual OFF"
        f = open("/var/www/html/" + "rad3.txt", "w")
        f.write(teksttemp)
        f.close()
        return str(teksttemp)
    else:
        return redirect(url_for('noaccess'))

@app.route('/slider', methods=["POST"])       #route for temperature threshhold slider
def slider():
    if 'loggedin' in session:
        if request.method == "POST" and request.form.get('temp') is not None:
           steprange = request.form.get("temp")
           file = open("/var/www/html/" + "tempideaal.txt","w")
           file.write(steprange)
           file.close()
           return redirect(url_for('dashboard'))
        else:
            return redirect(url_for('dashboard'))
    else:
          return redirect(url_for('noaccess'))

@app.route('/automatic', methods=["POST"])   # route for light override off
def automatic():
    if 'loggedin' in session:
        tekst = "light override off"
        f = open("/var/www/html/" + "tuleStaatus.txt", "w")
        f.write(tekst)
        f.close()
        file1 = open("/var/www/html/" + "check.txt", "r")
        tekst = file1.readline()
        file1.close()
        return str(tekst)
    else:
          return redirect(url_for('noaccess'))
        
@app.route('/on', methods=["POST"])  # route for manual button on
def on():
    if 'loggedin' in session:
        #OnOrOff.off()
        tekst = "Manual ON"
        f = open("/var/www/html/" + "tuleStaatus.txt", "w")
        f.write(tekst)
        f.close()
        return str(tekst)
    else:
          return redirect(url_for('noaccess'))
        
@app.route('/off', methods=["POST"])  # route for manual button off
def off():
    if 'loggedin' in session:
        tekst = "Manual OFF"
        f = open("/var/www/html/" + "tuleStaatus.txt", "w")
        f.write(tekst)
        f.close()
        return str(tekst)
    else:
          return redirect(url_for('noaccess'))
    
@app.route('/documentation')
def documentation():
    if 'loggedin' in session:
        return render_template('documentation.html')
    else:
        return redirect(url_for('noaccess'))


@app.route('/401')
def noaccess():
    return render_template('401.html')


if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0')



