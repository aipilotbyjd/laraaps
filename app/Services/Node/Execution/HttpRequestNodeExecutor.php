<?php

namespace App\Services\Node\Execution;

use Illuminate\Support\Facades\Http;

class HttpRequestNodeExecutor extends NodeExecutor
{
    public function execute(array $inputData = [])
    {
        $properties = $this->node->properties;

        // Simple templating: replace placeholders like {{ $json.key }}
        $url = $this->replacePlaceholders($properties['url'], $inputData);
        $method = $properties['method'] ?? 'get';
        $headers = $this->replacePlaceholders($properties['headers'] ?? [], $inputData);
        $body = $this->replacePlaceholders($properties['body'] ?? [], $inputData);

        $response = Http::withHeaders($headers)->$method($url, $body);

        return [
            'status' => $response->status(),
            'headers' => $response->headers(),
            'body' => $response->json() ?? $response->body(),
        ];
    }

    private function replacePlaceholders($data, array $inputData)
    {
        if (is_string($data)) {
            return $this->replaceStringPlaceholders($data, $inputData);
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->replacePlaceholders($value, $inputData);
            }
        }

        return $data;
    }

    private function replaceStringPlaceholders(string $string, array $inputData): string
    {
        return preg_replace_callback('/{{\s*\$json\.([^\s}]+)\s*}}/', function ($matches) use ($inputData) {
            return data_get($inputData, $matches[1]);
        }, $string);
    }
}
