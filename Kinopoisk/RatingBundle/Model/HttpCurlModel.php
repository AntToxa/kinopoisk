<?php

namespace Kinopoisk\RatingBundle\Model;

/**
 * ������ ��� ������ � curl
 */
class HttpCurlModel
{
    /**
     * ������������� curl
     * @var resource
     */
    private $req;

    /**
     * ������������ ��������
     * @var mixed
     */
    private $data;

    /**
     * Url
     * @var string
     */
    private $url;

    /**
     * ����� �������� ������
     * @var string
     */
    private $requestMethod;

    /** ����� �������
     * @var string
     */
    private $response;

    public function __construct()
    {
        $this->req = curl_init();
        $this->returnHeaders(false);
        curl_setopt_array(
            $this->req,
            array(
                 CURLOPT_POST => false,
                 CURLOPT_HEADER => false,
                 CURLOPT_RETURNTRANSFER => true,
                 CURLOPT_FOLLOWLOCATION => false,
                 CURLINFO_HEADER_OUT => true,
                 CURLOPT_FILETIME => true
            )
        );
    }

    /**
     * ������������� ��������� ����������
     * @param $opt - ���������
     * @param $val - ��������
     */
    private function setCurlOpt($opt, $val)
    {
        curl_setopt($this->req, $opt, $val);
    }

    /**
     * ������������� ����� �������
     * @param $type - �����
     * @return HttpCurlBundle
     */
    public function setRequestMethod($type)
    {
        switch ($type) {
            case 'post':
                $this->requestMethod = 'post';
                $this->setCurlOpt(CURLOPT_POST, true);
                break;
            case 'get':
                $this->requestMethod = 'get';
                $this->setCurlOpt(CURLOPT_HTTPGET, true);
                break;
            case 'head':
                $this->requestMethod = 'head';
                $this->setRequestMethod('get');
                $this->setCurlOpt(CURLOPT_NOBODY, true);
                $this->setCurlOpt(CURLOPT_HEADER, true);
                break;
            case 'img':
                $this->setRequestMethod('get');
                $this->setCurlOpt(CURLOPT_HEADER, false);
                $this->setCurlOpt(CURLOPT_RETURNTRANSFER, true);
                break;
            default:
                $this->setCurlOpt(CURLOPT_HTTPGET, true);
        }
        return $this;
    }

    /**
     * ���������� �������������� ���������
     *
     * @return array
     */
    public function getRequestHeaders()
    {
        return curl_getinfo($this->req, CURLINFO_HEADER_OUT);
    }


    /**
     * ���������� ���� ����������� ���������� ���������
     *
     * @return string
     */
    public function getResponseMtime()
    {
        if ($this->getResponseCode() == 200) {
            return curl_getinfo($this->req, CURLINFO_FILETIME);
        } else {
            return -1;
        }
    }

    /**
     * ���������� ��� ������
     *
     * @return int
     */
    public function getResponseCode()
    {
        return curl_getinfo($this->req, CURLINFO_HTTP_CODE);
    }

    /**
     * ������������� ����� �������
     *
     * @param string $url
     * @return HttpCurlBundle
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * ������������� ������ ��� ��������
     *
     * @param array $arr
     * @return HttpCurlBundle
     */
    public function setData($arr)
    {
        $this->data = $arr;

        return $this;
    }

    /**
     * ��������� �� ��������� � ������
     *
     * @param bool $bool
     * @return HttpCurlBundle
     */
    public function returnHeaders($bool)
    {
        $this->setCurlOpt(CURLOPT_HEADER, $bool);

        return $this;
    }


    /**
     * �������� ������
     *
     * @return HttpCurlBundle
     */
    public function sendRequest()
    {
        $data = $this->prepareArray();
        switch ($this->requestMethod) {
            case 'get':
            case 'head':
                $this->setUrl($this->url . $data);
                break;
            case 'post':
                $this->setCurlOpt(CURLOPT_POSTFIELDS, $data);
                break;
        }
        $this->setCurlOpt(CURLOPT_URL, $this->url);
        $this->response = curl_exec($this->req);

        return $this;
    }

    /**
     * �������� �����
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function __destruct()
    {
        curl_close($this->req);
    }

    /**
     * ���������� ������� ��� �������� post
     * @param $var - ������ � �����������
     * @param bool|string $prefix - �������
     * @return array
     */
    public function arrayToPost($var, $prefix = false)
    {
        $return = array();
        foreach ($var as $idx => $value) {
            if (is_scalar($value)) {
                if (isset($prefix) && $prefix) {
                    $return[$prefix . '[' . $idx . ']'] = $value;
                } else {
                    $return[$idx] = $value;
                }
            } else {
                $return = array_merge(
                    $return,
                    $this->arrayTopost($value, isset($prefix) && $prefix ? $prefix . '[' . $idx . ']' : $idx)
                );
            }
        }

        return $return;
    }

    /**
     * �������� ������ ����� get
     * @param $var - ������ �� ����������
     * @return string
     */
    private function arrayToGet($var)
    {
        if (is_array($var)) {
            return http_build_query($var);
        } else {
            return '';
        }
    }

    /**
     * �������������� ������ ��� �������� get ��� post
     * @return array|string
     */
    private function prepareArray()
    {
        switch ($this->requestMethod) {
            case 'get':
                return $this->arrayToGet($this->data);
                break;
            case 'post':
                return $this->arrayToPost($this->data);
                break;
        }
    }

}
