<?php namespace Auditor;

use WP_REST_Server;

class AuditRouter
{
    /**
     * @var Interactor
     */
    public $user;

    /**
     * @var ActivityScout
     */
    public $scout;


    /**
     * @param ActivityScout $scout
     */
    public function __construct($scout)
    {
        $this->scout = $scout;
    }
    
    /**
     * @param  string  $permission [description]
     * @return Closure
     */
    public function hasPermission($permission)
    {
        return function () use ($permission) {
            return $this->user->can($permission);
        };
    }

    /**
     * Hooks into WordPress action
     */
    public function hooks()
    {
        add_action('rest_api_init', [$this, 'onRestInit']);
    }

    /**
     * Register routes on REST api
     *
     * @hook rest_api_init
     */
    public function onRestInit()
    {
        register_rest_route("auditor/v1", '/event', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get'],
            'permission_callback' => $this->hasPermission('manage_options')
        ]);

        register_rest_route("auditor/v1", '/event', [
            'methods'             =>  WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'post'],
            'permission_callback' => $this->hasPermission('manage_options')
        ]);
    }

    /**
     * @param  WP_REST_Request $request
     * @return array
     */
    public function get($request)
    {
        return $this->scout->locate(
            $request->get_param('entity'),
            $request->get_param('entity_id'),
            $request->get_param('namespace')
        );
    }

    /**
     * @param  WP_REST_Request $request
     * @return boolean
     */
    public function post($request)
    {
        return $this->scout->record(
            $request->get_param('entity'),
            $request->get_param('entity_id'),
            $request->get_param('activity'),
            $request->get_param('meta')
        );
    }
}
