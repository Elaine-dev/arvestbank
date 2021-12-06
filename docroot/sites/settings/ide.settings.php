<?php

/**
 * @file
 * Enable local split if we are on an IDE environment.
 */

use Acquia\Blt\Robo\Common\EnvironmentDetector;

if (
  EnvironmentDetector::isAhEnv()
  && EnvironmentDetector::getAhEnv() == 'ide'
) {

  // Enable the local split.
  $split_filename_prefix = 'config_split.config_split';
  $split = 'local';
  $config["$split_filename_prefix.$split"]['status'] = TRUE;
}
