<?php

/*

BLOCKSTRAP DOCUMENTATION

http://blockstrap.com/docs/

MIT LICENSE

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
    
    function __construct($php_base, $default_currency = 'btc')
    {
        if(!$php_base) $php_base = dirname(__FILE__);
        $base = $this->base($_SERVER);
        $slug = $this->slug($_SERVER, $base);
        $directory = $this->directory($_SERVER, $base);
        $currency = $this->currency($_SERVER, $base);
        if(strlen($currency) == 3 || strlen($currency) == 4)
        {
            $this->init($php_base, $base, $slug, $directory, $currency);
        }
        elseif($slug)
        {
            $url = $base.$default_currency.'/'.$slug;
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
        $base = substr($url, 0, 0 - strlen($slug));
        return $base;
    }
    
    public function data($base, $slug, $directory, $currency = 'en')
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
            if(file_exists($base.'/'.$slug.'/data.json'))
            {
                $data = array_merge(
                    $data, 
                    json_decode(file_get_contents($base.'/'.$slug.'/data.json'), true)
                );
            }
            elseif(file_exists($base.'/'.$currency.'/'.$slug.'/data.json'))
            {
                $data = array_merge(
                    $data, 
                    json_decode(file_get_contents($base.'/'.$currency.'/'.$slug.'/data.json'), true)
                );
            }
        }
        if(method_exists(self::$api, $directory))
        {
            $data = self::$api->$directory($base, $currency, $slug, $data);
        }
        $data = $this->filter($data, $directory, $slug, $currency, $base);
        //var_dumped($data); exit;
        return $data;
    }
    
    public function filter($raw_data, $directory, $slug, $currency, $base)
    {
        if($currency && $directory)
        {
            if(isset($raw_data['page']) && isset($raw_data['page']['base']))
            {
                $raw_data['page']['base'] = '../../';
                $raw_data['page']['css'] = 'page';
            }
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
                return $url_array[1].'/';
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
        if(isset($server['REDIRECT_URL']))
        {
            $url = $server['REDIRECT_URL'];
        }
        $self_array = array_slice(explode('/', $self), 1, -1);
        $url_array = array_slice(
            explode('/', $url), 
            count($self_array) + 1, 
            count($self_array)
        );
        foreach($url_array as $url)
        {
            $slug.= $url.'/';
        }
        return rtrim($slug, '/');
    }
}