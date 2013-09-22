<?php
$zip = new ZipArchive;
$res = $zip->open('nibbleblog-v3.7alpha2.zip');
if ($res === TRUE) {
  $zip->extractTo('./');
  $zip->close();
  echo 'woot!';
} else {
  echo 'doh!';
}
?>