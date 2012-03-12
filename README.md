# Virtual-Assistant

A small web application to retrieve a list of employees (using MySQL or LDAP) that will send them a message through XMPP/Jabber (if they're online) or email (if they're not). 

## To install

1. Edit the config.sample.php and rename it to config.php
2. Rename "public" to your public folder (usually public_html or www)
3. Point a virtual host to the public directory.

## Things to note:

This web application has only been tested using ejabberd. It should work in any other XMPP daemon, just make sure discovery services are enabled and that you're logging into an administrator Jabber/XMPP account (the XMPP class is pretty much read-only from an administrative point-of-view).