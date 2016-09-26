<?php
if (!function_exists('view')) {
    /**
     * @param null $view
     * @param array $data
     * @param bool $in_theme
     */
    function view($view = null, $data = [], $in_theme = false)
    {
        extract($data);
        require_once PATH_VIEW . $view . '.view.php';
    }
}

if (!function_exists('partial')) {
    /**
     * @param null $view
     * @param array $data
     * @param bool $in_theme
     */
    function partial($view = null, $data = [], $in_theme = false)
    {
        extract($data);
        require_once get_template_directory() . $view . '.php';
    }
}

if (!function_exists('sendy_action')) {

    function sendy_action($data, $action)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if ($action == 1) {
//        $data['name'] = $userdata->data->display_name;
            curl_setopt($ch, CURLOPT_URL, 'http://sendy.ringier.com.vn/subscribe');
        } else {
            if (isset($data['name'])) {
                unset($data['name']);
            }
            curl_setopt($ch, CURLOPT_URL, 'http://sendy.ringier.com.vn/unsubscribe');
        }

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
    }
}

if (!function_exists('cut_string_by_char')) {

    function cut_string_by_char($string, $max_length)
    {
        if (mb_strlen($string, "UTF-8") > $max_length) {
            $max_length = $max_length - 3;
            $string = mb_substr($string, 0, $max_length, "UTF-8");
            $pos = strrpos($string, " ");
            if ($pos === false) {
                return substr($string, 0, $max_length) . "...";
            }
            return substr($string, 0, $pos) . "...";
        } else {
            return $string;
        }
    }
}

if (!function_exists('limitWord')) {

    /**
     * Cut string by words
     *
     * @param $content
     * @param int $word_count
     * @param string $read_more_link
     * @return string
     */
    function limitWords($content, $word_count = 50, $read_more_link = '')
    {
        $content = trim(strip_tags($content));
        $arr = explode(' ', $content);
        $rs = '';
        for ($i = 0; $i < $word_count; $i++) {
            if (!empty($arr[$i])) {
                if ($i != 0) {
                    $rs .= ' ' . $arr[$i];
                } else {
                    $rs .= $arr[$i];
                }
            }
        }

        if (count($arr) > $word_count) {
            $rs .= '...';
        }

        $rs .= $read_more_link;

        return $rs;
    }
}

if (!function_exists('set_cache_tag')) {
    /**
     * Set cache by tag name
     *
     * @param string $key
     * @param array|string $tags
     */
    function set_cache_tag($key, $tags)
    {
        if (!is_array($tags)) {
            $tags = array($tags);
        }
        foreach ($tags as $tag) {
            $tag_key = "tag_" . md5($tag);
            $list = wp_cache_get($tag_key);
            if ($list === false) {
                $list = array($key);
            } else {
                $list[] = $key;
            }
            wp_cache_set($tag_key, $list, CACHEGROUP, CACHETIME);
        }
    }
}

if (!function_exists('delete_cache_tag')) {
    /**
     * Delete in tag name
     *
     * @param string|array $tags
     */
    function delete_cache_tag($tags)
    {
        if (!is_array($tags)) {
            $tags = array($tags);
        }
        foreach ($tags as $tag) {
            $tag_key = "tag_" . md5($tag);
            $list = wp_cache_get($tag_key);
            if (is_array($list) && !empty($list)) {
                foreach ($list as $value) {
                    wp_cache_delete($value, CACHEGROUP, CACHETIME);
                }
            }
        }
    }
}

if (!function_exists('valueOrNull')) {
    /**
     * Check variable if null and return value or default value if null
     * @param $value
     * @param null $default_data
     * @return null
     */
    function valueOrNull(&$value, $default_data = null)
    {
        return empty($value) ? $default_data : $value;
    }
}

if (!function_exists('isAjax')) {
    /**
     * Check if request is ajax request
     *
     * @return bool
     */
    function isAjax()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('generateRandomCode')) {
    /**
     * Tạo code random
     *
     * @param int $length
     * @return string
     */
    function generateRandomCode($length = 5)
    {
        $code = '';
        $total = 0;
        do {
            $code .= rand(0, 9);
            $total++;
        } while ($total < $length);

        return $code;
    }
}

if (!function_exists('getImages')) {
    function getImages($post_id)
    {
        get_the_post_thumbnail_url($post_id, ['thumbnail', 'featured-image', 'square-image']);
    }
}