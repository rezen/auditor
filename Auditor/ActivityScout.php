<?php namespace Auditor;

class ActivityScout
{
    /**
     * @var Interactor
     */
    public $user;

    /**
     * @var ActivityRecorder
     */
    public $recorder;

    /**
     * @var string
     */
    public $activityClass = Activity::class;

    /**
     * @param ActivityRecorder $recorder
     * @param string           $activityClass
     */
    public function __construct($recorder, $activityClass)
    {
        $this->recorder      = $recorder;
        $this->activityClass = $activityClass;
    }

    /**
     * @param  string  $entity
     * @param  integer $entityId
     * @param  string  $action
     * @param  array   $meta
     * @return boolean
     */
    public function record($entity, $entityId, $action, $meta = [])
    {
        $activity = new $this->activityClass($entity, $entityId, $action);
        $activity->meta = json_encode($meta);

        if ($this->user->isLoggedIn()) {
            $activity->author = $this->user->getId();
        }

        return $this->recorder->record($activity);
    }

    /**
     * @param  string  $entity
     * @param  integer $entityId
     * @param  string  $namespace
     * @param  integer $page
     * @return array
     */
    public function locate($entity, $entityId, $namespace = null, $page = 0)
    {
        $activity = new $this->activityClass($entity, $entityId);
        if (!is_null($namespace)) {
            $activity->namespace = $namespace;
        }
        return $this->recorder->find($activity, $page);
    }
}
