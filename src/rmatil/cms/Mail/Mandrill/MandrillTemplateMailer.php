<?php


namespace rmatil\cms\Mail\Mandrill;


use Mandrill;
use rmatil\cms\Mail\MailerInterface;
use rmatil\cms\Mail\MailInterface;
use RuntimeException;

class MandrillTemplateMailer implements MailerInterface {

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

    public function __construct($apiToken, $templateName, array $templateContent, array $globalMergeVars) {
        $this->mandrill = new Mandrill($apiToken);
        $this->templateName = $templateName;
        $this->templateContent = $templateContent;
        $this->globalMergeVars = $globalMergeVars;
    }

    public function send(MailInterface $mail) {
        if ( ! ($mail instanceof MandrillTemplateMail)) {
            throw new RuntimeException(sprintf("Mail must be of instance %s to be sent using %s", MandrillTemplateMail::class, MandrillTemplateMailer::class));
        }

        $message = array(
            'subject' => $mail->getSubject(),
            'from_email' => $mail->getFromEmail(),
            'from_name' => $mail->getFromName(),
            'to' => array(
                $mail->getTo()
            ),
            'headers' => array('Reply-To' => 'info@jogr.ch'),
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