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

<img src="files/GPIO-index.png?raw=true" width="150"><br>

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



## Author

This project was created in 2019 by [Kevin Holmes](http://GoKEV.com/).


