<?php

namespace Core\AttributeBundle\Entity;

/**
 * FormSubmission
 */
class FormSubmission
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * @var \Core\AttributeBundle\Entity\CollectionAttribute
     */
    private $collection;

    /**
     * @var \Core\AttributeBundle\Entity\Type
     */
    private $type;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return FormSubmission
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return FormSubmission
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return FormSubmission
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    
        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set collection
     *
     * @param \Core\AttributeBundle\Entity\CollectionAttribute $collection
     *
     * @return FormSubmission
     */
    public function setCollection(\Core\AttributeBundle\Entity\CollectionAttribute $collection = null)
    {
        $this->collection = $collection;
    
        return $this;
    }

    /**
     * Get collection
     *
     * @return \Core\AttributeBundle\Entity\CollectionAttribute
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set type
     *
     * @param \Core\AttributeBundle\Entity\Type $type
     *
     * @return FormSubmission
     */
    public function setType(\Core\AttributeBundle\Entity\Type $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \Core\AttributeBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }
}

