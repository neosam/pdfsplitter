<?php
namespace OCA\PDFSplitter\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
    public function __construct() {
        parent::__construct('pdfsplitter');
    }
}
