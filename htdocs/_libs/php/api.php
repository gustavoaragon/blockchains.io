<?php

/*

BLOCKSTRAP DOCUMENTATION

http://blockstrap.com/docs/

MIT LICENSE

*/

class blockstrap_api
{
    private static $options = array(
        'url' => 'http://beta:beta123@neuro.api/v0/'
    );
    
    private static $date_format = 'l jS \of F Y h:i:s A';
    
    private static $currency = 'btc';
    
    private function option($key, $default = false)
    {
        if($key && isset($this::$options[$key]))
        {
            return $this::$options[$key];
        }
        else return $default;
    }          
    
    public function headers($directory)
    {
        $headers = array(
            'transaction' => array(
                'h1' => 'test',
                'h2' => 'testing'
            )
        );
        if($directory)
        {
            if(isset($headers[$directory]))
            {
                $headers[$directory]['search'] = false;
                $headers[$directory]['panel'] = false;
                $headers[$directory]['css'] = array(
                    'left' => 'col-xs-12',
                    'right' => 'hidden'
                );
                return $headers[$directory];
            }
            else
            {
                return false;
            }
        }
        else
        {
            return $headers;
        }
    }
    
    private function parameters($options = array())
    {
        /**
         * The API parameters used to control what the API returns.
         * These are the defaults, and exclude elements that are unique to the page
         * being shown. They are overwritten by each page before being used in the
         * call to the API.
         * More documentation on parameters used to control the API is available at
         * http://docs.blockstrap.com/api
         */
        $defaults = array(
            'showtxn' => 0,
            'showtxnio' => 0,
            'records' => 500,
            'skip' => 0,
            'currency' => 'USD',
            'prettyprint' => 0
        );
        $settings = array_merge($defaults, $options);
        return $settings;
    }
    
    private function get($options = array()) 
    {
        $parameters = $this->parameters($options);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->priv($parameters));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    private function pub($options = array()) 
    {
        $parameters = $this->parameters($options);
        $parameters['prettyprint'] = 1;
        unset($parameters['api_key']);
        unset($parameters['api_sig']);
        return $this->url($parameters);
    }

    private function priv($options = array()) 
    {
        $parameters = $this->parameters($options);
        $parameters['prettyprint'] = 0;
        return $this->url($parameters);
    }

    private function url($options = array()) 
    {
        global $chain;
        $parameters = $this->parameters($options);
        $url = $this->option('url', '').self::$currency.'/'.$parameters['method'];
        if(isset($parameters['id'])) 
        {
            $url .= '/' . $parameters['id'];
        }
        $url .= '?';
        if($parameters['showtxn']) 
        {
            $url .= 'showtxn=' . $parameters['showtxn'] . '&';
        }
        if($parameters['showtxnio']) 
        {
            $url .= 'showtxnio=' . $parameters['showtxnio'] . '&';
        }
        if(500 != $parameters['records']) 
        {
            $url .= 'records=' . $parameters['records'] . '&';
        }
        if($parameters['skip']) 
        {
            $url .= 'skip=' . $parameters['skip'] . '&';
        }
        if('USD' != $parameters['currency']) 
        {
            $url .= 'currency=' . $parameters['currency'] . '&';
        }
        if($parameters['prettyprint']) 
        {
            $url .= 'prettyprint=' . $parameters['prettyprint'] . '&';
        }
        if(isset($parameters['debug']) && $parameters['debug'])
        {
            var_dumped($url);
        }
        return $url;
    }
    
    private function request($slug)
    {
        $slugged_array = explode('/', $slug);
        if(count($slugged_array) === 3)
        {
            return $slugged_array[2];
        }
    }
    
    function __construct($base, $slug, $directory, $currency)
    {
        self::$currency = $currency;
    }
    
    public function transaction($base, $currency, $slug, $data = array())
    {   
        $id = $this->request($slug);
        
        // MAKE API CALL
        $options = array(
            'debug' => false,
            'method' => 'transaction',
            'id' => $id,
            'showtxn' => 1,
            'showtxnio' => 1
        );
        $results = $this->get($options);
        if(isset($results['status']) && $results['status'] == 'success')
        {
            $tx = $results['data']['Transaction'];
            $ago = $this->ago($tx['time']);
            $tx['extras'] = array(
                'value' => ($tx['_output_value'] - $tx['_fees']) / 100000000,
                'tx_time' => date(self::$date_format, $tx['time']),
                'block_time' => date(self::$date_format, $tx['_block_time']),
                'ago' => $ago
            );
            $data['header']['sub'] = array();
            $data['header']['sub']['h1'] = 'transaction first relayed '.$ago;
            $data['header']['sub']['h2'] = 'TXID '.$tx['id'];
            $data['objs'] = array(
                0 => array(
                    'header' => array(
                        'text' => 'TX '.$tx['id'],
                        'buttons' => array(
                            0 => array(
                                'href' => '#',
                                'text' => 'JSON'
                            )
                        )
                    ),
                    'req' => $results['data']['_request'],
                    'tx' => $tx
                )
            );
        }
        return $data;
    }
    
    public function height($base, $currency, $slug, $data = array())
    {   
        $id = $this->request($slug);
        
        // MAKE API CALL
        $options = array(
            'debug' => false,
            'method' => 'blockHeight',
            'id' => $id,
            'showtxn' => 1,
            'showtxnio' => 1
        );
        $results = $this->get($options);
        if(isset($results['status']) && $results['status'] == 'success')
        {
            $block = $results['data']['Blocks'][0];
            $ago = $this->ago($block['time']);
            
            $block['extras'] = array(
                'ago' => $ago
            );
            
            $data['header']['sub'] = array();
            $data['header']['sub']['h1'] = 'waiting for internet';
            $data['header']['sub']['h2'] = 'coming soon';
            
            $data['objs'] = array(
                0 => array(
                    'header' => array(
                        'text' => 'TX '.$block['id'],
                        'buttons' => array(
                            0 => array(
                                'href' => '#',
                                'text' => 'JSON'
                            )
                        )
                    ),
                    'req' => $results['data']['_request'],
                    'block' => $block
                )
            );
            foreach($data['objs'][0]['block']['Transactions'] as $tx_key => $tx)
            {
                $ago = $this->ago($tx['time']);
                $data['objs'][0]['block']['Transactions'][$tx_key]['extras'] = array(
                    'ago' => $ago
                );
            }
        }
        return $data;
    }
    
    public function ago($date = false)
    {
        // Array of time period chunks
        $chunks = array(
            array( 60 * 60 * 24 * 365 , _( 'year'), _( 'years') ),
            array( 60 * 60 * 24 * 30 , _( 'month'), _( 'months') ),
            array( 60 * 60 * 24 * 7, _( 'week'), _( 'weeks' ) ),
            array( 60 * 60 * 24 , _( 'day'), _( 'days') ),
            array( 60 * 60 , _( 'hour'), _( 'hours') ),
            array( 60 , _( 'minute'), _( 'minutes') ),
            array( 1, _( 'second'), _( 'seconds') )
        );

        if ( !is_numeric( $date ) ) {
            $time_chunks = explode( ':', str_replace( ' ', ':', $date ) );
            $date_chunks = explode( '-', str_replace( ' ', '-', $date ) );
            $date = gmmktime( (int)$time_chunks[1], (int)$time_chunks[2], (int)$time_chunks[3], (int)$date_chunks[1], (int)$date_chunks[2], (int)$date_chunks[0] );
        }

        //$current_time = current_time( 'mysql', $gmt = 0 );
        $newer_date = strtotime( 'now' );

        // Difference in seconds
        $since = $newer_date - $date;

        // Something went wrong with date calculation and we ended up with a negative date.
        if ( 0 > $since )
            return _( 'sometime');

        /**
         * We only want to output one chunks of time here, eg:
         * x years
         * xx months
         * so there's only one bit of calculation below:
         */

        //Step one: the first chunk
        for ( $i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];

            // Finding the biggest chunk (if the chunk fits, break)
            if ( ( $count = floor($since / $seconds) ) != 0 )
                break;
        }

        // Set output var
        $output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];


        if ( !(int)trim($output) ){
            $output = '0 ' . _('seconds');
        }

        $output .= _(' ago');

        return $output;
    }
}