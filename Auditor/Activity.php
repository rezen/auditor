<?php namespace Auditor;

class Activity
{
    /**
     * @var integer
     */
    public $entityId;

    /**
     * @var string
     */
    public $namespace;
    
    /**
     * @var string
     */
    public $entity;
    
    /**
     * @var string
     */
    public $activity;
    
    /**
     * @var integer
     */
    public $author;

    /**
     * @var string
     */
    public $meta;

    /**
     * @param string  $entity
     * @param integer $entityId
     * @param string  $activity
     */
    public function __construct($entity, $entityId = null, $activity = '')
    {
        $this->setEntity($entity);
        $this->entityId = $entityId;
        $this->activity = $activity;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return  [
            'namespace' => $this->namespace,
            'entity_id' => $this->entityId,
            'entity'    => $this->entity,
            'activity'  => $this->activity,
            'meta'      => $this->meta,
        ];
    }

    /**
     * @param string $slug
     */
    public function setEntity($slug)
    {
        if (!is_string($slug)) {
            return;
        }

        $pieces = explode('.', $slug);

        if (isset($pieces[1])) {
            $this->namespace = $pieces[0];
            $this->entity    =  $pieces[1];
        } else {
            $this->entity  =  $pieces[0];
        }
    }
}
