<?php
/**
* Class api Nyaa
*/
class NYAA
{
  const NYAA_URL = 'https://nyaa.si/';
  private $path = '?page=rss&c=0_0&f=0&q=%s';

  public function getListSearch($search)
  {
    sprintf($this->path, urlencode($search));
    $content = preg_replace("/nyaa:/", "nyaa", file_get_contents(self::NYAA_URL.sprintf($this->path, urlencode($search))));

    $a = new SimpleXMLElement($content);
    $tab = [];

    foreach($a->channel->item as $key => $entry) {
      $datePub = date("Y-m-d H:i:s",strtotime($entry->pubDate->__toString()));
      $idTorrent = preg_replace("/https:\/\/nyaa.si\/view\//", "", $entry->guid->__toString());;
      $tab[] = array(
        'title' => $entry->title->__toString(),
        'download' => $entry->link->__toString(),
        'page' => $entry->guid->__toString(),
        'datetime' => $datePub,
        'size' => $entry->nyaasize->__toString(),
        'hash' => $idTorrent,
        'seeds' => $entry->nyaaseeders->__toString(),
        'leechs' => $entry->nyaaleechers->__toString(),
        'category' => $entry->nyaacategory->__toString()
      );
    }

    return $tab;
  }
}

?>
