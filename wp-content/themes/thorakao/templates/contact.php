<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 08/06/2016
 * Time: 11:48 CH
 * Template Name: Contact
 */

get_header(); ?>

    <div class="breadcrumb no-padding">
        <div class="container">
            <ul class="no-padding no-margin">
                <li>
                    <a href="<?php echo WP_SITEURL; ?>">
                        <?php echo (pll_current_language() == 'vi') ? 'Trang chủ' : 'Home'; ?>
                    </a>
                    <span class="slash">/</span>
                </li>
                <li class="last">
                    <a href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/lien-he' : '/contact'; ?>">
                        <?php echo (pll_current_language() == 'vi') ? 'Liên hệ' : 'Contact'; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <?php get_sidebar(); ?>
                </div>
                <div class="col-md-9 detail left-divider">
                    <div class="row">
                        <div class="col-md-12">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4524428813065!2d106.68155486433669!3d10.776617762138079!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f25b54d7a35%3A0xab2c9fc508766970!2sThorakao!5e0!3m2!1svi!2s!4v1462126805281"
                                height="350" width="600" frameborder="0" allowfullscreen
                                style="width: 100%; margin-bottom: 30px;"></iframe>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php the_content(); ?>
                                    <!--Thorakao được thành lập từ năm 1961, với một số mặt hàng truyền thống-->
                                    <!--kem dưỡng da trân châu, dầu gội đầu Hoa bưởi, xà bông thơm, nước bóng tóc Parafine &-->
                                    <!--Brillantine mang nhãn hiệu Thorakao.<br><br>Sản phẩm với thương hiệu Thorakao nhanh-->
                                    <!--chóng nổi tiếng, chiếm được lòng người tiêu dùng và đã có bằng sáng chế số 1779 ngày-->
                                    <!--15/11/1968 được bán rộng rãi trên toàn miền Nam, Việt Nam lúc bấy giờ.<br><br>Năm 1969-->
                                    <!--hãng đã mở chi nhánh tại Campuchia và bắt đầu xuất khẩu sản phẩm sang các sang các nước-->
                                    <!--Ðông Nam Á.-->
                                    <!--Trải qua những bước tiến đầy khó khăn, công ty đã thiết lập thị trường vững chắc trên-->
                                    <!--toàn lãnh thổ Việt Nam với sản phẩm ngày càng phong phú về chủng loại, mẫu mã, nhãn-->
                                    <!--hiệu. Ngoài thương hiệu đầy uy tín Thorakao, Công ty đã có những thương hiệu nổi tiếng-->
                                    <!--khác được sự dụng trên nhiều thị trường quốc ngoại khác nhau.-->
                                </div>
                                <div class="col-md-6">
                                    <form action="javascript:void(0)" class="uk-form contact-form">
                                        <fieldset>
                                            <legend>
                                                <div class="detail__title mt-0">Liên hệ</div>
                                            </legend>
                                            <div class="uk-form-row">
                                                <label class="uk-form-label">Họ và tên</label>
                                                <div class="uk-form-controls">
                                                    <input type="text" name="name" placeholder="Nhập họ và tên"
                                                           class="uk-width-1-1">
                                                </div>
                                            </div>
                                            <div class="uk-form-row">
                                                <label class="uk-form-label">Email</label>
                                                <div class="uk-form-controls">
                                                    <input type="email" name="email" placeholder="Nhập email"
                                                           class="uk-width-1-1">
                                                </div>
                                            </div>
                                            <div class="uk-form-row">
                                                <label class="uk-form-label">Số điện thoại</label>
                                                <div class="uk-form-controls">
                                                    <input type="text" name="phone" placeholder="Nhập số điện thoại"
                                                           class="uk-width-1-1">
                                                </div>
                                            </div>
                                            <div class="uk-form-row">
                                                <label class="uk-form-label" for="note">Ghi chú</label>
                                                <div class="uk-form-controls">
                                                    <textarea class="uk-width-1-1" name="note" id="note"
                                                              placeholder="Nhập ghi chú"></textarea>
                                                </div>
                                            </div>
                                            <div class="uk-form-row">
                                                <div class="uk-form-controls">
                                                    <button type="submit" class="uk-button uk-button-primary">Gửi
                                                    </button>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <input type="hidden" name="action" value="trk_ajax_handler_account">
                                        <input type="hidden" name="method" value="ContactForm">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var $ = jQuery.noConflict();
        $(document).ready(function () {

            $('.contact-form').validate({
                rules: {
                    name: "required",
                    phone: "required",
                    email: "required",
                    note: "required"
                },
                messages: {
                    name: "Vui lòng nhập tên.",
                    phone: "Vui lòng nhập số điện thoại.",
                    email: "Vui lòng nhập email.",
                    note: "Vui lòng nhập ghi chú."
                },
                errorPlacement: function (error, element) {
                    element.attr('data-original-title', error.text())
                        .attr('data-toggle', 'tooltip')
                        .attr('data-placement', 'top');
                    $(element).tooltip('show');
                },
                unhighlight: function (element) {
                    $(element)
                        .removeAttr('data-toggle')
                        .removeAttr('data-original-title')
                        .removeAttr('data-placement')
                        .removeClass('error');
                    $(element).unbind("tooltip");
                },
                submitHandler: function (form) {
                    var obj = $(form);
                    $.ajax({
                        url: ajaxurl,
                        type: "post",
                        dataType: 'json',
                        data: obj.serialize(),
                        beforeSend: function () {
                            $('input, button[type=submit]', obj).attr('disabled', true).css({'opacity': '0.5'});
//                            $('.btn-submit-checkout').attr('disabled', true).css({'opacity': '0.5'});
                        },
                        success: function (data) {
                            $('input, button[type=submit]', obj).attr('disabled', false).css({'opacity': 1});
                            // $('.btn-submit-checkout').attr('disabled', false).css({'opacity': 1});
                            if (data.status == 'success') {
                                swal({
                                    title: 'Thành công',
                                    text: "<p style='font-weight: bold;color: black'>Gửi liên hệ thành công</p>",
                                    confirmButtonColor: "#80b501",
                                    type: "success",
                                    html: true
                                });
                            }
                            else {
                                swal({"title": "Error", "text": data.message, "type": "error", html: true});
                            }
                        }
                    });
                }
            });

        });
    </script>

<?php get_footer();