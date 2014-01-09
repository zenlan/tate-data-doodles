<?php
/**
 * @arg callback
 * JSONP AJAX callback function
 *
 * @arg type
 * 1=purchased
 * 0=gifted
 *
 * @arg gender
 * F=female
 * M=male
 *
 * @arg action
 * w=write
 *
 * @example  write json file for purchased artworks by female artists
 * api_rs.php?callback=myfunc&type=1&gender=F&action=w
 *
 * @example  write json file for gifted artworks by male artists
 * api_rs.php?callback=myfunc&type=0&gender=M&action=w
 *
 * @example  fetch json for purchased artworks by female artists
 * api_rs.php?callback=myfunc&type=1&gender=F&action=w
 *
 * @example  fetch json for gifted artworks by male artists
 * api_rs.php?callback=myfunc&gender=M&type=0
 *
 * @return jsonp two arrays, one of male artists, one of female artists, with
 * data arrays comprising name pairs of year/total as x/y for a Rickshaw graph
 */

if (!isset($_REQUEST['callback']) || empty($_REQUEST['callback'])) {
  write_error_log('NO CALLBACK');
  exit(1);
}

if (!isset($_REQUEST['type'])) {
  write_error_log('NO TYPE');
  exit(1);
}

if (!isset($_REQUEST['gender']) || empty($_REQUEST['gender'])) {
  write_error_log('NO GENDER');
  exit(1);
}
define('TATE_CACHE_FILE', 'json/tate_cache_' . strtoupper($_REQUEST['gender']) . $_REQUEST['type'] . '.json');

function write_error_log($type) {
  if ($fp = @fopen('/var/tate_error.log', 'a')) {
    fwrite($fp, '[' . $_SERVER['REQUEST_TIME'] . '] !' . $type . '!' . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['HTTP_REFERER'] . ' ' . print_r($_REQUEST, 1) . "\n");
    fclose($fp);
  }
}

function write_access_log() {
  if ($fp = @fopen('/var/tate.log', 'a')) {
    fwrite($fp, '[' . $_SERVER['REQUEST_TIME'] . '] ' . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['HTTP_REFERER'] . "\n");
    fclose($fp);
  }
}

function write_cache() {
  try {
    if (file_put_contents(TATE_CACHE_FILE, json_encode1(query()))) {
      return TRUE;
    }
  } catch (Exception $e) {
    error_response($e->getMessage());
  }
  return FALSE;
}

function read_cache() {
  try {
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'w') {
      if (!write_cache()) {
        error_response('Problem writing cache');
      }
    }
    $data = file_get_contents(TATE_CACHE_FILE);
    if ($data) {
      echo jsonpWrap($data);
      exit(1);
    }
  } catch (Exception $e) {
    error_response($e->getMessage());
  }
}

function error_response($message) {
  $data = array(
    "success" => 0,
    "error" => $message,
  );
  write_error_log($message);
  echo jsonpWrap(json_encode($data));
  exit(1);
}

function json_encode1($arr) {
  array_walk_recursive($arr, function (&$item, $key) {
        if (is_string($item))
          if (mb_detect_encoding($item,"UTF-8, ISO-8859-1") != 'UTF-8') {
            $item = utf8_encode($item);
          }
          $item = mb_encode_numericentity($item, array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
      });
  return mb_decode_numericentity(json_encode($arr), array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

function jsonpWrap($json) {
  if (isset($_REQUEST['callback'])) {
    $jsonp = sprintf("%s(%s);", $_REQUEST['callback'], $json);
  }
  return $jsonp;
}

function query() {
  $mdbh = new PDO('mysql:host=localhost;dbname=tate', 'root', '');
  if (!$mdbh) {
    error_response($mdbh->errorInfo());
  }
  $result = $data = array();
  $sql = 'SELECT *
      FROM `view_acquired_by_gender`
      WHERE `gender`="' . strtoupper($_REQUEST['gender']) . '"' .
      ' AND `purchased`=' . $_REQUEST['type'];
  if ($res = $mdbh->query($sql)) {
    $data = $res->fetchAll(PDO::FETCH_ASSOC);
  }
  foreach ($data as $v) {
    $tmp = array(
      'acquired' => $v['acquired'],
      'total' => $v['total'],
      'works' => array(),
    );
    $sql = 'SELECT
      w.id AS id,
      w.thumbnailUrl AS thumb,
      w.creditLine AS credit,
      w.url,
      w.artist,
      w.title
      FROM artwork w
      LEFT JOIN artist a ON a.id = w.artistId
      WHERE w.id IN (' . $v['ids'] . ')';
    if ($res = $mdbh->query($sql)) {
      $tmp['works'] = $res->fetchAll(PDO::FETCH_ASSOC);
    }
    $result[] = $tmp;
  }
  return $result;
}

read_cache();