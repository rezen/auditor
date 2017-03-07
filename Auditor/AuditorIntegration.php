<?php namespace Auditor;

class AuditorIntegration
{
    /**
     * @var ActivityScout
     */
    public $scout;

    /**
     * @var ActivityRouter
     */
    public $router;

    /**
     * @var string
     */
    public $views;

    /**
     * @param ActivityScout  $scout
     * @param ActivityRouter $router
     * @param string         $viewDirectory
     */
    public function __construct($scout, $router, $viewDirectory)
    {
        $this->scout  = $scout;
        $this->router = $router;
        $this->views  = $viewDirectory;
    }

    /**
     * @param  string $viewDirectory
     * @return AuditorIntegration
     */
    public static function create($viewDirectory)
    {
        global $wpdb;
        $recorder = new ActivityRecorder($wpdb);
        $scout    = new ActivityScout($recorder, Activity::class);
        $router   = new AuditRouter($scout, $recorder);
        return new static($scout, $router, $viewDirectory);
    }

    /**
     * Sets up all the hooks for integrating into WordPress
     */
    public function hooks()
    {
        add_action('init', [$this, 'onInit'], 999);
        add_action('wp_login', [$this, 'onLogin'], 10, 2);
        add_action('user_register', [$this, 'onUserRegister']);
        add_action('profile_update', [$this, 'onProfileUpdate'], 10, 2);
        add_action('admin_menu', [$this, 'onAdminMenu']);
        $this->router->hooks();
    }

    /**
     * @param  string $template
     * @param  array  $data
     * @return string
     */
    public function render($template, $data = [])
    {
        // Prevent shenanigans, don't extract globals
        if (isset($_GET) && $_GET === $data) {
            $data = [];
        }

        if (isset($_POST) && $_POST === $data) {
            $data = [];
        }

        extract($data, EXTR_SKIP);
        include "{$this->views}/{$template}";
    }

    /**
     * @hook admin_menu
     */
    public function onAdminMenu()
    {
        $label = __('Auditor', 'auditor');
        add_users_page($label, $label, 'manage_options', 'auditor', [$this, 'pageAuditor']);
    }

    /**
     * Admin page to show user events
     *
     * @return string
     */
    public function pageAuditor()
    {
        $events = $this->scout->locate('wp.user', null);
        $events = array_map(function ($event) {
            $event->user = get_userdata($event->entity_id);
            return $event;
        }, $events);
        return $this->render('admin/page-user-events.php', [
            'events'  => $events
        ]);
    }

    /**
     * Hooks into init and attaches user info into objects
     *
     * @hook init
     */
    public function onInit()
    {
        $this->user =  Interactor::create();
        $this->scout->user  = $this->user;
        $this->router->user = $this->user;
    }

    /**
     * Recorder the ip and user-agent user logged in with
     *
     * @hook   wp_login
     * @param  string  $user_login
     * @param  WP_User $user
     */
    public function onLogin($user_login, $user)
    {
        $ip    = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $ip    = sanitize_text_field($ip);
        $agent = sanitize_text_field(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $this->scout->record('wp.user', $user->ID, __('User logged in', 'auditor'), compact('ip', 'agent'));
    }

    /**
     * Record the user account created and user role
     *
     * @hook   user_register
     * @param  integer $userId
     */
    public function onUserRegister($userId)
    {
        $user   = get_userdata($userId);
        $role   = implode(', ', array_map('sanitize_text_field', $user->roles));
        $login  = sanitize_text_field($user->user_login);
        $action = sprintf(__('Created user account for [%s] with [%s] privileges', 'auditor'), $login, $role);
        $this->scout->record('wp.user', $userId, $action);
    }

    /**
     * Record changes in users role
     *
     * @hook   profile_update
     * @param  integer $userId
     * @param  WP_User $oldData
     */
    public function onProfileUpdate($userId, $oldData)
    {
        $user = get_userdata($userId);

        if ($user->roles === $oldData->roles) {
            return;
        }

        $action = sprintf(esc_html__('User role changed from [%s] to [%s]', 'auditor'), $oldData->roles[0], $user->roles[0]);
        $this->scout->record('wp.user', $user->ID, $action);
    }
}
