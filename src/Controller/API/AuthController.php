<?php

namespace Toei\PortalAdmin\Controller\API;

use GuzzleHttp\Client as HttpClient;

/**
 * Auth controller
 */
class AuthController extends BaseController
{
    /** @var string */
    protected $authServer;

    /** @var string */
    protected $authClientId;

    /** @var string */
    protected $authClientSecret;

    /**
     * pre execute
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @return void
     */
    protected function preExecute($request, $response): void
    {
        $settings = $this->settings['api'];

        $this->authServer       = $settings['auth_server'];
        $this->authClientId     = $settings['auth_client_id'];
        $this->authClientSecret = $settings['auth_client_secret'];

        parent::preExecute($request, $response);
    }

    /**
     * token action
     *
     * @link https://m-p.backlog.jp/view/TOEI-112
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeToken($request, $response, $args)
    {
        $meta = ['name' => 'Authorization Token'];
        $this->data->set('meta', $meta);

        $response = $this->requestToken();

        $rawData = $response->getBody()->getContents();
        $data    = json_decode($rawData, true);

        $this->data->set('data', $data);
    }

    /**
     * request Token
     *
     * @link https://docs.aws.amazon.com/ja_jp/cognito/latest/developerguide/token-endpoint.html
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function requestToken(): \Psr\Http\Message\ResponseInterface
    {
        $endpoint = '/oauth2/token';
        $headers  = [
            'Authorization' => $this->createAuthStr(),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        $params   = ['grant_type' => 'client_credentials'];

        $httpClient = $this->createHttpClient();
        $response   = $httpClient->post($endpoint, [
            'headers' => $headers,
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * create HTTP Client
     *
     * @return HttpClient
     */
    protected function createHttpClient(): HttpClient
    {
        $config = [
            'timeout' => 5, // ひとまず5秒
            'connect_timeout' => 5, // ひとまず5秒
            'http_errors' => true,
        ];

        $config['base_uri'] = 'https://' . $this->authServer;

        return new HttpClient($config);
    }

    /**
     * create Authorization string
     *
     * @return string
     */
    protected function createAuthStr(): string
    {
        $clientId     = $this->authClientId;
        $clientSecret = $this->authClientSecret;

        return 'Basic ' . base64_encode($clientId . ':' . $clientSecret);
    }
}
