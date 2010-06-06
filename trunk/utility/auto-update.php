<?php

function fetch_data() {
  $ch = curl_init("http://ip-to-country.webhosting.info/downloads/ip-to-country.csv.zip");
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_POST, FALSE);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
  curl_setopt($ch, CURLOPT_TIMEOUT, 600);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
  $fileContent = curl_exec($ch);
  if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
    file_put_contents('ip-to-country.csv.gz', $fileContent);
  }
  curl_close($ch);
}

function decompress_data() {
  $cmd = 'gzip -d ip-to-country.csv.gz';
  system($cmd);
}

function convert_data() {
  $input_file = 'ip-to-country.csv';
  $output_file = '../is_taiwan_ip_new.php';
  if (!$ifp = fopen($input_file, 'rb')) {
    echo "Can not read the file!\n";
    return FALSE;
  }
  flock($ifp, LOCK_SH);

  $twip = array();
  while ($line = fgets($ifp, 10240)) {
    //"978714624","978780159","TW","TWN","TAIWAN"
    $data = explode(',', str_replace('"', '', $line));
    if (isset($data[2]) && $data[2] === 'TW') {
      $twip[] = '($longip>='.$data[0].' AND $longip<='.$data[1].')';
    }
  }
  if (count($twip) > 0) {
    $file_head = '<?php
/*
 * echo is_taiwan_ip(\'140.113.1.1\');      //TRUE
 *
 * @DataSource    http://ip-to-country.webhosting.info/
 * @LastUpdated   '.date('Y-m-d').'
 */
function is_taiwan_ip($ip) {
  $longip = sprintf("%u", ip2long($ip));
  if (';

    $file_foot = '
  )
    return TRUE;
  else
    return FALSE;
}
?>';

    $or = '
    OR ';

    file_put_contents($output_file, $file_head. join($or, $twip). $file_foot);
  }

  flock($ifp, LOCK_UN);
  fclose($ifp);
}

fetch_data();
decompress_data();
convert_data();

?>