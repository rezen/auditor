<?php namespace Auditor;

class ActivityRecorder
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
     * @var integer
     */
    public $limit = 100;

    /**
     * @var string
     */
    public static $tableName = 'auditor_events';

    /**
     * @param wpdb $wpdb
     */
    public function __construct($wpdb)
    {
        $this->wpdb  = $wpdb;
        $this->table = $wpdb->prefix . self::$tableName;
    }

    /**
     * @param  Activity $activity
     * @return boolean
     */
    public function record($activity)
    {
        $insert                = $activity->toArray();
        $insert['created_at']  = current_time('mysql');

        if (!$insert['entity']) {
            // @todo throw exception
        }

        if (!is_string($insert['meta'])) {
            $insert['meta'] = json_encode($insert['meta']);
        }
        return $this->wpdb->insert($this->table, $insert);
    }

    /**
     * @param  Activity  $activity
     * @param  integer   $page
     * @return array
     */
    public function find($activity, $page = 0)
    {
        $data   = $activity->toArray();
        $offset = $page * $this->limit;
        $params  = [];
        $filters = [
            'entity_id' => '%d',
            'entity'    => '%s',
            'namespace' => '%s',
        ];

        $sql  = "SELECT * FROM {$this->table} ";
        $sql .= "WHERE 1=1 ";

        foreach ($filters as $filter => $type) {
            if (is_null($data[$filter]) || empty($data[$filter])) {
                continue;
            }

            $sql .= "AND $filter = $type ";
            $params[] = sanitize_text_field($data[$filter]);
        }

        $sql .= "ORDER BY created_at DESC ";
        $sql .= "LIMIT %d OFFSET %d ";

        $params[] = $this->limit;
        $params[] = $offset;

        array_unshift($params, $sql);



        $results =  $this->wpdb->get_results(
            call_user_func_array([$this->wpdb, 'prepare'], $params)
        );

        return array_map([$this, 'transformResult'], $results);
    }

    /**
     * @param  stdClass $result
     * @return stdClass
     */
    protected function transformResult($result)
    {
        if (!is_string($result->meta)) {
            return $result;
        }

        $first = substr($result->meta, 0, 1);

        if ($first === '[' || $first === '{') {
            $result->meta = json_decode($result->meta, true);
        }
        return $result;
    }
}
