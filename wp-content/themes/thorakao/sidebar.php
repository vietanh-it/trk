<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 26/05/2016
 * Time: 11:12 SA
 */

$post_ctrl = \TVA\Controllers\PostController::init();

$theo_loai = $post_ctrl->getTermList('theo-loai');
$theo_cong_dung = $post_ctrl->getTermList('theo-cong-dung'); ?>

<div class="sidebar-scroll">

    <?php if (pll_current_language('slug') == 'vi') { ?>
        <h3 class="mt-0">Theo loại</h3>
    <?php } else { ?>
        <h3 class="mt-0">By Type</h3>
    <?php } ?>
    <ul class="nav nav-stacked">
        <?php foreach ($theo_loai as $key => $item) {
            $item->permalink = get_term_link($item); ?>

            <li><a href="<?php echo $item->permalink; ?>"><i class="fa fa-angle-right" style="padding-right: 5px;"></i> <?php echo $item->name; ?></a></li>

        <?php } ?>
    </ul>

    <?php if (pll_current_language('slug') == 'vi') { ?>
        <h3 class="mt-0">Theo công dụng</h3>
    <?php } else { ?>
        <h3 class="mt-0">By Effect</h3>
    <?php } ?>
    <ul class="nav nav-stacked">
        <?php foreach ($theo_cong_dung as $key => $item) {
            $item->permalink = get_term_link($item); ?>

            <li><a href="<?php echo $item->permalink; ?>"><i class="fa fa-angle-right" style="padding-right: 5px;"></i> <?php echo $item->name; ?></a></li>

        <?php } ?>
    </ul>
</div>