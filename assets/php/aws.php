<?php

require 'C:\Users\Brice\Documents\xampp\htdocs\vendor\aws-autoloader.php';
use Aws\Ses\SesClient;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;

$ini = parse_ini_file('../../bin/config.ini');
if ($ini['debug']) {error_reporting(E_ALL);} else {error_reporting(0);}
set_include_path($ini['include_path']);

function sendEmail($params) {
    $ini = parse_ini_file('../../bin/config.ini');
    $credentials = new Aws\Credentials\Credentials($ini['accessKey'], $ini['secretToken']);
    $ses = new SesClient([
        'version' => 'latest',
        'region' => 'us-east-1',
        'credentials' => $credentials
    ]);
    try {
        $result = $ses->sendEmail($params);
        $messageId = $result['MessageId'];
        echo "success";
        return true;
    } catch (AwsException $e) {
        echo "err_aws_ses_error";
        trigger_error($e->getAwsErrorMessage());
    } catch (Exception $e) {
        echo "err_unknown_error";
        trigger_error($e->error());
    }
}

function uploadFileToS3($file, $key, $acl) {
    $ini = parse_ini_file('../../bin/config.ini');
    $credentials = new Aws\Credentials\Credentials($ini['accessKey'], $ini['secretToken']);
    $s3 = new S3Client(['version' => 'latest','region'  => 'us-east-1','credentials' => $credentials]);

    try {
        $result = $s3->putObject([
            'Bucket' => $ini['bucket'],
            'Key' => $key,
            'ACL' => $acl,
            'Body' => $file
        ]);

        return true;
    } catch (S3Exception $e) {
        return $e;
    }
}