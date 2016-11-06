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
            <div class="col-md-2 col-xs-12 raw-logo"><img src="<?php echo THEME_URL; ?>/images/logo-raw-white.png" width="120">
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
                        <li><a href="<?php echo get_page_link(get_page_by_path('chinh-sach-giao-hang')); ?>"><i
                                    class="fa fa-angle-right"></i>Chính sách giao hàng</a></li>
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
                    Giấy CNĐKDN: 0301447063. Đăng ký lần đầu ngày 18 tháng 07 năm 1995.
                    <br/><br/> Đăng ký thay đổi lần thứ: 10, ngày 19 tháng 09 năm 2016.
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

<div style="border-top: 1px solid #dddddd; margin-bottom: 40px;">
	<div class="container">
		<div class="row" style="margin-top: 40px;">
				<div class="col-md-4">
					<h1 style="font-size: 16px; margin-top: 0;">Thorakao.vn - Website chính thức bởi mỹ phẩm thiên nhiên Thorakao Việt Nam.</h1>
					Mua thỏa thích trực tuyến (mua hàng online) mỹ phẩm thiên nhiên thorakao tại thorakao.vn. Hơn 50 năm phát triển, thương hiệu Thorakao đã thiết lập thị trường vững chắc trên toàn lãnh thổ Việt Nam, đa dạng hóa và phát triển các sản phẩm ngày càng phong phú về chủng loại, mẫu mã, nhãn hiệu.
					Những dòng mỹ phẩm chất lượng cao, giá cả phải chăng, được người tiêu dùng yêu thích qua nhiều năm có thể kể đến như kem nghệ, sữa rửa mặt nghệ, kem thoa da dưa leo, dầu gội dâu tằm, kem Trân châu, kem tan mỡ bụng,…Ngoài những thị trường lớn như Hồ Chí Minh, Hà Nội, Đà Nẵng, Cần Thơ,..Công ty đã mở rộng thị trường của mình sang nhiều nước khác như Singapore, Ðài Loan, Campuchia, Lào, Trung Quốc, Hàn Quốc, Úc, New Zealand, Thụy Sỹ, Mỹ, Ả Rập Saudi, DuBai, Ai Cập, Nga, Các nước Châu Phi…
				</div>
				<div class="col-md-4">
					<b>MỸ PHẨM NỔI BẬT:</b>
					<br/>
					<br/>
					Kem nghệ thorakao, kem sâm, kem trị mụn, sữa rửa mặt nghệ, kem tan mỡ bụng, phấn, dầu gội, nước hoa hồng, kem gấc, kem chống nắng, sữa rửa mặt sữa bò, kem tẩy tế bào chết, kem trị thâm quầng mắt, dầu gội bồ kết, lotion dưỡng tóc, kem lột nhẹ,
					<br/>
					<br/>
					<b>Dưỡng da</b>
					<br/>
					<br/>
					Kem Ngừa Nám Ốc Sên, Kem Trang Điểm Liquid, Kem Giảm Vết Nhăn, Kem Giảm Thâm Quầng Mắt Dưỡng Trắng Da Sữa Dê, Dưỡng Da Ốc Sên, kem nghệ collagen, kem lột nhẹ dưa leo, sữa rửa mặt nghệ ngừa mụn, sữa rửa mặt tẩy trang,
					<b>Dưỡng tóc</b>
					<br/>
					<br/>
					Dầu gội bồ kết, dầu gội bưởi, serum dưỡng tóc, lotion dưỡng tóc, dầu gội tỏi.
					Dưỡng toàn thân
					kem chống nắng, sữa tắm mật ong, kem massage, kem thoa tay, kem ngừa nứt gót chân, Kem Ngừa Nứt Nẻ Bàn Chân
				</div>
				<div class="col-md-4">
					<b>Combo</b>
					<br/>
					<br/>
					Bộ đôi dưỡng tóc
					<br/>
					<br/>
					<b>Cam kết từ Thorakao</b>
					<br/>
					<br/>
					100% sản phẩm được sản xuất tại Việt Nam
					100% không có Corticoid
					100% không có Isobutyl ParabensGiao hàng tận nơi toàn quốc
					<br/>
					<br/>
					<b>Thành phần thiên nhiên</b>
					<br/>
					<br/>
					Bồ kết, lúa mì, bưởi, cam, dừa, ô-liu, hướng dương, tỏi, mật ong, mù u, sáp ong, dâu tằm, cám gạo, đậu xanh, mủ trôm, rễ cây dâu tằm, gấc, ngọc trai, dịch nhầy ốc sên, sữa dê, lô hội, nghệ, mơ, hoa hồng, sữa bò, dưa leo, sâm ngọc linh
				</div>
			</div>
	</div>
</div>

<div class="bottom-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-3"><img src="<?php echo THEME_URL; ?>/images/dkbct.png" width="115"
                                       style="margin-right: 12px;"><img
                    src="<?php echo THEME_URL; ?>/images/hvnclc.jpg" width="90"></div>
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