<?php
/**
* Class Ffprobe to scan media files
* pathFile is path to media file
*/
class FFPROBE
{
  public function rawScanFile($pathFile) {
    if (is_file($pathFile)) {
      exec("ffprobe -loglevel 0 -print_format json -show_format -show_streams ".$pathFile,$output,$result);
      return json_decode(implode($output), true);
    } else {
      return [];
    }
  }

  public function scanFile($pathFile) {
    $scan = $this->rawScanFile($pathFile);
    $tab = [];
    if (empty($scan)) {
      return $tab;
    } else {
      $tab['format'] = [];
      if (isset($scan['format']['filename'])) $tab['format']['filename'] = $scan['format']['filename'];
      if (isset($scan['format']['nb_streams'])) $tab['format']['nb_streams'] = $scan['format']['nb_streams'];
      if (isset($scan['format']['format_name'])) $tab['format']['format_name'] = $scan['format']['format_name'];
      if (isset($scan['format']['size'])) $tab['format']['size'] = $scan['format']['size'];
      if (isset($scan['format']['duration'])) $tab['format']['duration'] = $scan['format']['duration'];
      if (isset($scan['format']['tags'])) $tab['format']['tags'] = $scan['format']['tags'];

      $tab['streams'] = [];
      foreach ($scan['streams'] as $key => $stream) {
        $tab['streams'][$key] = [];
        if (isset($stream['index'])) $tab['streams'][$key]['index'] = $stream['index'];
        if (isset($stream['codec_type'])) $tab['streams'][$key]['codec_type'] = $stream['codec_type'];
        if (isset($stream['codec_name'])) $tab['streams'][$key]['codec_name'] = $stream['codec_name'];
        if (isset($stream['width'])) $tab['streams'][$key]['width'] = $stream['width'];
        if (isset($stream['height'])) $tab['streams'][$key]['height'] = $stream['height'];
        if (isset($stream['display_aspect_ratio'])) $tab['streams'][$key]['display_aspect_ratio'] = $stream['display_aspect_ratio'];
        if (isset($stream['tags'])) $tab['streams'][$key]['tags'] = $stream['tags'];
      }
      return $tab;
    }
  }
}
?>
