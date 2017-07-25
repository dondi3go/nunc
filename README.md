# Nunc

# What is Nunc ?

Nunc (_now_ in latin) is a light PHP framework and CMS created for fun and education purpose.
It uses Twitter Bootstrap css framework for UI.

In its current state, nunc is single-user. Persistency is acheived using flat xml files 
(no need for a database), making nunc perfectly suited for very small spaces hosts, however,
in this perspective, nunc in obviously not very scalable.

The use of javascript is limited to a minimum for the sake of simplicity. Today,
javascript (jquery and bootstrap) is only used in the TopNavBar component.

# Installation

Download nunc on your local machine. 
Edit admin.php file to change login and password.
Upload all the files on your server
Rename _.htaccess into .htaccess
That's all

You can now adapt nunc to your needs.

# Files and folders

A brief introduction to nunc file tree
. core
. main
. more
. nuds
. ouds
. test

# Versionning

0.0.161116
- Remove Map
- Admin landing page
- Enforce HTTPS

0.0.170309
- Set php header <?php
- Handle [pic] and [row] tags in Blog

0.0.170316
- Add AuthenticationMailLogger
- Better Quote and Recipe display, better handling of pictures in Blog
- Use Btn-no-style in EditUI

0.0.170521
- Add Places and PlacesUI

0.0.170610
- Blog Pics upload/rename/delete working
- Add TopMenuBar

0.0.170708
- Start RSSReader