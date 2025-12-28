<?php 

namespace WcAppSheet\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AppSheetClient 
{
    protected Client $cliente;

    public function __construct()
    {
        $this->cliente = new Client([
            'base_uri' => 'https://api.appsheet.com/api/v2/apps/9108d0fb-e52e-4c24-964a-fafd72d15ef0/',
            'timeout'  => 10,
        ]);
    }

    public function sendOrder(array $data)
    {
        return $this->cliente->post('tables/Orders/Action', [
            'headers' => [
                'ApplicationAccessKey' => 'V2-sD0oB-7ESd4-PR2Ym-zKrhW-ZhSJc-TP7rH-FSkfO-w0eey',
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