<?php

namespace
{
    define('filesRoot', '/home/exmaple.org/');
    define('httpRoot', 'http:///exmaple.org/');

    function eresus_log() {}

    class FS
    {
        public static function canonicalForm($filename)
        {
            /* Convert slashes */
            $filename = str_replace('\\', '/', $filename);

            /* Prepend drive letter with slash if needed */
            if (substr($filename, 1, 1) == ':')
            {
                $filename = '/' . $filename;
            }

            return $filename;
        }

        public static function isFile($filename)
        {
            return is_file($filename);
        }
    }


    class EresusRuntimeException extends Exception {}

    class EresusApplication
    {
        public $fsRoot;

        public function getFsRoot()
        {
            return $this->fsRoot;
        }
    }

    class HttpRequest
    {
        public $localRoot;

        public function setLocalRoot($value)
        {
            $this->localRoot = $value;
        }
        //-----------------------------------------------------------------------------

        public function getLocalRoot()
        {
            return $this->localRoot;
        }
        //-----------------------------------------------------------------------------

        public function getScheme()
        {
            return 'http';
        }
        //-----------------------------------------------------------------------------

        public function getHost()
        {
            return 'example.org';
        }
        //-----------------------------------------------------------------------------
    }

    /**
     * @since 2.15
     */
    class TemplateSettings
    {
        public static function setGlobalValue($a, $b)
        {
            ;
        }
        //-----------------------------------------------------------------------------
    }

    class Template
    {
        public function compile($data)
        {
            return $data;
        }
        //-----------------------------------------------------------------------------
    }

    class Twig_Extension {}
}

namespace Eresus\ORMBundle
{
    class AbstractEntity {}
}

namespace Doctrine\Common\Collections
{
    class ArrayCollection extends \UniversalStub {}
}

namespace Doctrine\ORM
{
    class EntityRepository {}
}

