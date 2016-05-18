<?php


namespace rmatil\cms\Mail\Mandrill;


use Mandrill;
use rmatil\cms\Exceptions\InvalidConfigurationException;
use rmatil\cms\Handler\ConfigurationHandler;
use rmatil\cms\Mail\MailerInterface;
use rmatil\cms\Mail\MailInterface;
use rmatil\cms\Mail\RegistrationMail\MandrillRegistrationMail;
use rmatil\cms\Mail\RegistrationMail\RegistrationMail;
use RuntimeException;

class MandrillTemplateMailer implements MailerInterface {

    const MAILER_NAME = 'mandrill';

    /**
     * @var Mandrill Mandrill instance
     */
    protected $mandrill;

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var array
     */
    protected $templateContent;

    /**
     * @var array
     */
    protected $globalMergeVars;

    public function __construct() {
        $config = ConfigurationHandler::readConfiguration(CONFIG_FILE);

        if (! array_key_exists('mail', $config) ||
            ! array_key_exists(MandrillTemplateMailer::MAILER_NAME, $config['mail'])) {
            throw new InvalidConfigurationException(sprintf('Expected a mail configuration for %s', MandrillTemplateMailer::MAILER_NAME));
        }

        $mailChimpConfig = $config['mail'][MandrillTemplateMailer::MAILER_NAME];

        $globalMergeVars = array();
        foreach ($mailChimpConfig['global_merge_vars'] as $key => $val) {
            $globalMergeVars[] = array(
                'name' => $key,
                'content' => $val
            );
        }

        $this->mandrill = new Mandrill($mailChimpConfig['api_key']);
        $this->templateName = $mailChimpConfig['template_name'];
        $this->templateContent = $mailChimpConfig['template_content'];
        $this->globalMergeVars = $globalMergeVars;
    }

    public function send(MailInterface $mail) {
        if ( ! ($mail instanceof MandrillTemplateMail)) {
            throw new RuntimeException(sprintf("Mail must be of instance %s to be sent using %s", MandrillTemplateMail::class, MandrillTemplateMailer::class));
        }

        if ($mail instanceof RegistrationMail) {
            $mail = new MandrillRegistrationMail($mail);
        }

        $message = array(
            'subject' => $mail->getSubject(),
            'from_email' => $mail->getFromEmail(),
            'from_name' => $mail->getFromName(),
            'to' => array(
                $mail->getTo()
            ),
            'headers' => array('Reply-To' => $mail->getFromEmail()),
            'important' => false,
            'track_opens' => false,
            'track_clicks' => false,
            'auto_text' => false,
            'auto_html' => false,
            'inline_css' => false,
            'url_strip_qs' => false,
            'preserve_recipients' => false,
            'view_content_link' => false,
            'merge' => true,
            'merge_language' => 'mailchimp',
            'global_merge_vars' => $this->globalMergeVars,
            'merge_vars' => array($mail->getMergeVars()),
            'tags' => $mail->getTags()
        );

        $async = false;
        $ipPool = 'Main Pool';


        $result = $this->mandrill->messages->sendTemplate(
            $this->templateName,
            $this->templateContent,
            $message,
            $async,
            $ipPool
        );

        return $result;
    }
}
