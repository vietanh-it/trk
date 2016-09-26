<?php
namespace TVA\Library;
if ( !class_exists( 'Images' ) ) {

    class Images
    {
        private static $instance;

        protected function __construct(  ) {

        }

        public static function init()
        {
            if ( ! isset( self::$instance )) {
                self::$instance = new Images();
            }

            return self::$instance;
        }

        public function upload_image($post_id, $file)
        {
            $result = array(
                'status' => 'error',
                'message' => "Đăng hình không thành công."
            );
            if (!empty($file["name"])) {
                $filename = $file['name'];
                $wp_filetype = wp_check_filetype($filename);
                $ext_allow = array('jpg', 'jpeg', 'png');
                $max_size = 2 * 1024 * 1024;
                if (in_array(strtolower($wp_filetype['ext']), $ext_allow)) {
                    if ($file['size'] <= $max_size) {
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attach_id = wp_insert_attachment($attachment, $filename, $post_id);

                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                        $upload = wp_handle_upload($file, array('test_form' => false));
                        $attach_data = wp_generate_attachment_metadata($attach_id, $upload["file"]);
                        update_post_meta($attach_id, '_wp_attached_file', $attach_data["file"]);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        $kq = set_post_thumbnail($post_id, $attach_id);
                        if ($kq) {
                            $result = array(
                                'status' => 'success',
                                'message' => "Đăng hình thành công."
                            );
                        }
                    } else {
                        $result['message'] = 'Kích thước hình quá hơn 2M.';
                    }
                } else {
                    $result['message'] = 'Định dạng hình ảnh không hợp lệ.';
                }
            }
            return $result;
        }

        public function getPostImages($post_id, $arr_size){
            $images = array();
            $arr_size = (array)$arr_size;
            $thumbnail_id = get_post_thumbnail_id($post_id);
            foreach ($arr_size as $size) {
                $obj_image = wp_get_attachment_image_src($thumbnail_id, $size, true);
                $images[$size] = $obj_image[0];
            }
            return (object)$images;
        }

    }
}