<?php

namespace Core\AttributeBundle\Controller;

use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\AttributeBasedType;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DefaultController extends Controller
{
    public function editAction(Request $request)
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $router = $this->container->get('router');
        $attributeRepository = $entityManager->getRepository('CoreAttributeBundle:CollectionAttribute');
        $typeRepository = $entityManager->getRepository('CoreAttributeBundle:Type');

        $data = null;
        $type = null;
        if ($id = $request->get('id')) {
            $data = $attributeRepository->find($id);
            if (null === $data) {
                throw new EntityNotFoundException('Attribute#'.$id.' not found');
            }
            $type = $data->getType();
        } elseif ($typeName = $request->get('type')) {
            $type = $typeRepository->findOneByName($typeName);
            if (null === $type) {
                throw new EntityNotFoundException('Type[name='.$typeName.'] not found');
            }
        } else {
            throw new BadRequestHttpException('Neither id nor type was defined, thus unable to create form');
        }

        $form = $this->createForm(new AttributeBasedType($type), $data);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();

                $entityManager->persist($data);
                $entityManager->flush();

                $url = $router->generate('core_attribute_edit', array('id' => $data->getId()));
                return new RedirectResponse($url);
            }
        }

        return $this->render('CoreAttributeBundle:Default:edit.html.twig', array(
            'data'  => $data,
            'type'  => $type,
            'name'  => 'attribute',
            'form'  => $form->createView(),
        ));
    }

    public function listAction()
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $router = $this->container->get('router');
        $attributeRepository = $entityManager->getRepository('CoreAttributeBundle:CollectionAttribute');

        $records = $attributeRepository
            ->createQueryBuilder('a')
            ->andWhere('a.parent IS NULL')
            ->getQuery()
            ->getResult();

        return $this->render('CoreAttributeBundle:Default:list.html.twig', array(
            'records' => $records
        ));
    }
}
