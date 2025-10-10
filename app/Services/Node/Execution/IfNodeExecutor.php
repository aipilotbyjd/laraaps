<?php

namespace App\Services\Node\Execution;

class IfNodeExecutor extends NodeExecutor
{
    public function execute(array $inputData = [])
    {
        $properties = $this->node->properties;

        $condition = $properties['condition']; // e.g., "{{ $json.status }} == 'success'"

        // A simple evaluation of the condition. A more robust solution would use a proper expression language.
        $result = false;
        try {
            $result = eval('return '.$this->replacePlaceholders($condition, $inputData).';');
        } catch (\Throwable $th) {
            // ignore
        }

        return [
            '__branch' => $result ? 'true' : 'false',
            'data' => $inputData,
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
