<?php

namespace App\Services\Node\Execution;

use App\Mail\WorkflowEmail;
use Illuminate\Support\Facades\Mail;

class EmailNodeExecutor extends NodeExecutor
{
    public function execute(array $inputData = [])
    {
        $properties = $this->node->properties;

        $to = $this->replacePlaceholders($properties['to'], $inputData);
        $subject = $this->replacePlaceholders($properties['subject'], $inputData);
        $body = $this->replacePlaceholders($properties['body'], $inputData);

        Mail::to($to)->send(new WorkflowEmail($body));

        return $inputData;
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
