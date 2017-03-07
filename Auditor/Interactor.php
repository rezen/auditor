<?php namespace Auditor;

class Interactor
{
    /**
     * @var WP_User
     */
    public $user;

    /**
     * @param WP_User $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @return Interactor
     */
    public static function create()
    {
        return new static(wp_get_current_user());
    }

    /**
     * @return boolean
     */
    public function isLoggedIn()
    {
        return is_user_logged_in();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->user->ID;
    }

    /**
     * @param  string $permission
     * @return boolean
     */
    public function can($permission)
    {
        return user_can($this->user, $permission);
    }
}
