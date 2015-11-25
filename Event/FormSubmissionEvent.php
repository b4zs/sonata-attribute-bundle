<?php

namespace Core\AttributeBundle\Event;


use Core\AttributeBundle\Entity\FormSubmission;
use Symfony\Component\EventDispatcher\Event;

class FormSubmissionEvent extends Event
{

    /**
     * @var FormSubmission
     */
    private $formSubmission;


    /**
     * FormSubmissionEvent constructor.
     * @param FormSubmission $formSubmission
     */
    public function __construct(FormSubmission $formSubmission)
    {
        $this->formSubmission = $formSubmission;
    }

    /**
     * @return FormSubmission
     */
    public function getFormSubmission()
    {
        return $this->formSubmission;
    }


}