<?php

declare(strict_types=1);

namespace Yuxin\Feishu;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yuxin\Feishu\Exceptions\HttpException;

class HttpClient
{
    protected const BASE_URI = 'https://open.feishu.cn/open-apis/';

    protected ?Client $client = null;

    public function __construct(protected array $guzzleOptions = []) {}

    public function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client(array_merge($this->guzzleOptions, [
                'base_uri' => self::BASE_URI,
            ]));
        }

        return $this->client;
    }

    public function setOptions(array $options): void
    {
        $this->guzzleOptions = $options;
        // Reset client to apply new options
        $this->client = null;
    }

    public function getOptions(): array
    {
        return $this->guzzleOptions;
    }

    public function request(string $method, string $uri, array $options = [])
    {
        try {
            return $this->getClient()->request($method, $uri, $options);
        } catch (GuzzleException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function get(string $uri, array $options = [])
    {
        return $this->request('GET', $uri, $options);
    }

    public function post(string $uri, array $options = [])
    {
        return $this->request('POST', $uri, $options);
    }

    public function put(string $uri, array $options = [])
    {
        return $this->request('PUT', $uri, $options);
    }

    public function delete(string $uri, array $options = [])
    {
        return $this->request('DELETE', $uri, $options);
    }
}
