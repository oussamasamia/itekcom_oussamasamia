<?php


class Itekcom_OussamasamiaGithubAuthModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
    }


    public function init()
    {
        parent::init();

        if (isset($_GET['code']) && $_SERVER['REQUEST_METHOD'] === 'GET') {

            $code = $_GET['code'];

            //exchange code to access token
            $this->exchangeCodeToAccessToken($code);

        }

    }

    public function exchangeCodeToAccessToken($code)
    {
        //GitHub app's client ID and secret
        $clientId = 'ea8a7f19df16f8f988e8';
        $clientSecret = 'xxx';


        // Parameters for exchanging the authorization code for an access token
        $params = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
        ];

        // GitHub token endpoint URL
        $tokenEndpoint = 'https://github.com/login/oauth/access_token';

        // Make a POST request to the token endpoint
        $response = $this->sendPostRequest($tokenEndpoint, $params);

        // Process the response to extract the access token
        $accessToken = $this->parseAccessToken($response);

        if ($accessToken){
            $data = $this->getUserInfo($accessToken);
            var_dump($data);die();
        }

    }

    private function sendPostRequest($url, $data)
    {
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }

    private function parseAccessToken($response)
    {
        parse_str($response, $parsedResponse);
        if (isset($parsedResponse['access_token'])) {
            return $parsedResponse['access_token'];
        } else {
            // Handle the case when the access token is not found in the response
            return null;
        }
    }

    public function getUserInfo($accessToken)
    {
        // GitHub user endpoint URL
        $userUrl = 'https://api.github.com/user';

        // Headers for the GET request
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'User-Agent: YourApp/1.0', // Replace with your app's name and version
        ];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $userUrl,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        // Execute cURL request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            // Handle cURL error
            $error = curl_error($ch);
            // Log or display the error
        } else {
            // Decode the JSON response
            $userInfo = json_decode($response, true);
            // Now you have the user information
            return $userInfo;
        }

        // Close cURL session
        curl_close($ch);
    }





}
