# PHP-Class
Some class in php that I use on some project

## Deluge Class

**To change**

- Do not forget to change the download path (line 9)

**Usage**

- Initialize an object
```php
$deluge = new DELUGE("url to server", "password");
```
- Get the torrent already on the server
```php
$torrents = $deluge->getTorrents();
```
- Launch the download of a new torrent
```php
$deluge->addTorrent($urlTorrent);
```
- Do not forget to close the connexion once you finish using it
```php
$deluge->close();
```
