<?php 

namespace WcAppSheet\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AppSheetClient 
{
    protected Client $cliente;
    protected string $accessKey;
    protected string $appId;

    public function __construct()
    {
        $this->accessKey = get_option('wc_appsheet_access_key', '');
        $this->appId = get_option('wc_appsheet_app_id', '');
        $this->cliente = new Client([
            'base_uri' => 'https://api.appsheet.com/api/v2/apps/' . $this->appId . '/',
            'timeout'  => 10,
        ]);
    }

    public function sendOrder(array $data)
    {
        return $this->cliente->post('tables/Orders/Action', [
            'headers' => [
                'ApplicationAccessKey' => $this->accessKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'Action' => 'Add',
                'Properties' => [
                    'Locale' => 'es-ES',
                    'Timezone' => 'UTC',
                ],
                'Rows' => [$data],
            ],
        ]);
    }
}