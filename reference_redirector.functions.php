<?php

define('DEFAULT_URL', 'http://mek.oszk.hu/');
define('CSV_DIR', 'reference_redirector_csv');
define('LN', "\n");

/**
 * Resolve URLs based on 'Káldos-convention'
 */
function resolve_url($fullpath) {
  $parts = explode('/', $fullpath);

  switch(strtolower($parts[0])) {
    case 'rmny':
      $url = get_rmny_url($parts);
      break;
    case 'rmk':
      $url = get_rmk_url($parts);
      break;
    case 'ms':
      $url = get_ms_url($parts);
      break;
    default:
      $url = DEFAULT_URL;
  }
  return $url;
}

/**
 * Get the URL of an RMNy page
 */
function get_rmny_url($parts) {
  if (count($parts) < 2) {
    return DEFAULT_URL;
  }

  $book_id = $parts[1];
  $volume_id = '';
  $page_id = '';

  if (count($parts) == 4) {
    $volume_id = $parts[2];
    $page_id = $parts[3];
  }
  else if (count($parts) == 3) {
    $page_id = $parts[2];
  }

  while (preg_match('/^\d{1,3}\D?$/', $book_id)) {
    $book_id = '0' . $book_id;
  }

  $mek_dir = rmny2mek($book_id);

  return get_page_url($mek_dir, $volume_id, $page_id);
}

/**
 * Get the URL of an RMK page
 */
function get_rmk_url($parts) {
  if (count($parts) < 3) {
    return DEFAULT_URL;
  }

  $rmk_volume = $parts[1];
  $book_id = $parts[2];
  $volume_id = '';
  $page_id = '';

  if (count($parts) == 5) {
    $volume_id = $parts[3];
    $page_id = $parts[4];
  }
  else if (count($parts) == 4) {
    $rmk_volume = 1;
    $page_id = $parts[3];
  }

  if (strtoupper($rmk_volume) == 'I') {
    $rmk_volume = 1;
  }
  else if (strtoupper($rmk_volume) == 'II') {
    $rmk_volume = 2;
  }
  else if (strtoupper($rmk_volume) == 'III') {
    $rmk_volume = 3;
  }

  while (preg_match('/^\d{1,3}\D?$/', $book_id)) {
    $book_id = '0' . $book_id;
  }
  $mek_dir = rmk2mek($rmk_volume . '/' . $book_id);
  return get_page_url($mek_dir, $volume_id, $page_id);
}

/**
 * Get the URL of an RMK page
 */
function get_ms_url($parts) {
  if (count($parts) < 4) {
    return DEFAULT_URL;
  }

  $country = $parts[1];
  $library = $parts[2];
  $collection = '';
  $book_id = '';
  $volume_id = '';
  $page_id = '';

  if (count($parts) == 7) {
    $collection = $parts[3];
    $book_id = $parts[4];
    $volume_id = $parts[5];
    $page_id = $parts[6];
  }
  else if (count($parts) == 6) {
    $collection = $parts[3];
    $book_id = $parts[4];
    $page_id = $parts[5];
  }
  else if (count($parts) == 5) {
    $collection = $parts[3];
    $book_id = $parts[4];
  }

  if (strtoupper($volume_id) == 'I') {
    $volume_id = 1;
  }
  else if (strtoupper($volume_id) == 'II') {
    $volume_id = 2;
  }
  else if (strtoupper($volume_id) == 'III') {
    $volume_id = 3;
  }

  while (preg_match('/^\d{1,3}\D?$/', $book_id)) {
    $book_id = '0' . $book_id;
  }

  $mek_dir = ms2mek(sprintf("%s/%s/%s/%s",
    strtoupper($country), strtoupper($library), strtoupper($collection), $book_id));

  return get_page_url($mek_dir, $volume_id, $page_id);
}

/**
 * Get MEK ID by RMNy ID
 */
function rmny2mek($rmny_id) {
  $record = get_record_by_key(CSV_DIR . '/rmny.csv', $rmny_id);
  return $record[1];
}

/**
 * Get MEK ID by RMK ID
 */
function rmk2mek($rmk_id) {
  $record = get_record_by_key(CSV_DIR . '/rmk.csv', $rmk_id);
  return $record[1];
}

/**
 * Get MEK ID by MS ID
 */
function ms2mek($rmk_id) {
  $record = get_record_by_key(CSV_DIR . '/ms.csv', $rmk_id);
  return $record[1];
}

/**
 * Returns a record in CSV file based on a key.
 *
 * @param $csv_file (String)
 *   Relative path of a CSV file
 * @param $identifier (String)
 *   The key of the record we are looking for
 *
 * @return (Array or FALSE)
 *   The record as (non associative) array. If it doesn't find it, returns FALSE
 */
function get_record_by_key($csv_file, $identifier) {
  $result = FALSE;
  $handle = fopen($csv_file, "r");
  while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
    if ($data[0] == $identifier) {
      $result = $data;
      break;
    }
  }
  fclose($handle);
  return $result;
}

/**
 * Transform the volume number and page number into a canonical form, where the page
 * number is a four number and A or B letter. E.g. 1, 3 becomes 1/0003A.
 *
 * @param $volume_id (String)
 *   A volume number of an old book
 * @param $page_id (String)
 *   A page number of identifier of an old book
 *
 * @return (String or FALSE)
 *   The canonical form of the page number. It is used as key in CSV. If the page
 *   number doesn't fit into a pattern, it returns FALSE.
 */
function get_csv_identifier($volume_id, $page_id) {
  $identifier = FALSE;
  if (preg_match('/^(\d+|\d+[A-Zacfhklmnopq]\d)([rRvVaAbBC]?)$/', $page_id, $matches)) {
    $num   = $matches[1];
    $alpha = $matches[2];

    while (strlen($num) < 4) {
      $num = '0' . $num;
    }
    if (empty($alpha) && $num != '0000') {
      $alpha = 'A';
    }
    else {
      $alpha = strtoupper($alpha);
      if ($alpha == 'R') {
        $alpha = 'A';
      }
      else if ($alpha == 'V') {
        $alpha = 'B';
      }
    }
    $identifier = (isset($volume_id) && !empty($volume_id) ? $volume_id . '/' : '') . $num . $alpha;
  }
  return $identifier;
}

/**
 * Finds out the URL of a page in an old book. It uses a CSV file, which contains only
 * the referred pages, not all pages.
 *
 * @param $mek_dir (String)
 *   A relative MEK directory path, like 08800/08838
 * @param $volume_id (String)
 *   A volume number of an old book
 * @param $page_id (String)
 *   A page number of identifier of an old book
 *
 * @return (String)
 *   The URL of that page inside MEK or of the MEK home page
 */
function get_page_url($mek_dir, $volume_id, $page_id) {
  if ($mek_dir === FALSE) {
    return DEFAULT_URL;
  }
  $mek_id = substr($mek_dir, -5);

  $csv_file = CSV_DIR . '/' . $mek_id . '.csv';
  if (!file_exists($csv_file)) {
    return DEFAULT_URL . $mek_dir;
  }

  $identifier = get_csv_identifier($volume_id, $page_id);

  if ($identifier === FALSE) {
    return DEFAULT_URL . $mek_dir;
  }

  $mek_page = get_record_by_key($csv_file, $identifier);
  if (!empty($mek_page)) {
    return DEFAULT_URL . $mek_dir . '/html/' . $mek_page[1] . '.html';
  }
  else {
    return DEFAULT_URL . $mek_dir;
  }
}

