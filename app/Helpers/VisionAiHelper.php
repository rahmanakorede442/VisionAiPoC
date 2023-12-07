<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class VisionAiHelper{

    protected $apiKey;

    public function __construct(public string $command)
    {
        $this->apiKey = env("OPENAI_API_KEY");
    }

    protected function encode_image(string $imagePath)
    {
        $image = fopen($imagePath, 'rb');
        $imageData = fread($image, filesize($imagePath));
        fclose($image);

        return base64_encode($imageData);
    }

    public function generate_response($imagePath)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$this->apiKey}",
        ];
        $url = 'https://api.openai.com/v1/chat/completions';
        $dataUri = sprintf('data:image/jpeg;base64,%s',$this->encode_image($imagePath));

        $payload = [
            'model' => 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $this->command,
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $dataUri,
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => 300,
        ];

        return $response = $this->guzzle_req( $headers,$payload, $url);
    }

    protected function guzzle_req($headers, $payload, $url)
    {
        $client = new Client();

        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $payload,
          ]);

        $result = json_decode($response->getBody(), true)['choices'][0]['message']['content'];

        return $data = json_decode($result,true);
    }   
}