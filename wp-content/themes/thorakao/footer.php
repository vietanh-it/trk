<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 09/05/2016
 * Time: 12:40 SA
 */

$post_ctrl = \TVA\Controllers\PostController::init();

$theo_loai = $post_ctrl->getTermList('theo-loai');
$theo_cong_dung = $post_ctrl->getTermList('theo-cong-dung');
?>
<div class="pre-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-2 col-xs-12 raw-logo">
                <img src="<?php echo THEME_URL; ?>/images/logo-raw-white.png" width="120">
            </div>
            <div style="line-height: 35px;"
                 class="col-md-3 col-xs-12 title text-right"><?php echo (pll_current_language() == 'vi') ? 'Đăng ký nhận tin qua email' : 'Register for newsletter'; ?></div>
            <div class="col-md-7 col-xs-12">
                <form class="frm-newsletter uk-form-horizontal uk-form">
                    <input type="text" name="email_newsletter"
                           placeholder="<?php echo (pll_current_language() == 'vi') ? 'Nhập email để nhận thông báo' : 'Enter your email address'; ?>">
                    <button type="submit"
                            class="btn btn-primary"><?php echo (pll_current_language() == 'vi') ? 'Đăng ký nhận tin' : 'Subscribe'; ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div
                    class="footer__title"><?php echo (pll_current_language() == 'vi') ? 'Theo loại' : 'By type'; ?></div>
                <ul class="footer__menu nav">
                    <?php foreach ($theo_loai as $key => $item) {
                        $item->permalink = get_term_link($item); ?>

                        <li>
                            <a href="<?php echo $item->permalink; ?>">
                                <i class="fa fa-angle-right"></i><?php echo $item->name; ?>
                            </a>
                        </li>

                    <?php } ?>
                </ul>
            </div>
            <div class="col-md-3">
                <div
                    class="footer__title"><?php echo (pll_current_language() == 'vi') ? 'Theo công dụng' : 'By effect'; ?></div>
                <ul class="footer__menu nav">
                    <?php foreach ($theo_cong_dung as $key => $item) {
                        $item->permalink = get_term_link($item); ?>

                        <li>
                            <a href="<?php echo $item->permalink; ?>">
                                <i class="fa fa-angle-right"></i><?php echo $item->name; ?>
                            </a>
                        </li>

                    <?php } ?>
                </ul>
            </div>
            <div class="col-md-3">
                <div
                    class="footer__title"><?php echo (pll_current_language() == 'vi') ? 'Chăm sóc khách hàng' : 'Customer Services'; ?></div>
                <ul class="footer__menu nav">
                    <?php if (pll_current_language() == 'vi') { ?>
                        <li><a href="<?php echo get_page_link(get_page_by_path('mua-hang-cod')); ?>"><i
                                    class="fa fa-angle-right"></i>Mua hàng C.O.D</a></li>
                        <li><a href="<?php echo get_page_link(get_page_by_path('mua-hang-chuyen-khoan')); ?>"><i
                                    class="fa fa-angle-right"></i>Mua hàng chuyển khoản</a></li>
                        <li><a href="<?php echo get_page_link(get_page_by_path('thong-tin-chuyen-khoan')); ?>"><i
                                    class="fa fa-angle-right"></i>Thông tin chuyển khoản</a></li>
                        <li><a href="<?php echo get_page_link(get_page_by_path('mien-phi-giao-hang')); ?>"><i
                                    class="fa fa-angle-right"></i>Miễn phí giao hàng - Free ship</a></li>
                        <li><a href="<?php echo get_page_link(get_page_by_path('dai-ly-uy-quyen')); ?>"><i
                                    class="fa fa-angle-right"></i>Đại lý ủy quyền</a></li>
                    <?php } ?>
                    <li><a href="<?php echo get_page_link(get_page_by_path('chinh-sach-quy-dinh-chung')); ?>"><i
                                class="fa fa-angle-right"></i><?php echo (pll_current_language() == 'vi') ? 'Chính sách - Quy định chung' : 'Term & condition'; ?>
                        </a></li>
                    <li><a href="<?php echo get_page_link(get_page_by_path('dieu-khoan-su-dung')); ?>"><i
                                class="fa fa-angle-right"></i><?php echo (pll_current_language() == 'vi') ? 'Điều khoản sử dụng' : 'Term & condition'; ?>
                        </a></li>
                    <li><a href="<?php echo get_page_link(get_page_by_path('quyen-rieng-tu')); ?>"><i
                                class="fa fa-angle-right"></i><?php echo (pll_current_language() == 'vi') ? 'Quyền riêng tư' : 'Privacy Policy'; ?>
                        </a></li>
                    <li><a href="<?php echo get_page_link(get_page_by_path('lien-he')); ?>"><i
                                class="fa fa-angle-right"></i><?php echo (pll_current_language() == 'vi') ? 'Liên hệ' : 'Contact'; ?>
                        </a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <div class="footer__title"
                     style="white-space: nowrap;"><?php echo (pll_current_language() == 'vi') ? 'Kết nối với thorakao.vn' : 'Connect With thorakao.vn'; ?></div>
                <div class="footer__contact-info text-center">
                    Giấy CNĐKKD: 048032. Đăng ký lần đầu ngày 18 tháng 07 năm 1997.
                    <br/><br/> Đăng ký thay đổi lần thứ: 9, ngày 03 tháng 03 năm 2008.
                    <br/><br/> Cơ quan cấp: Phòng Đăng ký kinh doanh Sở Kế hoạch và Đầu tư TPHCM.
                </div>
                <div class="footer__social-links text-center"><a href="https://www.facebook.com/MyPhamThorakao/"><img
                            src="<?php echo THEME_URL; ?>/images/social/fb.png"></a><a
                        href="#"><img
                            src="<?php echo THEME_URL; ?>/images/social/g.png"></a><a href="#"><img
                            src="<?php echo THEME_URL; ?>/images/social/y.png"></a></div>
            </div>
        </div>
    </div>
</footer>
<div class="bottom-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-3"><img src="<?php echo THEME_URL; ?>/images/dkbct.png" width="115"
                                       style="margin-right: 12px;"><img
                    src="<?php echo THEME_URL; ?>/images/ncccl.png" width="90"></div>
            <div class="col-md-5 company_info text-center">
                <div style="padding-bottom: 10px;"><b>Copyright &copy; 2016 By Thorakao, All rights reserved</b></div>
                <div>Công ty TNHH sản xuất mỹ phẩm Lan Hảo<br>241 bis Cách mạng tháng tám, quận 3, tphcm<br>Hotline:
                     (08) 383 27381<br>Email: Thorakao@thorakaovn.com
                </div>
            </div>
            <div class="col-md-4 footer-bottom-logos">
                <div style="padding-bottom: 10px;" class="text-center"><b
                        style="text-transform: uppercase; white-space: nowrap;">Thanh toán an toàn và giao hàng</b>
                    <div class="text-center">
                        <div>
                            <img src="<?php echo THEME_URL; ?>/images/vcb.png" width="50" height="40">
                            <img src="<?php echo THEME_URL; ?>/images/ghn.png?v1" height="20">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="back-to-top"><i class="fa fa-chevron-up"></i></div>
<?php wp_footer(); ?>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>

</body>
</html>