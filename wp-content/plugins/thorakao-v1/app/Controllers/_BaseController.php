<?php
namespace TVA\Controllers;

class _BaseController
{
    protected $session;
    protected $validate;

    protected function __construct()
    {
        // view docs https://github.com/auraphp/Aura.Session
        $session_factory = new \Aura\Session\SessionFactory;
        $this->session = $session_factory->newInstance($_COOKIE);

        // view doc https://github.com/vlucas/valitron
        $this->validate = new \Valitron\Validator($_POST, [], 'vi');
    }


    public function ajaxHandler()
    {
        // view docs http://labs.omniti.com/labs/jsend
        $result = [
            'status'  => 'error',
            'message' => 'Đã xảy ra lỗi, vui lòng thử lại'
        ];
        if (!empty($_REQUEST["method"])) {
            $method = sanitize_text_field($_REQUEST["method"]);
            if (method_exists($this, "ajax" . $method)) {
                $result = call_user_func([$this, "ajax" . $method], $_REQUEST);
            }
        }
        $this->clearStaticCache();

        echo json_encode($result);
        exit;
    }


    public function clearStaticCache()
    {
        // Get cURL resource
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => WP_SITEURL . '/purge/'
        ]);
        $resp = curl_exec($curl);
        curl_close($curl);
    }
}
