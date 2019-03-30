We want the base to display our page.  There are two ways to do this:

################################################################################
##	The block that starts with this <Directory objective is about
##	line 164 in the file /etc/apache2/apache2.conf

##	Add the line:

        DirectoryIndex index.php

##	The block should look like this when you're done:

<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride None
        DirectoryIndex index.php
        Require all granted
</Directory>


##	Then restart apache with /etc/init.d/apache2 restart


################################################################################
##	Spoon-fed command examples:
################################################################################

## install the necessary packages
apt-get install -y apache2 php wiringpi

## To quickly go to line 164 of apache2.conf, use this command:
sudo nano +164 /etc/apache2/apache2.conf

## Restart apache:
sudo /etc/init.d/apache2 restart

## Get Apache to start on the next boot:
sudo update-rc.d apache2 enable 3 4 5

## Move this page and graphics to /var/www/html/
mv index.php /var/www/html/
mv on.png /var/www/html/
mv off.png /var/www/html/




################################################################################
##	To fully purge the old packages, try these on uninstall.
##	I removed apach2 and php to run the playbook again and
##	deleted my modified config files -- they were not automatically
##	recreated by apt until I purged using these commands:
################################################################################
sudo apt-get purge apache2*
sudo apt-get purge php*

