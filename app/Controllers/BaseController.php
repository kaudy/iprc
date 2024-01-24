<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Smarty;


/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $smarty;
    protected $share;

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
        $this->session = \Config\Services::session();
		$this->initSmarty();
    }

	// Configs iniciais do Smarty
	private function initSmarty() {
		$configDirs = \Config\SmartyConfig::$configDirs;

		$this->smarty = new Smarty();
		$this->smarty->setTemplateDir($configDirs['templateDir']);
		$this->smarty->setCompileDir($configDirs['compileDir']);
		$this->smarty->setCacheDir($configDirs['cacheDir']);
		$this->smarty->setConfigDir($configDirs['configDir']);
		$this->smarty->addPluginsDir($configDirs['pluginsDir']);
	}

	// metodo para carregar um template
	protected function smartyView($view, array $data = []) {
		$this->smarty->assign($data);
		$this->smarty->display($this->smarty->getTemplateDir(0) . $view . '.' . \Config\SmartyConfig::$fileExtension);
	}
}
