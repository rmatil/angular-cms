<?php


namespace rmatil\cms\Mail\Mandrill;


use rmatil\cms\Mail\AMail;

class MandrillTemplateMail extends AMail {

    protected $tags = array();

    protected $mergeVars = array();

    public function __construct($subject, $fromEmail, $fromName, $to, array $mergeVars, array $tags) {
        $to['type'] = 'to';
        $this->mergeVars = $mergeVars;
        $this->tags = $tags;

        parent::__construct($subject, $fromEmail, $fromName, $to);
    }

    /**
     * @return array
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags) {
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function getMergeVars() {
        return $this->mergeVars;
    }

    /**
     * @param array $mergeVars
     */
    public function setMergeVars($mergeVars) {
        $this->mergeVars = $mergeVars;
    }


}