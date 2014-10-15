<?php

/*

BLOCKSTRAP DOCUMENTATION

http://blockstrap.com/docs/

MIT LICENSE

*/

class blockstrap_api
{
    private static $options = array(
        'url' => 'http://beta:beta123@neuro.api/v0/',
        'chains' => array(
            'btc' => 'Bitcoin',
            'ltc' => 'Litecoin',
            'dog' => 'Dogecoin',
            'btct' => 'BTC Testnet',
            'ltct' => 'LTC Testnet',
            'dogt' => 'DOGE Testnet'
        )
    );
    
    private function currency($code)
    {
        $chains = $this->option('chains');
        if(isset($chains[$code])) return $chains[$code];
        else return 'Unknown';
    }
    
    private static $date_format = 'l jS \of F Y h:i:s A';
    
    private static $currency = 'dogt';
    
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
        $currency_to_try = self::$currency;
        if(!$currency_to_try) $currency_to_try = 'btc';
        $parameters = $this->parameters($options);
        if(isset($parameters['coin'])) $currency_to_try = $parameters['coin'];
        $url = $this->option('url', '').$currency_to_try.'/'.$parameters['method'];
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
    
    private function request($slug, $default = false)
    {
        $slugged_array = explode('/', $slug);
        if(count($slugged_array) === 3)
        {
            return $slugged_array[2];
        }
        return $default;
    }
    
    function __construct($base, $slug, $directory, $currency)
    {
        if($currency) self::$currency = $currency;
    }
    
    public function call($directory = false)
    {
        $func = 'blocks';
        if($directory)
        {
            if(
                $directory == 'address'
                || $directory == 'block'
                || $directory == 'blocks'
                || $directory == 'height'
                || $directory == 'search'
                || $directory == 'transaction'
            ){
                $func = $directory;
            }
        }
        return $func;
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
            $tx = $results['data']['transaction'];
            $ago = $this->ago($tx['time']);
            $tx['extras'] = array(
                'value' => ($tx['output_value'] - $tx['fees']) / 100000000,
                'tx_time' => date(self::$date_format, $tx['time']),
                'block_time' => date(self::$date_format, $tx['block_time']),
                'ago' => $ago
            );
            
            if(isset($results['data']['_request']))
            {
                $tx['extras']['currency'] = $this->currency(strtolower($results['data']['_request']['chain']['code']));
                $tx['extras']['code'] = strtolower($results['data']['_request']['chain']['code']);
            }
            
            $data['header']['sub'] = array(
                'h1' => 'transaction first relayed '.$ago,
                'h2' => 'TXID '.$tx['id']
            );

            $data['objs'] = array(
                0 => array(
                    'req' => $results['data']['_request'],
                    'tx' => $tx
                )
            );
        }
        return $data;
    }
    
    public function blocks($base, $currency, $slug, $data = array())
    {   
        $id = $this->request($slug, 10);
        
        // MAKE API CALL
        $options = array(
            'debug' => false,
            'method' => 'blocksLatest',
            'id' => $id,
            'showtxn' => 1,
            'showtxnio' => 1
        );
        $results = $this->get($options);
        if(isset($results['status']) && $results['status'] == 'success')
        {
            $blocks = $results['data']['blocks'];
            $data['objs'] = array();
            foreach($blocks as $block_key => $block)
            {
                $ago = $this->ago($block['time']);
                $block['extras'] = array(
                    'ago' => $ago
                );
                if(isset($results['data']['_request']))
                {
                    $block['extras']['currency'] = $this->currency(strtolower($results['data']['_request']['chain']['code']));
                    $block['extras']['code'] = strtolower($results['data']['_request']['chain']['code']);
                }
                foreach($block['transactions'] as $tx_key => $tx)
                {
                    $ago = $this->ago($tx['time']);
                    $block['transactions'][$tx_key]['extras'] = array(
                        'ago' => $ago
                    );
                }
                $data['objs'][$block_key] = $block;
            }
            $data['req'] = $results['data']['_request'];
        }
        return $data;
    }
    
    public function height($base, $currency, $slug, $data = array())
    {   
        $id = $this->request($slug);
        
        // MAKE API CALL
        $options = array(
            'debug' => false,
            'coin' => $currency,
            'method' => 'blockHeight',
            'id' => $id,
            'showtxn' => 1,
            'showtxnio' => 1
        );
        $results = $this->get($options);
        if(isset($results['status']) && $results['status'] == 'success')
        {
            if(isset($results['data']['blocks'][0]))
            {
                $block = $results['data']['blocks'][0];
                $ago = $this->ago($block['time']);

                $block['extras'] = array(
                    'ago' => $ago
                );
                
                if(isset($results['data']['_request']))
                {
                    $block['extras']['currency'] = $this->currency(strtolower($results['data']['_request']['chain']['code']));
                    $block['extras']['code'] = strtolower($results['data']['_request']['chain']['code']);
                }

                $data['header']['sub'] = array(
                    'h1' => 'Block Height '.$block['height'],
                    'h2' => 'Hash '.$block['id']
                );

                $data['objs'] = array(
                    0 => array(
                        'req' => $results['data']['_request'],
                        'block' => $block
                    )
                );
                foreach($data['objs'][0]['block']['transactions'] as $tx_key => $tx)
                {
                    $ago = $this->ago($tx['time']);
                    $data['objs'][0]['block']['transactions'][$tx_key]['extras'] = array(
                        'ago' => $ago
                    );
                }
            }
        }
        return $data;
    }
    
    public function block($base, $currency, $slug, $data = array())
    {   
        $id = $this->request($slug);
        
        // MAKE API CALL
        $options = array(
            'debug' => false,
            'method' => 'block',
            'id' => $id,
            'showtxn' => 1,
            'showtxnio' => 1
        );
        $results = $this->get($options);
        if(isset($results['status']) && $results['status'] == 'success')
        {
            $block = $results['data']['block'];
            $ago = $this->ago($block['time']);
            
            $block['extras'] = array(
                'ago' => $ago
            );
            
            if(isset($results['data']['_request']))
            {
                $block['extras']['currency'] = $this->currency(strtolower($results['data']['_request']['chain']['code']));
                $block['extras']['code'] = strtolower($results['data']['_request']['chain']['code']);
            }
            
            $data['header']['sub'] = array(
                'h1' => 'Block Height '.$block['height'],
                'h2' => 'Hash '.$block['id']
            );
            
            $data['objs'] = array(
                0 => array(
                    'req' => $results['data']['_request'],
                    'block' => $block
                )
            );
            foreach($data['objs'][0]['block']['transactions'] as $tx_key => $tx)
            {
                $ago = $this->ago($tx['time']);
                $data['objs'][0]['block']['transactions'][$tx_key]['extras'] = array(
                    'ago' => $ago
                );
            }
        }
        return $data;
    }
    
    public function address($base, $currency, $slug, $data = array())
    {   
        $id = $this->request($slug);
        
        // MAKE API CALL
        $options = array(
            'debug' => false,
            'method' => 'addressTransactions',
            'id' => $id,
            'showtxn' => 1,
            'showtxnio' => 1
        );
        $results = $this->get($options);
        if(isset($results['status']) && $results['status'] == 'success')
        {
            $address = $results['data']['address'];
            
            $address['extras'] = array(
                
            );
            
            if(isset($results['data']['_request']))
            {
                $address['extras']['currency'] = $this->currency(strtolower($results['data']['_request']['chain']['code']));
                $address['extras']['code'] = strtolower($results['data']['_request']['chain']['code']);
            }
            
            $data['header']['sub'] = array(
                'h1' => 'Address '.$address['address'],
                'h2' => 'Hash 160 - '.$address['address_hash160']
            );
            
            $data['objs'] = array(
                0 => array(
                    'req' => $results['data']['_request'],
                    'address' => $address
                )
            );
            foreach($data['objs'][0]['address']['transactions'] as $tx_key => $tx)
            {
                $ago = $this->ago($tx['time']);
                $data['objs'][0]['address']['transactions'][$tx_key]['ago'] = $ago;
            }
        }
        return $data;
    }
    
    public function term($id)
    {
        $term = false;
        $id_int = is_numeric($id);
        if($id_int > 0) $term = 'height';
        elseif(strlen($id) > 25 && strlen($id) < 35) $term = 'address';
        elseif($id) $term = 'transaction';
        return $term;
    }
    
    public function search($base, $currency, $slug, $data = array())
    {   
        $id = $this->request($slug);
        $search_type = $this->term($id);
        $data['search_failed'] = true;
        $data['is_block'] = false;
        $data['is_address'] = false;
        $data['is_transaction'] = false;
        if(method_exists($this, $search_type))
        {
            $is_term = $search_type;
            if($search_type == 'height') $is_term = 'block';
            $data['search_failed'] = false;
            $data['is_'.$is_term] = true;
            $this_slug = $currency.'/'.$search_type.'/'.$id;
            $data = $this->$search_type($base, $currency, $this_slug, $data);
            if(!isset($data['objs']))
            {
                // ONLY START RECURSIVE SEARCH IF NECESSARY
                if($currency == 'multi')
                {
                    $data = $this->searches(
                        $base, 
                        $currency, 
                        $this_slug, 
                        $data, 
                        $search_type, 
                        $id
                    );
                }
                if(!isset($data['objs']))
                {
                    $data['search_failed'] = true;
                }
            }
        }
        return $data;
    }
    
    public function searches($base, $currency, $this_slug, $data, $search_type, $id)
    {
        $objs = false;
        $chains = $this->option('chains');
        foreach($chains as $chain => $name)
        {
            if($chain != $currency)
            {
                if(method_exists($this, $this->call($search_type)))
                {
                    $new_slug = $chain.'/'.$search_type.'/'.$id;
                    if(is_array($objs) && isset($data['objs']))
                    {
                        $data['objs'] = array_merge($data['objs'], $objs);
                    }
                    $obj = $this->$search_type($base, $chain, $new_slug, $data);
                    if(isset($obj['objs']))
                    {
                        if(is_array($objs)) $objs = array_merge($objs, $obj['objs']);
                        else $objs = $obj['objs'];
                        if($search_type != 'height') return $data;
                    }
                }
            }
        }
        $data['objs'] = $objs;
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