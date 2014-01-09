<?php
/**
 * @arg callback
 * JSONP AJAX callback function
 *
 * @arg type
 * 1=purchased
 * 0=gifted
 *
 * @arg action
 * w=write
 *
 * @example  write json file for purchased artworks
 * api_rs.php?callback=myfunc&type=1&action=w
 *
 * @example  write json file for gifted artworks
 * api_rs.php?callback=myfunc&type=0&action=w
 *
 * @example  fetch json for purchased artworks
 * api_rs.php?callback=myfunc&type=1&action=w
 *
 * @example  fetch json for gifted artworks
 * api_rs.php?callback=myfunc&type=0
 *
 * @return jsonp two arrays, one of male artists, one of female artists, with
 * data arrays comprising name pairs of year/total as x/y for a Rickshaw graph
 */

if (!isset($_REQUEST['callback']) || empty($_REQUEST['callback'])) {
  error_response('NO CALLBACK');
  exit(1);
}

if (!isset($_REQUEST['type'])) {
  error_response('NO TYPE');
  exit(1);
}

define('TATE_CACHE_FILE', 'json/tate_cache_graph_' . $_REQUEST['type'] . '.json');

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

function error_response($message) {
  $data = array(
    "success" => 0,
    "error" => $message,
  );
  write_error_log($message);
  echo jsonpWrap(json_encode($data));
  exit(1);
}

function jsonpWrap($json) {
  if (isset($_REQUEST['callback'])) {
    $jsonp = sprintf("%s(%s);", $_REQUEST['callback'], $json);
  }
  return $jsonp;
}

function write_cache() {
  try {
    if (file_put_contents(TATE_CACHE_FILE, json_encode(query()))) {
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

function query_gender($gender) {
  $mdbh = new PDO('mysql:host=localhost;dbname=tate', 'root', '');
  if (!$mdbh) {
    error_response($mdbh->errorInfo());
  }
  $mdbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $mdbh->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
  $result = array();
  $sql = 'SELECT `acquired` AS `x`,  COUNT(`id`) AS `y`' .
      ' FROM `view_collection`' .
      ' WHERE `gender`="' . $gender . '"' .
      ' AND `acquired` > 1900' .
      ' AND `purchased`=' . $_REQUEST['type'] .
      ' GROUP BY `acquired` ORDER BY `acquired` ASC';
  try {
    $stmt = $mdbh->prepare($sql);
    if ($stmt->execute()) {
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  } catch (PDOException $e) {
    error_response($e->getMessage());
  }
  return $result;
}

function query() {
  $result = array(
    array(
      'name' => 'Female Artists',
      'data' => query_gender('F'),
    ),
    array(
      'name' => 'Male Artists',
      'data' => query_gender('M'),
    ),
  );
  return $result;
}

echo jsonpWrap(json_encode(read_cache()));
exit(1);



