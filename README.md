[![GoKEV](http://GoKEV.com/GoKEV200.png)](http://GoKEV.com/)

<div style="position: absolute; top: 40px; left: 200px;">

# RaspberryPi-GPIO-PHP

This project is a playbook that deploys a PHP interface for GPIO functions to a Raspberry Pi.

I wanted a SIMPLE way to access (read and write) GPIO values, so I created this with absolute minimal requirements.

## Here's an example of how you could launch this role:
<pre>
ansible-playbook -i mypi.hosts installer.yml
</pre>

## With a mypi.hosts that looks as such:

<pre>
[mypi]
192.168.1.123 ansible_user=pi ansible_password=raspberry

## Change that IP to your PI and the user / pass should work with the default settings
## Please change your default Raspberry Pi passwords after running this.  Please?

</pre>

## Troubleshooting & Improvements

  - On one deployment, APT seemed to hang due to dependencies.  Adding the force option seemed to help

## Notes

  - This reads the state of GPIO0 through GPIO7, as well as writes to them when the button is clicked.


## Screen Shots

  - this is what the web interface looks like when you access the page (locally or remotely)

<img src="https://github.com/GoKEV/RaspberryPi-GPIO-PHP/blob/master/files/html/images/GPIO-indexpage.png?raw=true" width="150"><br>
  
  - Buttons can be created in different color and size
  
<img src="https://raw.githubusercontent.com/GoKEV/RaspberryPi-GPIO-PHP/master/files/buttons_off.png">
<img src="https://raw.githubusercontent.com/GoKEV/RaspberryPi-GPIO-PHP/master/files/buttons_off.png"><br>

  - the same status can be validated from the command line as so:
```
pi@raspberrypi:~ $ gpio read 0
1
pi@raspberrypi:~ $ gpio read 1
0
pi@raspberrypi:~ $ gpio read 2
1
pi@raspberrypi:~ $ 
```

## GPIO from DWAB

  - this includes a screen shot of some of the GPIO pinouts explained

![RaspberryPiPinout.png](files/RaspberryPiPinout.png?raw=true "RaspberryPiPinout.png")

The GPIO pinout apparently differs for WiringPi (the app we use to interact with the GPIO).
To see the pins on your exact board, log on to your Pi and type this:

<pre>
root@raspberrypi# gpio readall
 +-----+-----+---------+------+---+---Pi 3B+-+---+------+---------+-----+-----+
 | BCM | wPi |   Name  | Mode | V | Physical | V | Mode | Name    | wPi | BCM |
 +-----+-----+---------+------+---+----++----+---+------+---------+-----+-----+
 |     |     |    3.3v |      |   |  1 || 2  |   |      | 5v      |     |     |
 |   2 |   8 |   SDA.1 |  OUT | 1 |  3 || 4  |   |      | 5v      |     |     |
 |   3 |   9 |   SCL.1 |  OUT | 1 |  5 || 6  |   |      | 0v      |     |     |
 |   4 |   7 | GPIO. 7 |  OUT | 0 |  7 || 8  | 0 | IN   | TxD     | 15  | 14  |
 |     |     |      0v |      |   |  9 || 10 | 1 | IN   | RxD     | 16  | 15  |
 |  17 |   0 | GPIO. 0 |  OUT | 0 | 11 || 12 | 0 | OUT  | GPIO. 1 | 1   | 18  |
 |  27 |   2 | GPIO. 2 |  OUT | 0 | 13 || 14 |   |      | 0v      |     |     |
 |  22 |   3 | GPIO. 3 |  OUT | 1 | 15 || 16 | 0 | OUT  | GPIO. 4 | 4   | 23  |
 |     |     |    3.3v |      |   | 17 || 18 | 0 | OUT  | GPIO. 5 | 5   | 24  |
 |  10 |  12 |    MOSI |   IN | 0 | 19 || 20 |   |      | 0v      |     |     |
 |   9 |  13 |    MISO |   IN | 0 | 21 || 22 | 0 | OUT  | GPIO. 6 | 6   | 25  |
 |  11 |  14 |    SCLK |   IN | 0 | 23 || 24 | 1 | IN   | CE0     | 10  | 8   |
 |     |     |      0v |      |   | 25 || 26 | 1 | IN   | CE1     | 11  | 7   |
 |   0 |  30 |   SDA.0 |  OUT | 1 | 27 || 28 | 0 | OUT  | SCL.0   | 31  | 1   |
 |   5 |  21 | GPIO.21 |   IN | 1 | 29 || 30 |   |      | 0v      |     |     |
 |   6 |  22 | GPIO.22 |   IN | 1 | 31 || 32 | 0 | IN   | GPIO.26 | 26  | 12  |
 |  13 |  23 | GPIO.23 |   IN | 0 | 33 || 34 |   |      | 0v      |     |     |
 |  19 |  24 | GPIO.24 |   IN | 0 | 35 || 36 | 0 | IN   | GPIO.27 | 27  | 16  |
 |  26 |  25 | GPIO.25 |   IN | 0 | 37 || 38 | 0 | IN   | GPIO.28 | 28  | 20  |
 |     |     |      0v |      |   | 39 || 40 | 0 | IN   | GPIO.29 | 29  | 21  |
 +-----+-----+---------+------+---+----++----+---+------+---------+-----+-----+
 | BCM | wPi |   Name  | Mode | V | Physical | V | Mode | Name    | wPi | BCM |
 +-----+-----+---------+------+---+---Pi 3B+-+---+------+---------+-----+-----+

As we see above, Physical pin 11 is GPIO0.  This matches the graphic above.
Many other graphics indicate a different pinout, but this is what works on my Pi 3B+ and WiringPi

</pre>



## WiringPi deprecation - don't email Gordon to support this.

After reading Gordon's self-proclaimed rant http://wiringpi.com/wiringpi-deprecated/ I pulled the source into this repo and decided to distribute the last version I could find.  I am doing my best to honor Gordon and GPL -- I didn't remove anything as far as credit, contact info etc.  Please don't call Gordon for support.  Read his rant and you'll understand why.  WiringPi is awesome and I hope everyone is happy with the way I've integrated it here.


## Project origin and major milestones:

This project was created in 2016 by [Kevin Holmes](http://GoKEV.com/), based on the desire for a simple GPIO interface pane.  It has evolved slightly to adapt for platform changes, but is still the same basic interface it started out to be.

- 2022-03-17  I added the functionality for momentary buttons.  They can be configured in the index.php as latching or momentary with a user-defined delay in seconds.  The blue LED doesn't change from the default value.  I'll fix that some day.
- 2022-03-16  Read above, "WiringPi deprecation - don't email Gordon to support this."
- 2019-03-29  Ansible playbook installer allows complete automated setup with a new Pi
- 2019-01-01  Initial working interface with GPIO
- 2016-04-03  Initial PHP button interface
