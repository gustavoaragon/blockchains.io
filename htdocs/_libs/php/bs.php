<?php

/*

Blockchains.io is a Blockstrap Application
Designed and Mantained by Neuroware.io

MIT LICENSE - http://en.wikipedia.org/wiki/MIT_License

------------------------------------------------------

The MIT License (MIT)

Copyright (c) 2014 Neuroware.io, Inc

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

------------------------------------------------------

Learn more about Blockstrap
http://blockstrap.com

MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMZZZZZZZZZZZZZMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMZZZZZZZ,,,,,,,,,ZZZZZZZMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMNZZZZZZZ,,,,,,,,,,,,,,,,,,,ZZZZZZZ8MMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMOZZZZZZZ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,$ZZZZZZZMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMZZZZZZZ?,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,:ZZZZZZZMMMMMMMMMMMMMMM
MMMMMMMMMZZZZZZZ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,ZZZZZZZMMMMMMMMMM
MMMDZZZZZZZ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,ZZZZZZZOMMMM
MMZZZZ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,IZZZMMM
MMZZ:,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,ZZOMM
MMNZZZZZ$,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,?ZZZZZ8MMM
MMMMMMNZZZZZO.,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,:ZZZZZZDMMMMMMM
MMMMMMMMMZZZZZZZZZ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,Z$ZZZZZZZMMMMMMMMMM
MMMMNZZZZZZIIII?ZZZZZZ7,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,+ZZZZZZIIIIIZZZZZZDMMMMM
MMMMZZIIIIIIIIIIIIII7ZZZZZZ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,ZZZZZZ7IIIIIIIIIIIIIIZZDMMMM
MMMMZZZ7IIIIIIIIIIIIIIIII$ZZZZZZ,,,,,,,,,,,,,,,,,,,ZZ$ZZZZIIIIIIIIIIIIIIIII7ZZZMMMMM
MMMMMMZZZZZZIIIIIIIIIIIIIIIII7ZZZZZZ$,,,,,,,,,?ZZZZZZIIIIIIIIIIIIIIIIIIZZZZZZ8MMMMMM
MMMMMMMMMMZZZZZZ7IIIIIIIIIIIIIIIII7ZZZZZZZZZZZZZ7IIIIIIIIIIIIIIIIIIZZZZZZMMMMMMMMMMM
MMMMMMMMMZZZZZZZZZZZ$IIIIIIIIIIIIIIIIIIZZZZZI?IIIIIIIIIIIIIIII7ZZZZZZZZZZZNMMMMMMMMM
MMMMMMZZZZZ,,,,,,,:ZZZZZZIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIZZZZZZ=,,,,,,.$ZZZZMMMMMMM
MMMMMMZZ,,,,,,,,,,,,,,,ZZZZZZ7IIIIIIIIIIIIIIIIIIIIIIIIZZZZZZ,,,,,,,,,,,,,,,ZZ8MMMMMM
MMMMMMZZZZZ,,,,,,,,,,,,,,,,+ZZZZZZIIIIIIIIIIIIIII$ZZZZZ7.,,,,,,,,,,,,,,.OOZZZMMMMMMM
MMMMMMMMMOZZZZZ:,,,,,,,,,,,,,,,,ZZZZZZIIIIIIIZZZZZZ,,,,,,,,,,,,,,,,.ZZZZZZMMMMMMMMMM
MMMMMMMMMMMMM$ZZZZZ7,,,,,,,,,,,,,,,,ZZZZZZZZZZZ,,,,,,,,,,,,,,,,=ZZZZZZMMMMMMMMMMMMMM
MMMMMMMMMZZZZZZIIIZZZZZZ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,$ZZZZZ7II$ZZZZZMMMMMMMMMM
MMMMMMMMZZ7IIIIIIIIIIIZZZZZZ,,,,,,,,,,,,,,,,,,,,,,,,,,,ZZZZZZIIIIIIIIIIIIZZMMMMMMMMM
MMMMMMMMZZZIIIIIIIIIIIIIIIZZZZZZ,,,,,,,,,,,,,,,,,,,ZZZZZZIIIIIIIIIIIIIIIZZZMMMMMMMMM
MMMMMMMMMZZZZZ$IIIIIIIIIIIIIIIZZZZZZ.,,,,,,,,,,ZZZZZZIIIIIIIIIIIIIII$ZZZZZMMMMMMMMMM
MMMMMMMMMMMMMOZZZZ7IIIIIIIIIIIIIII$ZZZZZ.,,ZZZZZZIIIIIIIIIIIIIII7ZZZZZMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMZZZZZ7IIIIIIIIIIIIIII$ZZZZZ$IIIIIIIIIIIIIII7ZZZZZMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMZZZZZ7IIIIIIIIIIIIIIIIIIIIIIIIIIIII7$ZZZZNMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMNZZZZZ7IIIIIIIIIIIIIIIIIIIIIIZZZZZDMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMNZZZZZIIIIIIIIIIIIIIIZZZZZDMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMDZZZZZIIIIIIIZZZZZ8MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM8ZZZZZZZZZOMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMOZ8MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM

TO LEARN MORE ABOUT OUR APIs and FRAMEWORK,
PLEASE READ OUR DOCUMENTATION - http://docs.blockstrap.com

*/

function var_dumped($results)
{
    echo '<pre>';
    var_dump($results);
    echo '</pre>';
}

class blockstrap_core
{
    private static $api = false;
    
    private function init($php_base, $base, $slug, $directory, $currency)
    {
        include_once($php_base.'/_libs/php/api.php');
        include_once($php_base.'/_libs/php/vendors/parsedown.php');
        include_once($php_base.'/_libs/php/vendors/mustache.php');
        self::$api = new blockstrap_api($base, $slug, $directory, $currency);
    }
    
    function is_page($directory, $currency)
    {
        $pages = array('about', 'multi');
        foreach($pages as $page)
        {
            if($page == $directory || $page == $currency) return true;
        }
        return false;
    }
    
    function __construct($php_base, $default_currency = 'btc')
    {
        if(!$php_base) $php_base = dirname(__FILE__);
        $base = $this->base($_SERVER);
        $slug = $this->slug($_SERVER, $base);
        $directory = $this->directory($_SERVER, $base);
        $currency = $this->currency($_SERVER, $base);
        if(
            strlen($currency) == 3 
            || strlen($currency) == 4 
            || $this->is_page($slug, $currency)
        ){
            if($slug == $currency && !$this->is_page($slug, $currency))
            {
                $url = $base.$currency.'/blocks/';
                header('Location: '.$url, true, 302);
                exit;    
            }
            else
            {
                $this->init($php_base, $base, $slug, $directory, $currency);
            }
        }
        elseif($slug)
        {
            if(isset($_GET) && isset($_GET['searchterm']))
            {
                $url = $base.'multi/'.$slug.'/'.$_GET['searchterm'];
            }
            else
            {
                $url = $base.$default_currency.'/'.$slug;
            }
            header('Location: '.$url, true, 302);
            exit;
        }
        else
        {
            $this->init($php_base, $base, $slug, $directory, $currency);
        }
    }
    
    public function base($server)
    {
        $url = '';
        $slug = $this->slug($server);
        if(isset($server['REDIRECT_URL']))
        {
            $url = $server['REDIRECT_URL'];
        }
        $base = substr($url, 0, 0 - (strlen($slug) + 1));
        return $base;
    }
    
    public function data($base, $slug, $directory, $currency = 'btc')
    {
        $data = false;
        if($directory) $directory = rtrim($directory, '/');
        if($currency.'/' == $slug) $currency = 'en';
        if(file_exists($base.'/_libs/defaults/index.json'))
        {
            $contents = file_get_contents($base.'/_libs/defaults/index.json');
            $data = json_decode($contents, true);
        }
        if($directory)
        {
            // GET SPECIFIC DATA
            if(file_exists($base.'/'.$directory.'/data.json'))
            {
                $data = array_merge(
                    $data, 
                    json_decode(file_get_contents($base.'/'.$directory.'/data.json'), true)
                );
            }
        }
        if($slug)
        {
            $data['header'] = array(
                'h1' => ''
            );
            if(file_exists($base.'/data/'.$slug.'.json'))
            {
                $data = array_merge(
                    $data, 
                    json_decode(file_get_contents($base.'/data/'.$slug.'.json'), true)
                );
            }
            elseif(file_exists($base.'/'.$currency.'/'.$slug.'.json'))
            {
                $data = array_merge(
                    $data, 
                    json_decode(file_get_contents($base.'/'.$currency.'/'.$slug.'.json'), true)
                );
            }
            if(self::$api->option('chains'))
            {
                $chains = self::$api->option('chains');
                if(is_array($chains))
                {
                    foreach($chains as $code => $name)
                    {
                        if($code.'/blocks' === $slug)
                        {
                            $button = '';
                            $data['header']['sub']['h1'] = 'Latest '.$name.' Blocks'.$button;
                            $data['header']['sub']['button'] = 'http://api.blockstrap.com/v0/'.$code.'/block/latest/5?prettyprint=1';
                        }
                    }
                }
            }
            if(strpos($slug, '/search/') !== false) 
            {
                $slug_array = explode('/', $slug);
                $term = end($slug_array);
                $data['header']['sub']['h1'] = 'Search Results for "'.$term.'"';
            }
        }
        else
        {
            $api = self::$api;
            $active_currency = $api::$currency;
            $chain = $api->currency($active_currency);
            $data['page']['h4'] = 'Latest '.$chain.' Blocks';
        }
        if(isset(self::$api))
        {
            $api = self::$api;
            if(isset($api::$options))
            {
                $options = $api::$options;
                if(isset($options['api'])) unset($options['api']);
                if(isset($options['chains'])) unset($options['chains']);
                if(!is_array($data)) $data = array();
                $data = array_merge(
                    $data, 
                    $options
                );
            }
        }
        if(method_exists(self::$api, self::$api->call($directory)))
        {
            $func = self::$api->call($directory);
            $data = self::$api->$func($base, $currency, $slug, $data);
            $slug_array = explode('/', $slug);
            if($currency.'/'.$func == $slug)
            {
                $data['page']['meta'] = 'Latest '.self::$api->currency($currency).' '.ucfirst($func);
            }
            elseif(count($slug_array) === 3)
            {
                $id = $slug_array[2];
                $data['page']['meta'] = self::$api->currency($currency).' '.ucfirst($func).' '.$id;
            }
        }
        elseif(method_exists(self::$api, self::$api->call($currency)))
        {
            $func = self::$api->call($currency);
            $data = self::$api->$func($base, $currency, $slug, $data);
            $data['page']['meta'] = 'test';
        }
        $data['stats'] = self::$api->stats();
        
        $data = $this->filter($data, $directory, $slug, $currency, $base);
        return $data;
    }
    
    public function filter($raw_data, $directory, $slug, $currency, $base)
    {
        $slug_array = explode('/', $slug);
        foreach($slug_array as $this_slug)
        {
            if($this_slug)
            {
                $raw_data['page']['base'] = $raw_data['page']['base'].'../';
            }
        }
        if($slug)
        {
            $raw_data['page']['css'] = 'page';
        }
        return $raw_data;
    }
    
    public function html($base, $slug, $directory, $currency = 'btc')
    {
        $html = false;
        $page = 'index';
        $header = '';
        $footer = '';
        if($slug)
        {
            $page = '404';
            if(file_exists($base.'/html/'.$slug.'.html'))
            {
                $html = file_get_contents($base.'/html/'.$slug.'.html');
            }
            elseif(file_exists($base.'/html/'.rtrim($directory, '/').'.html'))
            {
                $html = file_get_contents($base.'/html/'.rtrim($directory, '/').'.html');
            }
        }
        else if($directory)
        {
            $page = '404';
            if(file_exists($base.'/html/'.$directory.'.html'))
            {
                $html = file_get_contents($base.'/html/'.$directory.'.html');
            }
        }
        if(!$html && file_exists($base.'/html/'.$page.'.html'))
        {
            if($page == '404')
            {
                header("HTTP/1.0 404 Not Found");
            }
            $html = file_get_contents($base.'/html/'.$page.'.html');
        }
        if(file_exists($base.'/_libs/defaults/header.html'))
        {
            $header = file_get_contents($base.'/_libs/defaults/header.html');
        }
        if(file_exists($base.'/_libs/defaults/footer.html'))
        {
            $footer = file_get_contents($base.'/_libs/defaults/footer.html');
        }
        return $header.$html.$footer;
    }
    
    public function content($base, $slug, $directory)
    {
        // TO BE USED FOR MARKDOWN WITH PAGES ...?
    }
    
    public function display($html, $data)
    {
        $template = new MustachePHP();
        echo $template->render($html, $data);
    }
    
    public function markdown($string)
    {
        $parsedown = new Parsedown();
        return $parsedown->text($string);
    }
    
    public function directory($server, $base)
    {
        $url = '';
        $self = $server['PHP_SELF'];
        if(isset($server['REDIRECT_URL']))
        {
            $url = $server['REDIRECT_URL'];
        }
        $self_array = array_slice(explode('/', $self), 1, -1);
        $url_array = array_slice(explode('/', $url), count($self_array) + 1, -1);
        if(count($url_array) < 1)
        {
            return '';
        }
        else 
        {
            if(!isset($url_array[1]))
            {
                return false;
            }
            else
            {
                return $url_array[1];
            }
        }
    }
    
    public function currency($server)
    {
        $url = '';
        $self = $server['PHP_SELF'];
        if(isset($server['REDIRECT_URL']))
        {
            $url = $server['REDIRECT_URL'];
        }
        $self_array = array_slice(explode('/', $self), 1, -1);
        $url_array = array_slice(explode('/', $url), count($self_array) + 1, -1);
        if(count($url_array) > 0) return $url_array[0];
        else return '';
    }
    
    public function slug($server)
    {
        $slug = '';
        $url = '';
        $self = $server['PHP_SELF'];
        $self_len = strlen($self) - strlen('/index.php');
        $request = substr($server['PHP_SELF'], 0, $self_len);
        $request_len = strlen($request);
        if(isset($server['REDIRECT_URL']))
        {
            $url = substr($server['REDIRECT_URL'], $request_len);
        }
        return ltrim(rtrim($url, '/'), '/');
    }
}