<?php if ($theme->header_css != '') {
    echo $theme->header_css;
} ?>

<?php if (isset($cssAttributes['main_background_color']) || isset($cssAttributes['default_tc'])) : ?>
body, #page-content-wrapper {
    <?php if (isset($cssAttributes['main_background_color'])) : ?>background-color:#<?php echo $cssAttributes['main_background_color']?>;<?php endif;?>
    <?php if (isset($cssAttributes['default_tc'])) : ?>color:#<?php echo $cssAttributes['default_tc']?>;<?php endif;?>
}
<?php endif;?>

<?php if (isset($cssAttributes['link_tc'])) : ?>
a {
    color:#<?php echo $cssAttributes['link_tc']?>
}
<?php endif;?>

<?php if (isset($cssAttributes['panel_background_color']) || isset($cssAttributes['panel_border_color'])) : ?>
.card>.card-header{
    <?php if (isset($cssAttributes['panel_background_color'])) : ?>background-color:#<?php echo $cssAttributes['panel_background_color']?>;<?php endif;?>
    <?php if (isset($cssAttributes['panel_border_color'])) : ?>border-color:#<?php echo $cssAttributes['panel_border_color']?>;<?php endif;?>
}

.card{
    <?php if (isset($cssAttributes['panel_border_color'])) : ?>border-color:#<?php echo $cssAttributes['panel_border_color']?>;<?php endif;?>
}
<?php endif;?>

<?php if (isset($cssAttributes['panel_mbc'])) : ?>
.card {
    background-color:#<?php echo $cssAttributes['panel_mbc'];?>
}
<?php endif; ?>

<?php if (isset($cssAttributes['mactive_bc']) || isset($cssAttributes['mactive_bc'])) : ?>
.nav>li>a.nav-link:focus, .nav>li>a.nav-link:hover,.sidebar li.active > a.nav-link{
    background-color:#<?php echo $cssAttributes['mactive_bc']?>;
}
<?php endif;?>

<?php if (isset($cssAttributes['men_col'])) : ?>
.sidebar .nav-second-level li a,.sidebar ul li a.nav-link {
    color:#<?php echo $cssAttributes['men_col']?>;
}
<?php endif;?>

<?php if (isset($cssAttributes['tab_bc'])) : ?>
.nav-pills>li>a.nav-link.active, .nav-pills>li>a.nav-link.active:focus, .nav-pills>li>a.nav-link.active:hover,
.nav-pills>li>a.nav-link:hover,
.nav-tabs>li>a.nav-link.active, .nav-tabs>li>a.nav-link.active:focus, .nav-tabs>li>a.nav-link.active:hover,.nav-tabs>li>a.nav-link:hover {
    background-color:#<?php echo $cssAttributes['tab_bc'];?>
}
.navbar-default .navbar-nav>.open>a, .navbar-default .navbar-nav>.open>a:focus, .navbar-default .navbar-nav>.open>a:hover{
    background-color:#<?php echo $cssAttributes['tab_bc']?>;
}
<?php endif; ?>

<?php if (isset($cssAttributes['tab_boc'])) : ?>
.nav-pills>li>a,
.nav-tabs>li>a.active, .nav-tabs>li>a.active:focus, .nav-tabs>li>a.active:hover{
    border-color:#<?php echo $cssAttributes['tab_boc'];?>;
}
<?php endif; ?>

<?php if (isset($cssAttributes['tab_tc'])) : ?>
.nav-pills>li>a,
.nav-tabs>li>a{
    color:#<?php echo $cssAttributes['tab_tc'];?>;
}
<?php endif; ?>

<?php if (isset($cssAttributes['tab_atc'])) : ?>
.nav-pills>li>a.active, .nav-pills>li>a.active:focus, .nav-pills>li>a.active:hover,
.nav-tabs>li>a.active, .nav-tabs>li>a.active:focus, .nav-tabs>li>a.active:hover{
    color:#<?php echo $cssAttributes['tab_atc'];?>;
}
<?php endif; ?>

<?php if (isset($cssAttributes['drpdown_bgc']) || isset($cssAttributes['drpdown_boc'])) : ?>
.dropdown-menu {
    <?php if (isset($cssAttributes['drpdown_bgc'])) : ?>background-color:#<?php echo $cssAttributes['drpdown_bgc']?>;<?php endif;?>
    <?php if (isset($cssAttributes['drpdown_boc'])) : ?>border-color:#<?php echo $cssAttributes['drpdown_boc']?>;<?php endif;?>
}
<?php endif; ?>

<?php if (isset($cssAttributes['drpdown_hbgc'])) : ?>
.dropdown-menu > a.dropdown-item:focus, .dropdown-menu>a.dropdown-item:hover{
    background-color:#<?php echo $cssAttributes['drpdown_hbgc']?>;
}
<?php endif; ?>

<?php if (isset($cssAttributes['nvbar_bc']) || isset($cssAttributes['nvbar_pbc'])) : ?>
.navbar-light{
    <?php if (isset($cssAttributes['nvbar_bc'])) : ?>background-color:#<?php echo $cssAttributes['nvbar_bc']?>!important;<?php endif;?>
    <?php if (isset($cssAttributes['nvbar_pbc'])) : ?>border-color:#<?php echo $cssAttributes['nvbar_pbc']?>!important;<?php endif;?>
}
<?php endif;?>

<?php if (isset($cssAttributes['bcrumb_bgc']) || isset($cssAttributes['bcrumb_boc'])) : ?>
.breadcrumb{
    <?php if (isset($cssAttributes['bcrumb_bgc'])) : ?>background-color: #<?php echo $cssAttributes['bcrumb_bgc']?>;<?php endif;?>
    <?php if (isset($cssAttributes['bcrumb_boc'])) : ?>border-bottom: 1px solid #<?php echo $cssAttributes['bcrumb_boc']?>!important;<?php endif;?>
}

<?php if (isset($cssAttributes['bcrumb_bgc'])) : ?>
.navbar-lhc-right {
   <?php if (isset($cssAttributes['bcrumb_bgc'])) : ?>background-color: #<?php echo $cssAttributes['bcrumb_bgc']?>;<?php endif;?>
}
<?php endif;?>

<?php endif;?>

<?php if (isset($cssAttributes['chat_onl_bc']) || isset($cssAttributes['chat_onl_bc'])) : ?>
.user-online-row td {
    background-color:#<?php echo $cssAttributes['chat_onl_bc']?>;
}
<?php endif;?>

<?php if (isset($cssAttributes['tbl_boc'])) : ?>
.table>thead>tr>th{
    border-bottom: 2px solid #<?php echo $cssAttributes['tbl_boc']?>;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    border-top: 1px solid #<?php echo $cssAttributes['tbl_boc']?>;
}
<?php endif;?>

<?php if (isset($cssAttributes['nvb_tgl_bgc'])) : ?>
.navbar-light .navbar-toggler:focus, .navbar .navbar-toggler:hover {
    background-color: #<?php echo $cssAttributes['nvb_tgl_bgc']?>;
}
<?php endif;?>

<?php if (isset($cssAttributes['nvb_tgl_bc'])) : ?>
.navbar-light .navbar-toggler{
    border-color: #<?php echo $cssAttributes['nvb_tgl_bc']?>;
}
<?php endif;?>

<?php if (isset($cssAttributes['nvb_li_clr'])) : ?>
.navbar-light .navbar-nav>li>a.nav-link{
    color: #<?php echo $cssAttributes['nvb_li_clr']?>;
}
<?php endif; ?>

<?php if (isset($cssAttributes['nvb_lih_clr'])) : ?>
.navbar-light .navbar-nav>li>a.nav-link:focus, .navbar-light .navbar-nav>li>a.nav-link:hover, .navbar-default .navbar-nav>li>a.nav-link:hover{
    color: #<?php echo $cssAttributes['nvb_lih_clr']?>;
}
<?php endif; ?>

<?php if (isset($cssAttributes['btnd_clr']) || isset($cssAttributes['btnd_bc']) || isset($cssAttributes['btnd_boc'])) : ?>
.btn-secondary {
    <?php if (isset($cssAttributes['btnd_clr'])) : ?>color: #<?php echo $cssAttributes['btnd_clr']?>;<?php endif;?>
    <?php if (isset($cssAttributes['btnd_bc'])) : ?>background-color: #<?php echo $cssAttributes['btnd_bc']?>;<?php endif;?>
    <?php if (isset($cssAttributes['btnd_boc'])) : ?>border-color: #<?php echo $cssAttributes['btnd_boc']?>;<?php endif;?>
}
<?php endif; ?>

<?php if (isset($cssAttributes['btnda_clr']) || isset($cssAttributes['btnda_bc']) || isset($cssAttributes['btnda_boc'])) : ?>
.btn-secondary:hover,.btn-secondary.active.focus,.btn-secondary.active:focus,.btn-secondary.active:hover,.btn-secondary:active.focus,.btn-secondary:active:focus,.btn-secondary:active:hover{
    <?php if (isset($cssAttributes['btnda_clr'])) : ?>color: #<?php echo $cssAttributes['btnda_clr']?>;<?php endif;?>
    <?php if (isset($cssAttributes['btnda_bc'])) : ?>background-color: #<?php echo $cssAttributes['btnda_bc']?>;<?php endif;?>
    <?php if (isset($cssAttributes['btnda_boc'])) : ?>border-color: #<?php echo $cssAttributes['btnda_boc']?>;<?php endif;?>
}
<?php endif; ?>

<?php if (isset($cssAttributes['btnp_clr']) || isset($cssAttributes['btnp_bc']) || isset($cssAttributes['btnp_boc'])) : ?>
.btn-primary {
    <?php if (isset($cssAttributes['btnp_clr'])) : ?>color: #<?php echo $cssAttributes['btnp_clr']?>;<?php endif;?>
    <?php if (isset($cssAttributes['btnp_bc'])) : ?>background-color: #<?php echo $cssAttributes['btnp_bc']?>;<?php endif;?>
    <?php if (isset($cssAttributes['btnp_boc'])) : ?>border-color: #<?php echo $cssAttributes['btnp_boc']?>;<?php endif;?>
}
<?php endif; ?>

<?php if (isset($cssAttributes['btnpa_clr']) || isset($cssAttributes['btnpa_bc']) || isset($cssAttributes['btnpa_boc'])) : ?>
.btn-primary:hover,.btn-primary.active.focus,.btn-primary.active:focus,.btn-primary.active:hover,.btn-primary:active.focus,.btn-primary:active:focus,.btn-primary:active:hover{
    <?php if (isset($cssAttributes['btnpa_clr'])) : ?>color: #<?php echo $cssAttributes['btnpa_clr']?>;<?php endif;?>
    <?php if (isset($cssAttributes['btnpa_bc'])) : ?>background-color: #<?php echo $cssAttributes['btnpa_bc']?>;<?php endif;?>
    <?php if (isset($cssAttributes['btnpa_boc'])) : ?>border-color: #<?php echo $cssAttributes['btnpa_boc']?>;<?php endif;?>
}
<?php endif; ?>

<?php if (isset($cssAttributes['buble_operator_background']) || isset($cssAttributes['buble_operator_text_color'])) : ?>
    div.message-admin div.msg-body{
        <?php if (isset($cssAttributes['buble_operator_background'])) : ?>background-color: #<?php echo $cssAttributes['buble_operator_background'];?>;<?php endif; ?>
        <?php if (isset($cssAttributes['buble_operator_text_color'])) : ?>color: #<?php echo $cssAttributes['buble_operator_text_color'];?>;<?php endif; ?>
    }

    <?php if (isset($cssAttributes['buble_operator_text_color'])) : ?>
    div.message-admin div.msg-body a.link{
        color: #<?php echo $cssAttributes['buble_operator_text_color'];?>;
    }
    <?php endif; ?>

<?php endif; ?>

<?php if (isset($cssAttributes['buble_operator_other_background']) || isset($cssAttributes['buble_operator_other_text_color'])) : ?>
div.other-operator div.msg-body{
    <?php if (isset($cssAttributes['buble_operator_other_background'])) : ?>background-color: #<?php echo $cssAttributes['buble_operator_other_background'];?>;<?php endif;?>
    <?php if (isset($cssAttributes['buble_operator_other_text_color'])) : ?>color: #<?php echo $cssAttributes['buble_operator_other_text_color'];?>;<?php endif; ?>
}

<?php if (isset($cssAttributes['buble_operator_other_text_color'])) : ?>
div.other-operator div.msg-body a.link{
    color: #<?php echo $cssAttributes['buble_operator_other_text_color'];?>;
}
<?php endif; ?>

<?php endif; ?>

<?php if (isset($cssAttributes['buble_operator_title_color'])) : ?>
    .op-tit{
        color: #<?php echo $cssAttributes['buble_operator_title_color'];?>
    }
<?php endif; ?>

<?php if (isset($cssAttributes['buble_operator_other_title_color'])) : ?>
    div.other-operator .op-tit{
        color: #<?php echo $cssAttributes['buble_operator_other_title_color'];?>
    }
<?php endif; ?>

<?php if (isset($cssAttributes['buble_visitor_text_color']) || isset($cssAttributes['buble_visitor_background'])) : ?>
    div.message-row.response div.msg-body{
        <?php if (isset($cssAttributes['buble_visitor_background'])) : ?>background-color: #<?php echo $cssAttributes['buble_visitor_background'];?>;<?php endif; ?>
        <?php if (isset($cssAttributes['buble_visitor_text_color'])) : ?>color: #<?php echo $cssAttributes['buble_visitor_text_color'];?>;<?php endif; ?>
    }

    <?php if (isset($cssAttributes['buble_visitor_text_color'])) : ?>
    div.message-row.response div.msg-body a.link{
        <?php if (isset($cssAttributes['buble_visitor_text_color'])) : ?>color: #<?php echo $cssAttributes['buble_visitor_text_color'];?>;<?php endif; ?>
    }
    <?php endif; ?>

<?php endif; ?>

<?php if (isset($cssAttributes['buble_visitor_title_color'])) : ?>
    .vis-tit{
        color: #<?php echo $cssAttributes['buble_visitor_title_color'];?>
    }
<?php endif; ?>

<?php if (isset($cssAttributes['chat_bg'])) : ?>
    .msgBlock-admin{
        background-color: #<?php echo $cssAttributes['chat_bg'];?>
    }
<?php endif; ?>

<?php if (isset($cssAttributes['time_color'])) : ?>
    .msg-date{
        color: #<?php echo $cssAttributes['time_color'];?>
    }
<?php endif; ?>


<?php if (isset($cssAttributes['buble_sys_background']) || isset($cssAttributes['buble_sys_title_color']) ) : ?>
    .sys-tit{
        <?php if (isset($cssAttributes['buble_sys_background'])) : ?>background-color: #<?php echo $cssAttributes['buble_sys_background'];?>;<?php endif; ?>
        <?php if (isset($cssAttributes['buble_sys_title_color'])) : ?>color: #<?php echo $cssAttributes['buble_sys_title_color'];?>;<?php endif; ?>
    }
<?php endif; ?>

<?php if (isset($cssAttributes['buble_sys_text_color'])) : ?>
div.system-response div.msg-body,
div.system-response div.msg-body a.link{
    color: #<?php echo $cssAttributes['buble_sys_text_color'];?>
}
<?php endif; ?>


<?php /*
<div class="msgBlock" style="" id="messagesBlock">
    <div class="message-row response" id="msg-10459" data-op-id="0">
        <div class="msg-date">10:14:39</div>
        <span style="color:#{{bactract_bg_color_buble_visitor_title_color}}" class="usr-tit vis-tit" role="button"><i class="material-icons chat-operators mi-fs15 mr-0">account_box</i>Operator</span>
        <div class="msg-body" style="background-color: #{{bactract_bg_color_buble_visitor_background}};color:#{{bactract_bg_color_buble_visitor_text_color}}">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
    </div>
    <div class="message-row message-admin operator-changes" id="msg-10463" data-op-id="1">
        <div class="msg-date">10:18:22</div>
        <span style="color:#{{bactract_bg_color_buble_operator_title_color}}" class="usr-tit op-tit" ><i class="material-icons chat-operators mi-fs15 mr-0">face</i>Visitor</span>
        <div class="msg-body" style="color:#{{bactract_bg_color_buble_operator_text_color}};background-color: #{{bactract_bg_color_buble_operator_background}};">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
    </div>
</div>*/