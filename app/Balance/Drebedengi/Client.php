<?php

declare(strict_types=1);

namespace App\Balance\Drebedengi;

class Client
{
    /**
     * @var string E-mail.
     */
    protected $email;
    /**
     * @var string Password.
     */
    protected $password;

    /**
     * @var \SoapClient SOAP client.
     */
    private $client;

    /**
     * @var string WSDL URL.
     */
    private static $wsdl = 'http://www.drebedengi.ru/soap/dd.wsdl';

    /**
     * Client constructor.
     *
     * @param string $email    E-mail.
     * @param string $password Password.
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
        $this->client = new \SoapClient(static::$wsdl, [
            'soap_version' => \SOAP_1_2,
            'cache_wsdl'   => \WSDL_CACHE_MEMORY,
            'compression'  => \SOAP_COMPRESSION_ACCEPT,
            'trace'        => true,
            'exceptions'   => true,
            'keep_alive'   => true
        ]);
    }

    /**
     * Get balance.
     *
     * @param array $params Parameters.
     *
     * @return array
     *
     * @throws \SoapFault
     */
    public function getBalance(array $params = []): array
    {
        return $this->request(__FUNCTION__, $params);
    }

    /**
     * Get currencies list.
     *
     * @return array
     *
     * @throws \SoapFault
     */
    public function getCurrencyList(): array
    {
        return $this->request(__FUNCTION__);
    }

    /**
     * Get category list.
     *
     * @return array
     *
     * @throws \SoapFault
     */
    public function getCategoryList(): array
    {
        return $this->request(__FUNCTION__);
    }

    /**
     * Get sources list.
     *
     * @return array
     *
     * @throws \SoapFault
     */
    public function getSourceList(): array
    {
        return $this->request(__FUNCTION__);
    }

    /**
     * Get places list.
     *
     * @return array
     *
     * @throws \SoapFault
     */
    public function getPlaceList(): array
    {
        return $this->request(__FUNCTION__);
    }

    /**
     * Set records list.
     *
     * @param array $records Records list.
     *
     * @return array
     *
     * @throws \SoapFault
     */
    public function setRecordList(array  $records): array
    {
        return $this->request(__FUNCTION__, $records);
    }

    /**
     * Request to service.
     *
     * @param string $method Method name.
     * @param array  $params Parameters.
     *
     * @return mixed
     *
     * @throws \SoapFault
     */
    private function request($method, array $params = [])
    {
        try {
            return $this->client->$method(\env('DREBEDENGI_API_KEY'), $this->email, $this->password, $params);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\SoapFault $e) {
            /** @noinspection PhpUndefinedMethodInspection */
            $filename = \Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
            \file_put_contents($filename . 'soap-request.xml', $this->client->__getLastRequest());
            \file_put_contents($filename . 'soap-response.xml', $this->client->__getLastResponse());
            throw $e;
        }
    }
}
