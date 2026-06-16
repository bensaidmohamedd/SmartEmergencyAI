<?php

$tmpDir = dirname(__DIR__).DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'tmp';

if (! is_dir($tmpDir)) {
    mkdir($tmpDir, 0755, true);
}

ini_set('upload_tmp_dir', $tmpDir);
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '25M');
