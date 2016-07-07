<?php

/** getDatafromUri
    * get data from uri split and return
    *
    * @param string $getDataID data to request
    * @return string or array
    */
    function getDatafromUri($getDataID=false)
        {
        $request = $_SERVER['REQUEST_URI'];
        if(substr($request, 0, 1) == '/') 
            $request = substr($request, 1);
        $request = explode('/', $request);
        if($getDataID !== false)
            if (isset($request[$getDataID]))
                return $request[$getDataID];
            else
                return false;
        else
            return $request;
        }

/** NametoUri
    * conver name to self uri
    *
    * @param string $name
    * @return string
    */
    function NametoUri($name)
        {
        $name = trim($name);
        $name = strtolower($name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = str_replace([' - ', ' : ',' ', ':','--'], '-', $name );
        return preg_replace('/[^A-Za-z0-9\-]/', '', $name );
        }

/** DateFormat
    * convert mysql date to phpdate
    *
    * @param string $mysqldate
    * @return string
    */
    function DateFormat($mysqldate)
        {
        return date( 'd-m-y H:i:s', strtotime( $mysqldate ) );
        }

/** limitText
    * limit name text
    *
    * @param string $text
    * @return string
    */
    function limitText ( $text )
        {
        if ( strlen ( $text ) > 20 )
            return mb_substr ( $text, 0, 12 ).'...'.mb_substr ( $text, -5 );
        else
            return $text;
        }

/** checked
    * checked
    *
    * @param string $value
    * @param string $choise
    * @return array
    */
    function checked($value, $choise='true,false')
        {
        $choise = toArray($choise);
        $checked = array();
        foreach($choise as $check)
            {
            if($value==$check)
                $checked[$check] = ' checked';
            else
                $checked[$check] = '';
            }
        return $checked;
        }

/** filesizeConvert
    * Converts bytes into human readable file size.
    *
    * @param string $bytes
    * @return string human readable file size (2,87 Мб)
    * @author Mogilev Arseny
    */
    function FileSizeConvert($bytes)
        {
        $bytes = floatval($bytes);
            $arbytes = [
                0 => ["unit" => "TB", "value" => pow(1024, 4)],
                1 => ["unit" => "GB", "value" => pow(1024, 3)],
                2 => ["unit" => "MB", "value" => pow(1024, 2)],
                3 => ["unit" => "KB", "value" => 1024],
                4 => ["unit" => "B", "value" => 1],
            ];
        foreach($arbytes as $aritem)
            {
            if($bytes >= $aritem["value"])
                {
                $result = $bytes / $aritem["value"];
                $result = strval(round($result, 2))." ".$aritem["unit"];
                break;
                }
            else
                {
            $result = '0 B';
                }
            }
        return $result;
        }

/** createDirectory
    * Create directory
    *
    * @param string $directory
    * @return bool
    */
    function CreateDirectory ( $directory )
        {
        if ( isset ( $directory ) )
            {
            file_exists ( $directory ) ? '' : mkdir ( $directory, 0700 );
            return true;
            }
        else
            {
            return false;
            }
        }

/** increaseIndex
    * Change array index to 1
    *
    * @param array $array
    * @return array
    * @author Mr. Alien
    */
    function increaseIndex ( $array )
        {
        return array_combine ( range( 1, count ( $array ) ), $array );
        }

/** mtrim
    * Multidimensional arrays trim with recursive function
    *
    * @param array $array
    * @return array
    */
    function mtrim ( $value )
        {
        return is_array ( $value ) ? array_map ( 'mtrim', $value ) : trim ( $value, " \t\n\r\0\x0B\xC2\xA0" );
        }

    function mstripslashes ( $value )
        {  
        return is_array ( $value ) ? array_map ( 'mstripslashes', $value ) : stripslashes ( $value );
        }

    function maddslashes ( $value )
        {  
        return is_array ( $value ) ? array_map ( 'maddslashes', $value ) : addslashes ( $value );
        }

/** arrayswitch
	* arrayswitch
	*
	* @param array $array
	* @param int $oldIndex
	* @param int $newIndex
	* @return array
	*/
    function arrayswitch ( $array, $oldIndex, $newIndex )
        {
        array_splice (
            $array,
            $newIndex,
            count ( $array ),
            array_merge (
                array_splice ( $array, $oldIndex, 1 ),
                array_slice ( $array, $newIndex, count ( $array ) )
            )
        );
        return $array;
        }

 /** toArray
    * toArray
    *
    * @param string $string
    * @param string $split
    * @return array
    */
    function toArray ( $string, $split = ',' )
        {
        return array_filter ( array_map ( 'trim', explode ( $split, $string ) ), 'strlen' );
        }

 /** stringPad
    * stringPad
    *
    * @param string $string
    * @return string
    */
    function stringPad ( $string, $length = '3' )
        {
        return str_pad ( (int) $string, $length, '0', STR_PAD_LEFT ).strrchr ( $string, '.' );
        }

/** selfBool
    * Convert string to boolean
    *
    * @param string $string
    * @return boolean
    * @author DevellMen
    */
    function selfBool($string)
        {
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
        }

/** autoloader
    * Autoloader - Simple class autoloading function (PHP 5.4+)
    *
    * @author Shay Anderson 05.13
    * @copyright 2013 Shay Anderson <http://www.shayanderson.com>
    * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
    * @link http://www.shayanderson.com/php/simple-php-class-autoloading-function-and-tutorial.htm
    *
    * Autoloader
    *
    * @staticvar boolean $is_init
    * @staticvar array $conf
    * @staticvar array $paths
    * @param array|string|NULL $class_paths
    *        when loading class paths ex: ['path/one', 'path/two']
    *        when loading class ex: 'myclass'
    *        when returning cached paths: NULL
    * @param boolean $use_base_dir (when true will prepend class path with base directory)
    * @return array|boolean
    *        (default boolean if class paths registered/loaded, or when debugging
    *        (or NULL passed as $class_paths) array of registered class paths
    *        (and loaded class files, configuration settings) returned)
    */
    function autoloader($class_paths = NULL, $use_base_dir = true)
        {
        static $is_init = false;
        static $conf = [
            // set debug mode on/off
            'debug' => false,
            // set project base path
            'basepath' => '',
            // set allowed class extension(s) to load
            'extensions' => ['.php', '.inc.php', '.class.php'],
            // use namespace if autoloader function is in namespace for registering autoloader
            'namespace' => '',
            // will print internal messages (for debugging)
            'verbose' => false
        ];
        static $paths = [];
        if (is_null($class_paths)) // autoloader(); returns paths (for debugging)
            {
            return $paths;
            }
        if (is_array($class_paths) && isset($class_paths[0]) && is_array($class_paths[0])) // conf settings
            {
            foreach($class_paths[0] as $k => $v)
                {
                if (isset($conf[$k]) || array_key_exists($k, $conf))
                    {
                    $conf[$k] = $v; // set conf setting
                    }
                }
            unset($class_paths[0]); // rm conf from class paths
            }
        if (!$is_init) // init autoloader
            {
            spl_autoload_extensions(implode(',', $conf['extensions']));
            spl_autoload_register(NULL, false); // flush existing autoloads
            $is_init = true;
            }
        if ($conf['debug'])
            {
            $paths['conf'] = $conf; // add conf for debugging
            }
        if (!is_array($class_paths)) // autoload class
            {
            // class with namespaces, ex: 'MyPack\MyClass' => 'MyPack/MyClass' (directories)
            $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_paths);

            foreach($paths as $path)
                {
                if (!is_array($path)) // do not allow cached 'loaded' paths
                    {
                    foreach($conf['extensions'] as & $ext)
                        {
                        $ext = trim($ext);
                        if (file_exists($path.$class_path.$ext))
                            {
                            if ($conf['debug'])
                                {
                                if (!isset($paths['loaded']))
                                    {
                                    $paths['loaded'] = [];
                                    }
                                $paths['loaded'][] = $path.$class_path.$ext;
                                }
                            require $path.$class_path.$ext;
                            if ($conf['verbose'])
                                {
                                echo '<div>'.__METHOD__.': autoloaded class "'.$path.$class_path.$ext.'"</div>';
                                }
                            return true;
                            }
                    }
                    if ($conf['verbose'])
                        {
                        echo '<div>'.__METHOD__.': failed to autoload class "'.$path.$class_path.$ext.'"</div>';
                        }
                    }
                }
            return false; // failed to autoload class
            }
        else // register class path
            {
            $is_unregistered = true;
            if (count($class_paths) > 0)
                {
                foreach($class_paths as $path)
                    {
                    $tmp_path = ($use_base_dir ? rtrim($conf['basepath'], DIRECTORY_SEPARATOR)
                        .DIRECTORY_SEPARATOR : '').trim(rtrim($path, DIRECTORY_SEPARATOR))
                        .DIRECTORY_SEPARATOR;
                    if (!in_array($tmp_path, $paths))
                        {
                        $paths[] = $tmp_path;
                        if ($conf['verbose'])
                            {
                            echo '<div>'.__METHOD__.': registered path "'.$tmp_path.'"</div>';
                            }
                        }
                    }
                $namespace = strlen($conf['namespace']) > 0 ? rtrim($conf['namespace'], '\\').'\\' : ''; // add namespace
                if (spl_autoload_register(($namespace).'autoloader', (bool) $conf['debug']))
                    {
                    if ($conf['verbose'])
                        {
                        echo '<div>'.__METHOD__.': autoload registered</div>';
                        }
                    $is_unregistered = false; // flag unable to register
                    }
                else if ($conf['verbose'])
                    {
                    echo '<div>'.__METHOD__.': autoload register failed</div>';
                    }
                }
            return !$conf['debug'] ? !$is_unregistered : $paths;
            }
        }
