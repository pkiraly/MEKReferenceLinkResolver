<?php
/**
 * PHP Unit test class for the MEK reference redirector.
 * It requires PHPUnit (https://github.com/sebastianbergmann/phpunit/)
 * 
 * Run the script:
 * $ phpunit -v --debug ResolverTest
 */

include_once('reference_redirector.functions.php');
define('MEK', 'http://mek.oszk.hu/');

class ResolverTest extends PHPUnit_Framework_TestCase {

  public function xtestGetCsvIdentifier() {
    $this->assertEquals('00H2A', get_csv_identifier(NULL, '00H2A'));
    $this->assertEquals('00E4A', get_csv_identifier(NULL, '00E4A'));
    $this->assertEquals('00K3A', get_csv_identifier(NULL, '00K3A'));
  }

  public function testGetRecordByKey() {
    // $this->assertEquals(MEK . $mekId, resolve_url($bookUrl), "Testing $bookUrl");
    /*
    $this->assertEquals(array('2/0038B', 'hu_b1_rmk-1-113a_409'),
      get_record_by_key('reference_redirector_csv/08838.csv', '2/0038B'));
    $this->assertEquals(MEK . '08800/08838/html/hu_b1_rmk-1-113a_409.html', 
      resolve_url('rmny/353/2/0038B'));
    $this->assertEquals(MEK . '03600/03614/html/093hub1_rmkI208_sH1b_sH2a.html', 
      resolve_url('rmny/540/00H2A'));
    */
    $this->assertEquals(MEK . '12700/12770/html/RMK_I_0405_0111.html', 
      resolve_url('rmk/1/0405/0087'));
  }

  public function testResolveUrl() {
    $pairs = array(
      'rmk/1/0332' => '08800/08838',
      'rmny/0080/0000' => '12200/12278/html/RMK_I_0322_0001.html',
      'ms/hu/B1/MNy/12/308' => '12900/12986/html/hu_b1_mny12_0328.html'
    );

    foreach ($pairs as $url => $mekPath) {
      $this->assertEquals(MEK . $mekPath, resolve_url($url), "Testing $url");
    }
  }

  public function testRmny() {
    $skip = array(
      'rmny/0353/2/0038B',
      'rmny/0353/2/0073A',
      'rmny/0017/0016A',
      'rmny/0017/0025A',
      'rmny/0017/0057A',
      'rmny/0017/0065A',
      'rmny/0017/0083A',
    );
    $this->assertEquals(MEK, resolve_url('rmny'));
    $this->doTestBooks('rmny', $skip);
  }

  public function testRmk() {
    $skip = array(
      'rmk/1/0210',
      'rmk/1/0332/2/0038B',
      'rmk/1/0332/2/0073A',
      'rmk/1/0007/0016A',
      'rmk/1/0007/0025A',
      'rmk/1/0007/0057A',
      'rmk/1/0007/0065A',
      'rmk/1/0007/0083A',
    );
    $this->assertEquals(MEK, resolve_url('rmk'));
    $this->doTestBooks('rmk', $skip);
  }

  public function testMs() {
    $skip = array();
    $this->assertEquals(MEK, resolve_url('ms'));
    $this->doTestBooks('ms', $skip);
  }

  public function doTestBooks($prefix, $skip = array()) {
    $rmnyLines = file('reference_redirector_csv/' . $prefix . '.csv');
    foreach ($rmnyLines as $rmnyLine) {
      if (substr($rmnyLine, 0, 1) == '#') {
        continue;
      }
      $rmnyLine = rtrim($rmnyLine);
      list($bookId, $mekId) = explode(';', $rmnyLine);
      list($mekCollId, $mekBookId) = explode('/', $mekId);
      $bookUrl = $prefix . '/' . $bookId;
      $this->assertEquals(MEK . $mekId, resolve_url($bookUrl), "Testing bookUrl: $bookUrl");

      $fileName = 'reference_redirector_csv/' . $mekBookId . '.csv';
      if (!file_exists($fileName)) {
        echo "File doesn't exist: $fileName\n";
        continue;
      }
      $pageNrs = array();
      $pageLines = file($fileName);
      foreach ($pageLines as $pageLine) {
        if (substr($pageLine, 0, 1) == '#') {
          continue;
        }
        $pageLine = rtrim($pageLine);
        if (!strpos($pageLine, ';')) {
          echo "No separator in $pageLine ($mekBookId.csv)\n";
        }
        list($pageNr, $mekPageId) = explode(';', $pageLine);
        if (isset($pageNrs[$pageNr])) {
          // echo "Repeated pageNr: $pageNr\n";
        } else {
          $pageNrs[$pageNr] = 1;
          $pageUrl = $bookUrl . '/' . $pageNr;
          if (!in_array($pageUrl, $skip)) {
            $this->assertEquals(MEK . $mekId . '/html/' . $mekPageId . '.html', 
              resolve_url($pageUrl), "Testing pageUrl: $pageUrl");
          }
        }
      }
    }
  }
}
