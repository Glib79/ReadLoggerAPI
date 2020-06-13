<?php
declare(strict_types=1);

namespace App\Support;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use UnexpectedValueException;

class SendEmail
{
    public const TEMPLATE_CONFIRM_EMAIL = 'confirmEmail';
    
    private const EMAIL_FROM = 'grzegorz.libera+support@gmail.com';
    /** @todo domain should be in config? */
    private const CONFIRM_LINK = 'http://localhost:3000/email-confirm/%s';
    
    /**
     * @var MailerInterface 
     */
    private $mailer;
    
    /**
     * SendEmail interface
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    
    /**
     * Sends email to recipient
     * @param array $emailParams email parameters to, subject
     * @param array $messageParams message parameters message template, language, data to put inside message
     * @return void
     */
    public function sendEmail(array $emailParams, array $messageParams): void
    {
        if (empty($emailParams['to'])) {
            throw new UnexpectedValueException('Param to can not be empty!');
        }

        if (empty($messageParams['template']) || empty($messageParams['language'])) {
            throw new UnexpectedValueException('Param template and language can not be empty!');
        }
        
        $messageParams['email'] = $emailParams['to'];
        
        $message = $this->prepareMessage($messageParams['template'], $messageParams);
        
        $email = (new Email())
            ->from(self::EMAIL_FROM)
            ->to($emailParams['to'])
            ->subject($message['subject'])
            ->text($message['text'])
            ->html($message['html']);

        $this->mailer->send($email);        
    }
    
    /**
     * Prepare message based on template
     * @param string $template
     * @param array $params
     * @return array
     */
    private function prepareMessage(string $template, array $params): array
    {
        switch ($template) {
            case self::TEMPLATE_CONFIRM_EMAIL:
                return $this->prepareConfirmEmail($params);
            default:
                throw new UnexpectedValueException('Wrong template!');
        }
    }
    
    /**
     * Prepare confirmation email
     * @param array $params
     * @return array
     */
    private function prepareConfirmEmail(array $params): array
    {
        /* @todo think about emails and templates */
        $link = sprintf(
            self::CONFIRM_LINK,
            base64_encode(sprintf('%s %s', $params['email'], $params['token']))
        );
        
        switch ($params['language']) {
            case 'en':
                $subject = 'Welcome to the Read Logger Service!';
                
                $messageText = sprintf("Welcome to the our service, \r\n"
                    . "We are very happy that you joined to our society.\r\n \r\n "
                    . "Please confirm your email by clicking link below:\r\n"
                    . "%s \r\n \r\n"
                    . "Welcome,\r\n"
                    . "Read Logger Team \r\n\r\n"
                    . "If you did not sign up to Read Logger service please ignore this email.",
                    $link
                );

                $messageHtml = sprintf('Welcome to the our service, <br />'
                    . 'We are very happy that you joined to our society.<br /><br />'
                    . 'Please confirm your email by clicking link below:<br />'
                    . '<a href="%s">%s</a> <br /><br />'
                    . 'Welcome,<br />'
                    . 'Read Logger Team <br /><br />'
                    . 'If you did not sign up to Read Logger service please ignore this email.',
                    $link,
                    $link
                );
                break;
            case 'pl':
                $subject = 'Witamy w serwisie Czytacz!';
                
                $messageText = sprintf("Witamy w naszym serwisie, \r\n"
                    . "Jesteśmy szczęśliwi, że dołączyłeś(aś) do naszej społeczności.\r\n \r\n "
                    . "Proszę, potwierdź swój adres e-mail klikając w poniższy link:\r\n"
                    . "%s \r\n \r\n"
                    . "Witamy,\r\n"
                    . "Zespół Czytacza \r\n\r\n"
                    . "Jeśli nie rejestrowałeś(aś) się w serwisie Czytacz, proszę zignoruj tego e-maila.",
                    $link
                );

                $messageHtml = sprintf('Witamy w naszym serwisie, <br />'
                    . 'Jesteśmy szczęśliwi, że dołączyłeś(aś) do naszej społeczności.<br /><br />'
                    . 'Proszę, potwierdź swój adres e-mail klikając w poniższy link:<br />'
                    . '<a href="%s">%s</a> <br /><br />'
                    . 'Witamy,<br />'
                    . 'Zespół Czytacza <br /><br />'
                    . 'Jeśli nie rejestrowałeś(aś) się w serwisie Czytacz, proszę zignoruj tego e-maila.',
                    $link,
                    $link
                );
                break;
            default:
                throw new UnexpectedValueException('Unsupported language!');
        }
        
        return [
            'subject' => $subject,
            'text'    => $messageText,
            'html'    => $messageHtml
        ];
    }
}
