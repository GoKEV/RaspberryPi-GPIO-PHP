We want the base to display our page.  There are two ways to do this:

################################################################################
The block that starts with this <Directory objective is about line 164 in the apache2.conf.

Add the line:
        DirectoryIndex index.php

Then restart apache2.  The block should look like this when you're done:

<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride None
        DirectoryIndex index.php
        Require all granted
</Directory>





################################################################################
##	Spoon-fed command examples:
################################################################################

## To quickly go to line 164 of apache2.conf, use this command:
sudo nano +164 /etc/apache2/apache2.conf

## Restart apache:
sudo /etc/init.d/apache2 restart

## Get Apache to start on the next boot:
sudo update-rc.d apache2 enable 3 4 5
