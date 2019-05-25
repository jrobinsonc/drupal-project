<?php

namespace DrupalProject\composer;

/**
 * Site installer.
 *
 * @package DrupalProject\composer
 * @author Jose Robinson <hi@joserobinson.com>
 * @copyright 2019 Jose Robinson
 * @license GPL-2.0
 * @since 1.0.0
 */
class SiteInstaller extends AbstractSiteInstaller {

  /**
   * Entrypoint for the installer.
   */
  public function __construct() {
    $this->checkRequiredDirs([], 0755);
    $this->generateSettings();
    // $this->customTask();
  }

  /**
   * Custom task example you can use to create new ones.
   */
  protected function customTask() {
    $log = $this->log('My custom task.');
    $errors = [];

    if (TRUE) {
      $errors[] = 'There was an error.';
    }

    $log->done($errors);
  }

}
