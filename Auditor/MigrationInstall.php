<?php namespace Auditor;

class MigrationInstall
{
    /**
     * @var wpdb
     */
    public $wpdb;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $optionKey = 'auditor_v';

    /**
     * @param wpdb $wpdb
     */
    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
        $this->table = "{$this->wpdb->prefix}auditor_events";
    }

    /**
     * Creates database tables!
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
			  ID          bigint(20) NOT NULL AUTO_INCREMENT,
			  namespace   varchar(120) NOT NULL,
			  author      bigint(20) NOT NULL,
			  entity      varchar(120) NOT NULL,
			  entity_id   bigint(20) NOT NULL,
			  activity    text NOT NULL,
			  meta        text,
			  created_at  datetime NOT NULL,
			  PRIMARY KEY (ID),
			  KEY entity_id (entity_id),
			  KEY entity (entity)
			) DEFAULT CHARSET=utf8;";

        $this->databaseChanges($sql);
        update_option($this->optionKey, '1.0');
    }

    /**
     * Removes database tables
     */
    public function down()
    {
        $this->databaseChanges("DROP TABLE IF EXISTS {$this->table}");
        delete_option($this->optionKey);
    }

    /**
     * @param  string $sql
     */
    protected function databaseChanges($sql)
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
