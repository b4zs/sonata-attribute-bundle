<?php

namespace Core\AttributeBundle\EventAction;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraints\Email;
use Core\AttributeBundle\Entity\FormSubmission;
use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Event\FormSubmissionEvent;
use Core\AttributeBundle\Utils\TypeHelper;
use Core\DynamicEventListenerBundle\Entity\DynamicEventListener;
use Core\DynamicEventListenerBundle\EventAction\SendEmailAction;

/**
 * Class SendDynamicFormEmailAction
 * @package Core\AttributeBundle\EventAction
 */
class SendDynamicFormEmailAction extends SendEmailAction
{
    public function getLabel()
    {
        return 'Dynamic form event';
    }

    public function getAvailableEvents()
    {
        return array(
            'dynamic_form_submission\..+',
        );
    }

    /**
     * @param Event $event
     * @param DynamicEventListener $listenerEntity
     */
    public function processEvent(Event $event, DynamicEventListener $listenerEntity)
    {
        $params = $listenerEntity->getParams();

        if(!isset($params['from_email'])){
            throw new \InvalidArgumentException('Parameter "from_email" must be set');
        }

        list($body, $subject) = $this->renderTemplate($params);
        $to = array();

        if($body && $event instanceof FormSubmissionEvent){
            $formSubmission = $event->getFormSubmission();
            $to[] = $this->replacePlaceholdersWithValues($formSubmission, $params['to']);
            if($formSubmission instanceof FormSubmission){
                $additionalValues = array(
                    '[current_date_time]' => date('d-m-Y H:i'),
                );
                $body = $this->replacePlaceholdersWithValues($formSubmission, $body, $additionalValues);
            }else{
                //
            }
        }else{
            //
        }

        $from = array(
            'email' => $params['from_email'],
            'name'  => $params['from_name'],
        );

        $this->sendMail(
            $from,
            $to,
            $subject,
            $body
        );
    }

    public function buildParamsForm(FormBuilderInterface $formBuilder, $options){

        $eventName = $options['event_name'];

        $placeholders = array(
            '[current_date_time]',
        );

        if(null !== $eventName){
            $rootType = $this->guessFormTypeFromEventName($eventName);
            $inputPaths = TypeHelper::flattenType($rootType);
            $placeholders = $placeholders+array_map(
                function($n){
                    return '['.$n.']';
                },
                $inputPaths
            );
        }

        $bodySonataHelp = 'Available placeholders:'. PHP_EOL .implode($placeholders, PHP_EOL);

        $formBuilder->add('from_email', 'email', array(
            'constraints' => array(
                new Email()
            ),
        ));
        $formBuilder->add('from_name', 'text', array());
        $formBuilder->add('to', 'text');
        $formBuilder->add('subject', 'text', array());
        $formBuilder->add('body', 'ckeditor', array(
            'config' => array(
                'toolbar' => array(
                    array(
                        'name' => 'clipboard',
                        'items' => array('Undo', 'Redo'),
                    ),
                    array(
                        'name' => 'links',
                        'items' => array('Link', 'Unlink'),
                    ),
                    array(
                        'name' => 'basicstyles',
                        'items' => array('Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'),
                    ),
                    array(
                        'name' => 'paragraph',
                        'items' => array('NumberedList', 'BulletedList'),
                    ),
                ),
            ),
            'sonata_help' => $bodySonataHelp,
        ));
        $formBuilder->add('template', 'choice', array(
            'choices' => $this->getTemplates(),
            'required' => true,
        ));

    }

    private function guessFormTypeFromEventName($eventName){

        if(preg_match("/dynamic_form_submission\.(.+)/", $eventName, $output_array)){
            $name = $output_array[1];
            $doctrine = $this->getDoctrine();

            $type = $doctrine->getRepository('CoreAttributeBundle:Type')->findOneBy(array(
                'name' => $name,
            ));

            if(!$type){
                throw new \RuntimeException(sprintf('Type with name "%s" can not be found"', $name));
            }else{
                //
            }

        }else{
            throw new \RuntimeException(sprintf('Invalid event name format: "%s"', $eventName));
        }

        return $type;

    }

    private function getDoctrine(){
        return $this->container->get('doctrine');
    }

    /**
     * @param $formSubmission
     * @param $text string
     * @return string
     */
    private function replacePlaceholdersWithValues(FormSubmission $formSubmission, $text, $additionalValues = array())
    {
        $rootType = $formSubmission->getType();
        $paths = TypeHelper::flattenType($rootType);
        $paths = array_combine($paths, $paths);

        $paths = array_map(function($n){
            $n = explode('.', $n);
            array_shift($n);
            array_unshift($n, 'collection');
            return implode('.', $n);
        },$paths);

        $pa = new PropertyAccessor();

        $collectionValues = array();
        foreach ($paths as $typePath => $propertyPath) {
            $value = $pa->getValue($formSubmission, $propertyPath);
            $placeholder = '[' . $typePath . ']';

            if (null !== $value && (is_scalar($value) || method_exists($value, '__toString'))) {
                $collectionValues[$placeholder] = (string)$value;
            }
        }

        $collectionValues = $collectionValues + $additionalValues;

        $text = strtr($text, $collectionValues);
        return $text;
    }

}