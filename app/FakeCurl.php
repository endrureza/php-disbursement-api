<?php

namespace App;

class FakeCurl
{
    public $curl;
    public $url = null;
    public $response = null;

    private $headers = [];
    private $options = [];

    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }

        $this->curl = curl_init();
        $this->init();
    }

    /**
     * Destruct this script
     */
    public function __destruct()
    {
        $this->close();
    }

    public function get($url, $data = [])
    {
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $this->setOpt(CURLOPT_HTTPGET, true);

        return $this->send();
    }

    public function post($url, $data = [])
    {
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));

        return $this->send();
    }

    /**
     * Build data for post request
     *
     * @param $data
     * @return void
     */
    public function buildPostData($data)
    {
        $data = http_build_query($data, '', '&');
        return $data;
    }

    /**
     * Send curl
     *
     * @param $curl
     * @return void
     */
    public function send()
    {
        $this->response = curl_exec($this->curl);

        return $this->response;
    }

    /**
     * Close curl to free up memory
     *
     * @return void
     */
    public function close()
    {
        curl_close($this->curl);
    }

    /**
     * Set value for every curl opt
     *
     * @param $option
     * @param $value
     * @return bool
     */
    public function setOpt($option, $value): bool
    {
        $result = curl_setopt($this->curl, $option, $value);

        if ($result) {
            $this->options[$option] = $value;
        }

        return $result;
    }

    /**
     * Set curl url
     *
     * @param  $url
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;

        $this->setOpt(CURLOPT_URL, $this->url);
    }

    /**
     * Set single header
     *
     * @param $key
     * @param $value
     * @return void
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ": " . $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Set multi headers
     *
     * @param $headers
     * @return void
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }

        $headers = [];

        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ": " . $value;
        }

        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Get curl response
     *
     * @return void
     */
    public function getResponse()
    {
        return $this->resonse;
    }

    private function init()
    {
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }
}
