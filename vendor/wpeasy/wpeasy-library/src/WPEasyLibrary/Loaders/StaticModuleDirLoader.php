<?php
/**
 * Utilities to load modules and initialise them
 * Modules must be included in PSR4 Autoloading
 * Modules must implement WPEasyLibrary\Interfaces\IStaticInitialiser
 */

namespace WPEasyLibrary\Loaders;
use WPEasyLibrary\Exceptions\ModuleNotExistException;
use WPEasyLibrary\Interfaces\IStaticInitialiser;


class StaticModuleDirLoader
{

    public $loadedModules = [];
    private $_moduleDir;
    private $_rootClassName;
    private $_frontControllerName;
    private $_moduleClassRoot;

    public function __construct( $moduleDir, $rootClassName, $frontControllerName = '\Controller\ModuleController' )
    {
        $this->_moduleDir = $moduleDir;
        $this->_rootClassName = $rootClassName;
        $this->_frontControllerName = $frontControllerName;
        $this->_moduleClassRoot = $rootClassName . '\\' . trim(explode($rootClassName, $moduleDir)[1], '/\\');
    }

    /*
     * Loads all modules and initialises them.
     * Does a simple directory scan, so order cannot be specified.
     */
    public function initAllModules()
    {
        $modules = scandir($this->_moduleDir);

        foreach($modules as $module)
        {
            if(in_array($module, ['.','..'])) continue;

            /**@var $class IStaticInitialiser */
            $this->initModule($module);
        }
    }

    /**
     * Only init specified modules
     * @param $moduleArray
     * @throws ModuleNotExistException
     */
    public function initSpecificModules($moduleArray)
    {
        foreach($moduleArray as $module)
        {
            $this->initModule($module);
        }
    }


    /**
     * Initialises one Module
     * @param $module
     * @throws ModuleNotExistException
     */
    public function initModule($module)
    {
        /**@var $class IStaticInitialiser */
        $class = $this->_moduleClassRoot. '\\' . $module . $this->_frontControllerName;
        if(!class_exists($class))
            throw new ModuleNotExistException("Module: $module controller does not exist");

        $class::init();
        $info = new \stdClass();
        isset($class::$name) && $info->name = $class::$name;
        isset($class::$description) && $info->description = $class::$description;
        $this->loadedModules[$module] = $info;
    }
}