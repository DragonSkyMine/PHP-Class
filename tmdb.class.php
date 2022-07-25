<?php
/**
* Class Tmdb
* you can get an apikey on you're profil page on Tmdb
*/
class TMDB
{

  const LANG = "en-US";
  const API_URL = "https://api.themoviedb.org/3/";
  const IMAGE_URL = "https://image.tmdb.org/t/p/original";

  private $searchPath = 'search/tv?language='.$LANG.'&page=1&include_adult=false&api_key={$1}&query={$2}';
  private $seriesPath = 'tv/{$2}?language='.$LANG.'&api_key={$1}';
  private $seasonPath = 'tv/{$2}/season/{$3}?language='.$LANG.'&api_key={$1}';

  private $moviesPath = 'movie/{$2}?language='.$LANG.'&api_key={$1}';

  private $apiKey;

  public function __construct($apikey)
  {
    $this->apiKey = $apikey;
  }

  public function searchSerie($name)
  {
    $url = $this->replaceLink(array($this->apiKey,urlencode($name)), self::API_URL.$this->searchPath);
    $res = $this->curlRequestJson($url);
    return $res;
  }

  public function getInfosSerie($idSerie)
  {
    $url = $this->replaceLink(array($this->apiKey,$idSerie), self::API_URL.$this->seriesPath);
    $res = $this->curlRequestJson($url);
    return $res;
  }

  public function getInfosSeason($idSerie, $numSeason)
  {
    $url = $this->replaceLink(array($this->apiKey,$idSerie, $numSeason), self::API_URL.$this->seasonPath);
    $res = $this->curlRequestJson($url);
    return $res;
  }

  public function getInfosMovie($idMovie)
  {
    $url = $this->replaceLink(array($this->apiKey,$idMovie), self::API_URL.$this->moviesPath);
    $res = $this->curlRequestJson($url);
    return $res;
  }

  public function fullInfosSerie($idSerie)
  {
    $tab = [];
    $tabSeasons = [];
    $t_tab = $this->getInfosSerie($idSerie);
    $tab['id'] = $t_tab['id'];
    $tab['name'] = $t_tab['name'];
    $tab['original_name'] = $t_tab['original_name'];
    $tab['overview'] = $t_tab['overview'];
    $tab['poster_image'] = $this->getUrlImage($t_tab['poster_path']);
    $tab['backdrop_image'] = $this->getUrlImage($t_tab['backdrop_path']);
    $tab['first_air_date'] = $t_tab['first_air_date'];
    $tab['status'] = $t_tab['status'];
    $t_tabSeasons = [];
    foreach ($t_tab['seasons'] as $key => $value) {
      $tabSeasons[$value['season_number']] = [];
      $t_tabSeasons[$value['season_number']] = $this->getInfosSeason($idSerie, $value['season_number']);
      $tabSeasons[$value['season_number']] = array(
        'id' => $t_tabSeasons[$value['season_number']]['id'],
        'name' => $t_tabSeasons[$value['season_number']]['name'],
        'poster_image' => $this->getUrlImage($t_tabSeasons[$value['season_number']]['poster_path']),
        'season_number' => $t_tabSeasons[$value['season_number']]['season_number'],
        'air_date' => $t_tabSeasons[$value['season_number']]['air_date']
      );
      foreach ($t_tabSeasons[$value['season_number']]['episodes'] as $key => $episode) {
        $tabSeasons[$value['season_number']]['episodes'][$episode['episode_number']] = array(
          'id' => $episode['id'],
          'name' => $episode['name'],
          'season_number' => $episode['season_number'],
          'still_image' => $this->getUrlImage($episode['still_path']),
          'air_date' => $episode['air_date'],
          'episode_number' => $episode['episode_number']
        );
      }
    }

    $tab['seasons'] = $tabSeasons;
    return $tab;
  }

  public function fullInfosMovie($idMovie)
  {
    $tab = [];
    $t_tab = $this->getInfosMovie($idMovie);
    $tab['id'] = $t_tab['id'];
    $tab['title'] = $t_tab['title'];
    $tab['original_title'] = $t_tab['original_title'];
    $tab['overview'] = $t_tab['overview'];
    $tab['release_date'] = $t_tab['release_date'];
    $tab['backdrop_path'] = $this->getUrlImage($t_tab['backdrop_path']);
    $tab['poster_path'] = $this->getUrlImage($t_tab['poster_path']);
    return $tab;
  }

  private function getUrlImage($path)
  {
    if ($path == "") {
      return "";
    } else {
      return self::IMAGE_URL.$path;
    }
  }

  private function curlRequestJson($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return json_decode($output, true);
  }

  private function replaceLink($tab, $url) {
    $count = count($tab);
    if ($count > 0) {
      $regexTab = array();
      for ($i=1; $i <= $count; $i++) {
        $regexTab[] = "/{\\$". $i ."}/";
      }
      return preg_replace($regexTab, $tab, $url);
    } else {
      return $url;
    }
  }
}
?>
