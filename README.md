# PHP-Class
Some class in php that I use on some project

## Deluge Class ##
### [deluge.class.php](https://github.com/DragonSkyMine/PHP-Class/blob/main/deluge.class.php) ###

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

## Tmdb Class ##
### [tmdb.class.php](https://github.com/DragonSkyMine/PHP-Class/blob/main/tmdb.class.php) ###

**To change**

- Do not forget to change the lang you want the api to be call with (line 9)

**Usage**

- Initialize an object (with your TMDB api key)
```php
$tmdb = new TMDB($apiKey);
```

- You can get all the infos about a movie with the id TMDB of the movie
```php
$infosMovie = $tmdb->fullInfosMovie($movie);
```

- You can get all the infos about a serie with the id TMDB of the series
```php
$infosSerie = $tmdb->fullInfosSerie($series);
```

## Free Class ##
### [free.class.php](https://github.com/DragonSkyMine/PHP-Class/blob/main/free.class.php) ###

**Usage**

- Get your login mobile.free.fr and your api key and initialize an object
```php
$free = new FREE($freeUser, $freeApiKey);
```

- Send SMS to yourself
```php
$free->sendMessage("Hello world");
```

## Nyaa Class ##
### [nyaa.class.php](https://github.com/DragonSkyMine/PHP-Class/blob/main/nyaa.class.php) ###

**Usage**

- initialize an object
```php
$nyaa = new NYAA();
```

- Get the result of a research
```php
$result = $nyaa->getListSearch($request);
```

## Ffprobe Class ##
### [ffprobe.class.php](https://github.com/DragonSkyMine/PHP-Class/blob/main/ffprobe.class.php) ###

**Important**

 You need to be able to execute 'exec()' command in PHP and to have ffprobe installed on your server

**Usage**

- initialize an object
```php
$ffprobe = new FFPROBE();
```

- Get a raw scan of a media file
```php
$ffprobe->rawScanFile($pathfile);
```

- Get a cleaned scan of a media file
```php
$ffprobe->scanFile($pathfile);
```
