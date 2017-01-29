ttrss-sendtokindle
==================

Tiny Tiny RSS Plugin to send articles to your kindle with a single click. Tested with Tiny Tiny RSS v17.1 (f45a115).

Installation
------------
* Goto your plugins directory and clone this git repository with following command:  
`git clone https://github.com/usr42/ttrss-sendtokindle kindle`  
or download this repository as zip: https://github.com/usr42/ttrss-sendtokindle/archive/master.zip and unzip it into your plugin directory and rename the folder to 'kindle'
* Go to your tt-rss Preference page
* Under Plugins section enable the kindle plugin
* Configure your Kindle mail address at "Personal data / Authentication" preferences.
* Go to your "My Kindle" site (e.g.  https://www.amazon.com/myk or  https://www.amazon.de/myk)
* Add "kindle@darkhelmetlive.com" to your Kindle Approved Email List  because the plugin uses the service of tinderizer.com to send the website to your kindle.
![Add "kindle@darkhelmetlive.com" to your Kindle Approved Email List](KindleApprovedEmailList.png "Kindle Approved Email List")

Usage
-----
* Click on The Kindle Symbol ![Kindle Symbol] (kindle.png "Kindle Symbol") to send the current article to your Kindle
OR
* Use the hotkey **Shift-K** to send the current article to your Kindle

Version history
---------------
* **0.1** Initial version
* **0.2** Added hotkey Shift-K to send current article to kindle

Thanks to
---------
* http://tinderizer.com/ : Go to his website and donate if you like his work!
* Tiny Tiny RSS http://tt-rss.org
