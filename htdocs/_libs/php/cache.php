<?php

abstract class BlockstrapCacheEngine {

    //holds the settings for this engine
    public $settings = array();
    //all keys are prefixed by this
    protected $key_prefix = null;

    public function init($settings = array()) {
        $settings = array_merge(
                $this->settings, array(
            'key_prefix' => 'blockstrap_', //all keys prefixed by this
            'cache_duration' => 3600, //1 hour, in seconds
            'purge_probability' => 1, //1% chance of cleaning
            'suppress_errors' => True
                ), $settings);
        $this->settings = $settings;
        //Duration can also be passed in as string e.g. 1 week so convert to seconds
        if (!is_numeric($this->settings['cache_duration'])) {
            $this->settings['cache_duration'] = strtotime($this->settings['cache_duration']) - time();
        }
        return true;
    }

    /**
     * Write value for the given key into cache , to be valid for $cache_duration seconds
     *
     * @param string $key Identifier for the data
     * @param mixed $value Data to be cached
     * @param integer $duration How long to cache for.
     * @return boolean True if the data was successfully cached, false on failure
     */
    abstract public function write($key, $value, $cache_duration);

    /**
     * Read a key from the cache
     *
     * @param string $key Identifier for the data
     * @return mixed The data, or False
     */
    abstract public function read($key);

    /**
     * Remove a key and it's data from the cache
     *
     * @param string $key Identifier for the data
     * @return boolean True on success or non-existent, false on failure
     */
    abstract public function purge($key);

    /**
     * Forces removal of expired content
     * @param integer $expires [optional] time() if not supplied
     * @return void
     */
    public function purgeExpired($expires = null) {
        
    }

    /**
     * Clear *all* the keys from the cache
     *
     * @param boolean $check if true will check expiration, otherwise delete all
     * @return boolean True on sucess
     */
    abstract public function purgeAll();

    /**
     * Returns the Cache Engine's settings
     *
     * @return array settings
     */
    public function getSettings() {
        return $this->settings;
    }

}

class BlockstrapFileEngine extends BlockstrapCacheEngine {

    /**
     * The file used to store the data
     * @var SplFileObject
     */
    protected $file = null;
    protected $is_init = true;

    public function init($settings = array()) {
        $settings = array_merge(array(
            'engine' => 'BlockstrapFileEngine',
            'path' => '/tmp',
            'key_prefix' => 'bs_',
            'lock' => true,
            'serialize' => true,
            'is_windows' => false,
            'mask' => 0664
                ), $settings
        );
        parent::init($settings);
        //Windows needs differnt line ends
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->settings['is_windows'] = true;
        }
        //need trailing slash on path
        if (substr($this->settings['path'], -1) !== DIRECTORY_SEPARATOR) {
            $this->settings['path'] .= DIRECTORY_SEPARATOR;
        }
        return $this->_dirIsOk();
    }

    public function write($key, $data, $cache_duration) {

        if ($data === '' || is_null($data) || !$this->is_init) {
            return false;
        }
        if ($this->_openFile($key, true) === false) {
            return false;
        }
        $line_break = "\n";

        if ($this->settings['is_windows']) {
            $lineBreak = "\r\n";
        }

        if (!empty($this->settings['serialize'])) {
            if ($this->settings['is_windows']) {
                $data = str_replace('\\', '\\\\\\\\', json_encode($data));
            } else {
                $data = json_encode($data);
            }
        }
        $expires = time() + $cache_duration;
        $contents = $expires . $line_break . $data . $line_break;

        if ($this->settings['lock']) {
            $this->file->flock(LOCK_EX);
        }

        $this->file->rewind();
        $success = $this->file->ftruncate(0) && $this->file->fwrite($contents) && $this->file->fflush();

        if ($this->settings['lock']) {
            $this->file->flock(LOCK_UN);
        }

        return $success;
    }

    public function read($key) {
        if (!$this->is_init || $this->_openFile($key) === false) {
            return false;
        }
        if ($this->settings['lock']) {
            $this->file->flock(LOCK_SH);
        }

        $this->file->rewind();
        $time = time();
        $cachetime = intval($this->file->current());

        //check for expiry
        if ($cachetime !== false && ($cachetime < $time || ($time + $this->settings['cache_duration']) < $cachetime)) {
            if ($this->settings['lock']) {
                $this->file->flock(LOCK_UN);
            }
            return false;
        }
        $data = '';
        $this->file->next();
        while ($this->file->valid()) {
            $data .= $this->file->current();
            $this->file->next();
        }

        if ($this->settings['lock']) {
            $this->file->flock(LOCK_UN);
        }

        $data = trim($data);

        if ($data !== '' && !empty($this->settings['serialize'])) {
            if ($this->settings['is_windows']) {
                $data = str_replace('\\\\\\\\', '\\', $data);
            }
            $data = json_decode((string) $data, TRUE);
        }

        return $data;
    }

    public function purge($key) {
        if (!$this->is_init) {
            return false;
        }
        if ($this->_openFile($key) === false) {
            return true;
        }
        $path = $this->file->getRealPath();
        $this->file = null;
        return unlink($path);
    }

    public function purgeExpired($expires = True) {
        $this->purgeAll($expires);
    }

    public function purgeAll($check_expiry = false) {
        if (!$this->is_init) {
            return false;
        }
        $dir = dir($this->settings['path']);
        if ($check_expiry) {
            if (is_numeric($check_expiry)) {
                $now = $check_expiry;
            } else {
                $now = time();
            }
            $threshold = $now - $this->settings['cache_duration'];
        }
        $prefixLength = strlen($this->settings['key_prefix']);
        while (($entry = $dir->read()) !== false) {
            if (substr($entry, 0, $prefixLength) !== $this->settings['key_prefix']) {
                continue;
            }
            if ($this->_openFile($entry) === false) {
                continue;
            }
            if ($check_expiry) {
                $mtime = $this->file->getMTime();

                if ($mtime > $threshold) {
                    continue;
                }

                $expires = (int) $this->file->current();

                if ($expires > $now) {
                    continue;
                }
            }
            $path = $this->file->getRealPath();
            $this->file = null;
            if (file_exists($path)) {
                unlink($path);
            }
        }
        $dir->close();
        return true;
    }

    protected function _dirIsOk() {
        $dir = new SplFileInfo($this->settings['path']);
        if ($this->is_init && !($dir->isDir() && $dir->isWritable())) {
            $this->is_init = false;
            if (!$this->settings['suppress_errors']) {
                trigger_error($this->settings['path'] . ' is not writable', E_USER_WARNING);
            }
            return false;
        }
        return true;
    }

    protected function _openFile($key, $createKey = false) {

        $dir = $this->settings['path'];
        //create dir if needed
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = new SplFileInfo($dir . $key);

        if (!$createKey && !$path->isFile()) {
            return false;
        }
        if (empty($this->file) || $this->file->getBaseName() !== $key) {
            $exists = file_exists($path->getPathname());
            try {
                $this->file = $path->openFile('c+');
            } catch (Exception $e) {
                if (!$this->settings['suppress_errors']) {
                    trigger_error($e->getMessage(), E_USER_WARNING);
                }
                return false;
            }
            unset($path);

            if (!$exists && !chmod($this->file->getPathname(), (int) $this->settings['mask'])) {
                if (!$this->settings['suppress_errors']) {
                    trigger_error('Could not apply permission mask  on cache file ' . $this->file->getPathname(), E_USER_WARNING);
                }
            }
        }
        return true;
    }

}

class BlockstrapCache {

    /**
     * Configuration settings for the overall cache.
     * @var array
     */
    protected static $config = array();

    /**
     * Array of CacheEngines with name as key
     * @var array
     */
    protected static $engines = array();

    /**
     * Create or Update a cache instance
     * `Cache::config('instance_name', array('engine' => 'File', 'path' => TMP));`
     * @param type $name Name of the cache instance
     * @param array $settings Array of settings for this cache instance
     */
    public static function config($name, $settings) {
        $existing_instance = array();
        if (isset(self::$config[$name])) {
            //instance alreay exists, so we start with the existing settings
            $existing_instance = self::$config[$name];
        }
        self::$config[$name] = array_merge($existing_instance, $settings);

        //We must be supplied with a name of the desired CacheEngine 
        if (empty(self::$config[$name]['engine'])) {
            return False;
        }

        if (!isset(self::$engines[$name])) {
            self::initEngine($name);
        }
    }

    protected static function initEngine($name) {
        $config = self::$config[$name]; //settings for this engine
        //check engine class exists and is a cache engine
        
        if (!class_exists($config['engine'])) {
            trigger_error('Cache engine ' . $config['engine'] . ' not found.');
        }
        if (!is_subclass_of($config['engine'], 'BlockstrapCacheEngine')) {
            trigger_error('Cache engines must extend BlockstrapCacheEngine.');
        }
        //add to the array and init it with the $config
        self::$engines[$name] = new $config['engine']();
        if (!self::$engines[$name]->init($config)) {
            return False;
        }
        if (self::$engines[$name]->settings['purge_probability']) {
            if (rand(0, 100) < self::$engines[$name]->settings['purge_probability']) {
                self::$engines[$name]->purgeExpired();
            }
        }
    }

    public static function write($key, $value, $name = 'default') {
        $settings = self::getSettings($name);

        //valid cache?
        if (empty($settings)) {
            return false;
        }

        if (!self::isInit($name)) {
            return false;
        }
        $key = $settings['key_prefix'] . '_' . $key;

        if (!$key) {
            return false;
        }

        $success = self::$engines[$name]->write($key, $value, $settings['cache_duration']);

        if ($success === false && $value !== '') {
            if (!$settings['suppress_errors']) {
            trigger_error(
                    'Cant write key=' . $key . ' with ' .
                    self::$engines[$name]->settings['engine']
                    , E_USER_WARNING
            );
            }
        }
        return $success;
    }

    public static function read($key, $name = 'default') {
        $settings = self::getSettings($name);

        if (empty($settings)) {

            return false;
        }
        if (!self::isInit($name)) {
            return false;
        }

        $key = $settings['key_prefix'] . '_' . $key;

        if (!$key) {
            return false;
        }
        return self::$engines[$name]->read($key);
    }

    public static function purgeExpired($name = 'default', $expires = True) {
        self::$engines[$name]->purgeExpired($expires);
    }

    public static function purge($key, $name = 'default') {
        $settings = self::getSettings($name);

        if (empty($settings)) {
            return false;
        }
        if (!self::isInit($name)) {
            return false;
        }

        $key = $settings['key_prefix'] . '_' . $key;
        if (!$key) {
            return false;
        }

        return self::$engines[$name]->purge($key);
    }

    public static function purgeAll($name = 'default') {
        if (!self::isInit($name)) {
            return false;
        }
        return self::$engines[$name]->purgeAll();
    }

    public static function getSettings($name = 'default') {
        if (!empty(self::$engines[$name])) {
            return self::$engines[$name]->getSettings();
        }
        return array();
    }

    public static function isInit($name = 'default') {
        return isset(self::$engines[$name]);
    }

}
