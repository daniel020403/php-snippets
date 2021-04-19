<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class Elasticsearch {

    protected $host;
    protected $http_client;

    public function __construct($host) {
        $this->host             = $host;
        $this->http_client      = new Client([
            "base_uri"      => $this->host,
            "verify"        => false
        ]);
    }

    public function search($index, $body) {
        $uri        = "/" . $index . "/_search";
        $headers    = [
            "Content-Type"    => "application/json"
        ];

        return $this->http_client->request("GET", $uri, [
            "headers"       => $headers,
            "body"          => json_encode($body)
        ]);
    }

}
