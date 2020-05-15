<?php

namespace App\SES;


use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use App\SES\SimpleEmailService;
use App\SES\SimpleEmailServiceMessage;

class SESMail
{
  public $sesClient;

  private $receiver;
  private $body;
  private $subject;

  public function __construct($receiver, $subject, $body, $cc=null) 
  {
    $this->receiver = $receiver;
    $this->body = $body;
    $this->subject =  $subject;

    $this->sesClient = new SesClient([
      'version' => '2010-12-01',
      'region'  => env('AWS_DEFAULT_REGION'),
      'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY')
      ]
    ]);
  }

  public function sendEmailToVerifiedEmail() 
  {
    // try {
    //     $result = $this->sesClient->verifyEmailIdentity([
    //         'EmailAddress' => $this->receiver
    //     ]);
    //     var_dump($result);
    // } catch (AwsException $e) {
    //     echo $e->getMessage();
    //     echo "\n";
    // }
    $recipient_emails = [$this->receiver];
    $configuration_set = 'clientDomain-config';

    $subject = 'Amazon SES test (AWS SDK for PHP)';
    $plaintext_body = 'This email was sent with Amazon SES using the AWS SDK for PHP.' ;
    $html_body =  '<h1>AWS Amazon Simple Email Service Test Email</h1>'.
                  '<p>This email was sent with <a href="https://aws.amazon.com/ses/">'.
                  'Amazon SES</a> using the <a href="https://aws.amazon.com/sdk-for-php/">'.
                  'AWS SDK for PHP</a>.</p>';
    $char_set = 'UTF-8';

    try {
        $result = $this->sesClient->sendEmail([
            'Destination' => [
                'ToAddresses' => $recipient_emails,
            ],
            'ReplyToAddresses' => [$this->receiver],
            'Source' => $this->receiver,
            'Message' => [
              'Body' => [
                  'Html' => [
                      'Charset' => $char_set,
                      'Data' => $html_body,
                  ],
                  'Text' => [
                      'Charset' => $char_set,
                      'Data' => $plaintext_body,
                  ],
              ],
              'Subject' => [
                  'Charset' => $char_set,
                  'Data' => $subject,
              ],
            ],
            // If you aren't using a configuration set, comment or delete the
            // following line
            'ConfigurationSetName' => $configuration_set,
        ]);
        $messageId = $result['MessageId'];
        echo("Email sent! Message ID: $messageId"."\n");
    } catch (AwsException $e) {
        // output error message if fails
        echo "\n";
        echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
        echo "\n";
    }
  }

  public function sendEmail(){
    $ses = new SimpleEmailService(env('AWS_ACCESS_KEY_ID'), env('AWS_SECRET_ACCESS_KEY'));
    $m = new SimpleEmailServiceMessage();
    $m->addTo($this->receiver);
    // $m->setFrom('romansiryd@gmail.com');
    $m->setFrom(env('ADMIN_MAIL')?env('ADMIN_MAIL'):"help@clientDomain.app");
    $m->setSubject($this->subject);
    $m->setMessageFromString(null, $this->body);

    return $ses->sendEmail($m);
  }
}

