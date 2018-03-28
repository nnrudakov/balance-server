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
     */
    public function getBalance(array $params = []): array
    {
        return $this->request(__FUNCTION__, $params);
    }

    /**
     * Get currencies list.
     *
     * @return array
     */
    public function getCurrencyList(): array
    {
        return $this->request(__FUNCTION__);
    }

    /**
     * Request to service.
     *
     * @param string $method Method name.
     * @param array  $params Parameters.
     *
     * @return mixed
     */
    private function request($method, array $params = [])
    {
        return $this->client->$method(\env('DREBEDENGI_API_KEY'), $this->email, $this->password, $params);
    }
}
