<?php

declare(strict_types=1);

namespace App\Controller\API;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends BaseController
{
    protected string $authServer;

    protected string $authClientId;

    protected string $authClientSecret;

    protected function preExecute(Request $request, Response $response): void
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
     * @param array<string, mixed> $args
     */
    public function executeToken(Request $request, Response $response, array $args): Response
    {
        $meta = ['name' => 'Authorization Token'];

        $requestTokenResponse = $this->requestToken();

        $rawData   = $requestTokenResponse->getBody()->getContents();
        $tokenData = json_decode($rawData, true);

        return $response->withJson([
            'meta' => $meta,
            'data' => $tokenData,
        ]);
    }

    /**
     * @link https://docs.aws.amazon.com/ja_jp/cognito/latest/developerguide/token-endpoint.html
     */
    protected function requestToken(): ResponseInterface
    {
        $endpoint = '/oauth2/token';
        $headers  = [
            'Authorization' => $this->createAuthStr(),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        $params   = ['grant_type' => 'client_credentials'];

        $httpClient = $this->createHttpClient();

        return $httpClient->post($endpoint, [
            'headers' => $headers,
            'form_params' => $params,
        ]);
    }

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

    protected function createAuthStr(): string
    {
        $clientId     = $this->authClientId;
        $clientSecret = $this->authClientSecret;

        return 'Basic ' . base64_encode($clientId . ':' . $clientSecret);
    }
}
