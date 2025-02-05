<?php
namespace OCA\PDFSplitter\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\Util;

class Application extends App implements IBootstrap {
    public function __construct() {
        parent::__construct('pdfsplitter');
    }

    public function register(IRegistrationContext $context): void {
        // Register your services here
    }

    public function boot(IBootContext $context): void {
        \OCP\Util::addScript('pdfsplitter', 'pdfsplitter');
        \OCP\Util::addStyle('pdfsplitter', 'style');
    }
}
