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

        /**
     * Verifica si una fila con el ID dado existe en la tabla de AppSheet
     */
    public function existsInAppSheet($id, string $table = 'Orders')
    {
        try {
            $response = $this->cliente->post("tables/{$table}/find", [
                'headers' => [
                    'ApplicationAccessKey' => $this->accessKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'Rows' => [ [ 'id' => $id ] ],
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            error_log('AppSheet existsInAppSheet respuesta: ' . print_r($body, true));
            // Si la respuesta es un array numérico (como muestra el log), existe si hay elementos
            if (is_array($body) && array_keys($body) === range(0, count($body) - 1) && count($body) > 0) {
                return true;
            }
            // Si la respuesta es un array asociativo con 'Rows', existe si hay elementos en 'Rows'
            if (isset($body['Rows']) && is_array($body['Rows']) && count($body['Rows']) > 0) {
                return true;
            }
            return false;
        } catch (RequestException $e) {
            error_log('AppSheet existsInAppSheet error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendData(array $data, string $table = 'Orders')
    {
        // Si $data es un solo objeto, lo convertimos en array de uno
        $rows = isset($data[0]) ? $data : [$data];
        return $this->cliente->post("tables/{$table}/Action", [
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
                'Rows' => $rows,
            ],
        ]);
    }

    public function editData(array $data, string $table = 'Orders')
    {
        // Si $data es un solo objeto, lo convertimos en array de uno
        $rows = isset($data[0]) ? $data : [$data];
        return $this->cliente->post("tables/{$table}/Action", [
            'headers' => [
                'ApplicationAccessKey' => $this->accessKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'Action' => 'Edit',
                'Properties' => [
                    'Locale' => 'es-ES',
                    'Timezone' => 'UTC',
                ],
                'Rows' => $rows,
            ],
        ]);
    }

    public function deleteData(array $data, string $table = 'Orders')
    {
        // Si $data es un solo objeto, lo convertimos en array de uno
        $rows = isset($data[0]) ? $data : [$data];
        return $this->cliente->post("tables/{$table}/Action", [
            'headers' => [
                'ApplicationAccessKey' => $this->accessKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'Action' => 'Delete',
                'Properties' => [
                    'Locale' => 'es-ES',
                    'Timezone' => 'UTC',
                ],
                'Rows' => $rows,
            ],
        ]);
    }
}