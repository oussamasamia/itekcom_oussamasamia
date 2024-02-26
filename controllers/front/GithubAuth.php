<?php
require_once(_PS_MODULE_DIR_ . 'itekcom_oussamasamia/classes/CustomerGithub.php');

use \Itekcom_Oussamasamia\CustomerGithub;

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
        $clientId = Configuration::get('ITEKCOM_OUSSAMASAMIA_CLIENT_ID');
        $clientSecret = Configuration::get('ITEKCOM_OUSSAMASAMIA_CLIENT_SECRET');




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

        if ($accessToken) {
            $data = $this->getUserInfo($accessToken);
            $this->signInOrSignUpUser($data);
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


    public function signInOrSignUpUser($userInfo)
    {
        // Check if the user exists in your system
        $userExists = CustomerGithub::checkIfUserExists($userInfo['id']);


        if ($userExists) {
            // User exists, sign them in
            $this->signInUser($userInfo);
        } else {
            // User does not exist, sign them up
            $result = $this->signUpUser($userInfo['id'], $userInfo['login']);
        }
        var_dump($result);
        die();
    }


    public function signUpUser($githubUserId, $githubUserLogin)
    {
        // Create a new Customer object
        $customer = new CustomerGithub();
        // Set the customer's data
        $customer->email = $this->generateEmailFromGitHubLogin($githubUserLogin);
        $customer->firstname = $githubUserLogin;
        $customer->lastname = $githubUserLogin;
        $customer->github_id = $githubUserId;
        $customer->passwd = Tools::encrypt(Tools::passwdGen()); // Generate a random password

        // Save the customer
        if ($customer->save()) {
            return $customer; // Return the newly created customer object
        } else {
            return false; // Return false if the customer creation fails
        }
    }


    private function generateEmailFromGitHubLogin($githubLogin, $domain = 'github.com')
    {
        // Replace any characters in the GitHub login username that are not allowed in email addresses
        $githubLogin = preg_replace('/[^a-z0-9_.-]/i', '', $githubLogin);

        // Generate the email address by combining the GitHub login username with the domain
        $email = $githubLogin . '@' . $domain;

        return $email;
    }

    public function signInUser($userInfo)
    {
        // Check if the user exists based on GitHub ID
        $id = CustomerGithub::getIdByGithubId($userInfo['id']);
        $customer = new Customer($id);

        if ($customer) {
            // User already exists, sign them in
            $this->setupUserSession($customer);
        }
    }

    private function setupUserSession($customer)
    {
        $this->context->updateCustomer($customer);

        Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);

        // Login information have changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);

        // Redirect the user to home page
        Tools::redirect('index.php');
    }


}
