<?php


namespace Core\AttributeBundle\Validator\Constraints;


use Core\AttributeBundle\Entity\Type;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TypeClassValidator extends ConstraintValidator
{

    /** @var  EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if(!$value instanceof Type){
            return;
        }

        $name = $value->getName();
        $id = $value->getId();

        if(null !== $value->getParent()){
            //this type is not a root
            $siblings = $value->getParent()->getChildren();
            $siblings = $siblings->filter(function(Type $type) use ($id, $name){
                return $name == $type->getName() && $type->getId() !== $id;
            });
        }else{
            //this is a root type
            $siblingsQuery = $this->entityManager
                ->getRepository('CoreAttributeBundle:Type')
                ->createQueryBuilder('type')
                ->andWhere('type.name = :name')
                ->setParameter('name', $name)
            ;

            if(null !== $id){
                $siblingsQuery
                    ->andWhere('type.id != :id')
                    ->setParameter('id', $id)
                ;
            }

            $siblings = $siblingsQuery->getQuery()
                ->execute()
            ;
        }

//        var_dump($siblings);
//        die();

        if(count($siblings)){
            $this->createViolation($name, $constraint);
        }

    }

    private function createViolation($name, Constraint $constraint)
    {
        $this->context->buildViolation(strtr($constraint->message, array('%name%' => $name)))
            ->atPath('name')
            ->addViolation();
    }

}