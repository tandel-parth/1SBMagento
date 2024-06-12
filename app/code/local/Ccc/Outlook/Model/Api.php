<?php
class Ccc_Outlook_Model_Api extends Mage_Core_Model_Abstract
{
    protected $_configObj = null;

    private $tenantId = 'common';
    public function getAuthorizationUrl()
    {
        $configuration = $this->_configObj;
        $authorizationEndpoint = sprintf(
            "https://login.microsoftonline.com/%s/oauth2/v2.0/authorize",
            $this->tenantId
        );
        $authUrl = sprintf(
            "%s?client_id=%s&response_type=code&redirect_uri=%s&scope=%s",
            $authorizationEndpoint,
            $configuration->getClientId(),
            urlencode($configuration->getRedirectUrl() . $configuration->getId()),
            urlencode($configuration->getScope())
        );
        return $authUrl;
    }
    public function getAccessToken($authorizationCode)
    {
        var_dump($authorizationCode);
        $configuration = $this->_configObj;
        $tokenEndpoint = sprintf(
            "https://login.microsoftonline.com/%s/oauth2/v2.0/token",
            $this->tenantId
        );
        $data = [
            'client_id' => $configuration->getClientId(),
            'client_secret' => $configuration->getClientSecret(),
            'code' => $authorizationCode,
            'redirect_uri' => $configuration->getRedirectUrl() . $configuration->getId(),
            'grant_type' => 'authorization_code',
            // 'grant_type' => 'client_credentials',
            'scope' => $configuration->getScope(),
        ];
        $ch = curl_init($tokenEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('Error fetching access token: ' . curl_error($ch));
        }
        curl_close($ch);
        $result = json_decode($response, true);
        if (isset($result['error'])) {
            throw new Exception('Error in response: ' . $result['error_description']);
        }
        return $result['access_token'];
    }

    private function handleError($error)
    {
        print_r($error);
        switch ($error['code']) {
            case 'InvalidAuthenticationToken':
                print_r('Error: Invalid authentication token');
                break;
            case 'InvalidGrant':
                print_r('Error: Invalid grant. This may be due to an expired authorization code.');
                break;
            case 'ServiceNotAvailable':
                print_r('Error: Microsoft service is currently unavailable.');
                break;
            default:
                Mage::log('Error: ' . $error['message'], null, 'outlook_emails.log');
                break;
        }
    }
    public function saveTokenToFile($data)
    {
        $configuration = $this->_configObj;
        $filePath = Mage::getBaseDir('var') . DS . 'export' . DS . $configuration->getId() . '.txt';
        try {
            $io = new Varien_Io_File();
            $io->setAllowCreateFolders(true);
            $exportDir = Mage::getBaseDir('var') . DS . 'export';
            if (!is_dir($exportDir)) {
                $io->mkdir($exportDir, 0755, true);
            }
            $io->open(array('path' => $exportDir));
            $io->streamOpen($filePath, 'w+');
            $io->streamLock(true);
            $io->streamWrite($data);
            $io->streamUnlock();
            $io->streamClose();
            return true;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

    public function setConfigModel($obj)
    {
        $this->_configObj = $obj;
    }
    public function getConfigModel()
    {
        return $this->_configObj;
    }
    public function getEmails()
    {
        $accessToken = $this->readTokenFromFile();
        $baseUrl = "https://graph.microsoft.com/v1.0/me/messages";
        $baseUrl .= '?receivedDateTime,hasAttachments,bodyPreview,from,subject';
        $baseUrl .= '&top=2';
        $baseUrl .= '&orderby=receivedDateTime';
        $lastReceivedData = $this->getConfigModel()->getLastReadedEmail();
        if ($lastReceivedData) {
            $formattedDate = new DateTime($lastReceivedData);
            $formattedDate = $formattedDate->modify('+1 second');
            $baseUrl .= '&filter=' . urlencode('receivedDateTime gt ' . $formattedDate->format('Y-m-d\TH:i:s\Z'));
        }
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = $this->parseMails(json_decode($response, true));
        return $data;
    }
    public function readTokenFromFile()
    {
        $id = $this->getConfigModel()->getConfigrationId();
        $filePath = Mage::getBaseDir('var') . DS . 'export' . DS . $id . '.txt';
        try {
            $io = new Varien_Io_File();
            if ($io->fileExists($filePath)) {
                $io->open(array('path' => Mage::getBaseDir('var') . DS . 'export'));
                $data = $io->read($filePath);
                return $data;
            } else {
                return 'File does not exist.';
            }
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }
    public function parseMails($emails)
    {
        if ($emails['error']) {
            $this->handleError($emails['error']);
        }
        $emailValue = $emails['value'];
        if ($emailValue) {
            $parsedEmails = array();
            foreach ($emailValue as $email) {
                $toAddresses = isset($email['toRecipients'])
                    ? array_map(function ($recipient) {
                        return $recipient['emailAddress']['address'] ?? 'N/A';
                    }, $email['toRecipients']) : [];
                $to = implode(', ', $toAddresses);
                $parsedEmail = array(
                    'id' => $email['id'],
                    'createdDateTime' => $email['receivedDateTime'],
                    'from' => isset($email['from']['emailAddress']['address'])
                        ? $email['from']['emailAddress']['address'] : '',
                    'to' => $to,
                    'subject' => isset($email['subject']) ? $email['subject'] : '',
                    'body' => isset($email['body']['content'])
                        ? trim(strip_tags($email['body']['content'])) : 'No Content',
                    'has_attachments' => isset($email['hasAttachments']) && $email['hasAttachments']
                        ? true : false
                );
                $parsedEmails[] = $parsedEmail;
            }
            print_r($parsedEmails);
            return $parsedEmails;
        }
    }
    public function downloadAttachments($params)
    {
        $messageId = $params['message_id'];
        $url = "https://graph.microsoft.com/v1.0/me/messages/{$messageId}/attachments";
        $headers = [
            'Authorization: Bearer ' . $params['accesstoken'],
            'Accept: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $attachments = json_decode($response, true);
        $downloadedAttachments = [];
        foreach ($attachments['value'] as $attachment) {
            if (isset($attachment['contentBytes'])) {
                $fileName = $attachment['name'];
                $this->saveAttachment($this->getConfigModel(), $attachment, $params['email_id']);
                $downloadedAttachment[] = [
                    'name' => $fileName,
                ];
            }
            $downloadedAttachments[] = $downloadedAttachment;
        }
        return $downloadedAttachments;
    }
    public function saveAttachment(Ccc_Outlook_Model_Configuration $configuration, $attachment, $emailId)
    {
        $filePath = Mage::getBaseDir('var') . DS . 'attachment' . DS . $configuration->getId() . DS . $emailId . DS . $attachment['name'];
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        file_put_contents($filePath, base64_decode($attachment['contentBytes']));
    }
    public function buildMailUrl($baseUrl, $params = [])
    {
        return $baseUrl . '?' . http_build_query($params);
    }
}
