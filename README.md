Linora
=======

Linora is a link organiser and archiver, conceived by Kit Lester then simplified and enhanced by Rich Boakes - we use it for demonstrating things that are good and also things that are bad - i.e. it's developed the hard/wrong way to illustrate the evolution of web app development since '98.


Installing Linora
--

1. Upload all files in this folder and its' subfolders.
	* either to the site root directory as a whole site,
	* or to a new directory (preferably named "linora") to act as a subsite.
2. Configure the `inc/config.php` file to point to a MySQL or similar DB.
3. ...
4. Profit

Linora is started by browsing to the appropriate one of:

* http://_domain_/
* http://_domain_/_path_
* http://_domain_/_path_/linora



Known Issues
--
See [https://github.com/portsoc/linbeta/issues](https://github.com/portsoc/linbeta/issues)


Folder Configuration
--
On operating systems that provide the capability, files to be protected against direct off-site access, directories to be protected as below.

* The root folder should allow only index.html to be loaded.
* The lib folder shoudl allow all files to be read using GET only.
* All other files should be denies access except the api folder which should allow all HTTP traffic.

