<?php
namespace TVA\Models;

class Location
{
    protected $_wpdb;
    protected $_table_city;
    protected $_table_district;

    private static $instance;

    /**
     * Location constructor.
     *
     */
    protected function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->_table_city = $wpdb->prefix . "location_city";
        $this->_table_district = $wpdb->prefix . "location_district";
    }

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Location();
        }

        return self::$instance;
    }

    //region city
    public function getCityList()
    {
        $cacheId = __CLASS__ . __METHOD__;
        $result = wp_cache_get($cacheId);
        if ($result === false) {
            $query = "SELECT * FROM {$this->_table_city}";
            $result = $this->_wpdb->get_results($query);
            wp_cache_set($cacheId, $result, CACHEGROUP, CACHETIME);
        }

        return $result;
    }

    /**
     * @param $field
     * @param $value
     * @return array|bool|mixed|null|object|void
     */
    public function getCityBy($field, $value)
    {
        $cacheId = __CLASS__ . __METHOD__ . $value . $field;
        $result = wp_cache_get($cacheId);
        if ($result === false) {
            $query = "SELECT * FROM {$this->_table_city} WHERE $field = '$value'";
            $result = $this->_wpdb->get_row($query);
            wp_cache_set($cacheId, $result, CACHEGROUP, CACHETIME);
        }

        return $result;
    }
    //endregion

    //region district
    public function getDistrictList()
    {
        $cacheId = __CLASS__ . __METHOD__;
        $result = wp_cache_get($cacheId);
        if ($result === false) {
            $query = "SELECT * FROM {$this->_table_district}";
            $result = $this->_wpdb->get_results($query);
            wp_cache_set($cacheId, $result, CACHEGROUP, CACHETIME);
        }

        return $result;
    }

    /**
     * @param $field
     * @param $value
     * @return array|bool|mixed|null|object|void
     */
    public function getDistrictBy($field, $value)
    {
        $cacheId = __CLASS__ . __METHOD__ . $value . $field;
        $result = wp_cache_get($cacheId);
        if ($result === false) {
            $query = "SELECT * FROM {$this->_table_district} WHERE $field = '$value'";

            if ($field == 'city_id') {
                $result = $this->_wpdb->get_results($query);
            } else {
                $result = $this->_wpdb->get_row($query);
            }

            wp_cache_set($cacheId, $result, CACHEGROUP, CACHETIME);
        }

        return $result;
    }

    //endregion

    public function getMixedLocation()
    {
        $cacheId = __CLASS__ . __METHOD__;
        $result = wp_cache_get($cacheId);
        if ($result === false) {
            $cities = $this->getCityList();
            foreach ($cities as $city) {
                $query = "SELECT * FROM {$this->_table_district} WHERE city_id = {$city->id}";
                $districts = $this->_wpdb->get_results($query);
                $city->districts = $districts;
                $result[] = $city;
            }

            wp_cache_set($cacheId, $result, CACHEGROUP, CACHETIME);
        }

        return $result;
    }
}