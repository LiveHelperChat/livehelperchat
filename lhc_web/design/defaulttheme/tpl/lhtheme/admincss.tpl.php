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
.panel-default>.card-header{
    <?php if (isset($cssAttributes['panel_background_color'])) : ?>background-color:#<?php echo $cssAttributes['panel_background_color']?>;<?php endif;?>
    <?php if (isset($cssAttributes['panel_border_color'])) : ?>border-color:#<?php echo $cssAttributes['panel_border_color']?>;<?php endif;?>
}

.panel-default{
    <?php if (isset($cssAttributes['panel_border_color'])) : ?>border-color:#<?php echo $cssAttributes['panel_border_color']?>;<?php endif;?>
}
<?php endif;?>

<?php if (isset($cssAttributes['panel_background_color']) || isset($cssAttributes['panel_border_color'])) : ?>
.panel-default>.card-header{
    <?php if (isset($cssAttributes['panel_background_color'])) : ?>background-color:#<?php echo $cssAttributes['panel_background_color']?>;<?php endif;?>
    <?php if (isset($cssAttributes['panel_border_color'])) : ?>border-color:#<?php echo $cssAttributes['panel_border_color']?>;<?php endif;?>
}
<?php endif;?>

<?php if (isset($cssAttributes['panel_mbc'])) : ?>
.panel {
    background-color:#<?php echo $cssAttributes['panel_mbc'];?>
}
<?php endif; ?>

<?php if (isset($cssAttributes['mactive_bc']) || isset($cssAttributes['mactive_bc'])) : ?>
.nav>li>a:focus, .nav>li>a:hover,.sidebar li.active > a{
    background-color:#<?php echo $cssAttributes['mactive_bc']?>;
}
<?php endif;?>

<?php if (isset($cssAttributes['men_col'])) : ?>
.sidebar .nav-second-level li a,.sidebar ul li a {
    color:#<?php echo $cssAttributes['men_col']?>;
}
<?php endif;?>

<?php if (isset($cssAttributes['tab_bc'])) : ?>
.nav-pills>li.active>a, .nav-pills>li.active>a:focus, .nav-pills>li>a.active:hover,
.nav-pills>li>a:hover,
.nav-tabs>li>a.active, .nav-tabs>li>a.active:focus, .nav-tabs>li>a.active:hover,.nav-tabs>li>a:hover {
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
.dropdown-menu > li.dropdown-item > a:focus, .dropdown-menu>li.dropdown-item>a:hover{
    background-color:#<?php echo $cssAttributes['drpdown_hbgc']?>;
}
<?php endif; ?>

<?php if (isset($cssAttributes['nvbar_bc']) || isset($cssAttributes['nvbar_pbc'])) : ?>
.navbar-default{
    <?php if (isset($cssAttributes['nvbar_bc'])) : ?>background-color:#<?php echo $cssAttributes['nvbar_bc']?>;<?php endif;?>
    <?php if (isset($cssAttributes['nvbar_pbc'])) : ?>border-color:#<?php echo $cssAttributes['nvbar_pbc']?>;<?php endif;?>
}
<?php endif;?>

<?php if (isset($cssAttributes['bcrumb_bgc']) || isset($cssAttributes['bcrumb_boc'])) : ?>
.breadcrumb{
    <?php if (isset($cssAttributes['bcrumb_bgc'])) : ?>background-color: #<?php echo $cssAttributes['bcrumb_bgc']?>;<?php endif;?>
    <?php if (isset($cssAttributes['bcrumb_boc'])) : ?>border-bottom: 1px solid #<?php echo $cssAttributes['bcrumb_boc']?>;<?php endif;?>
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
.navbar-light .navbar-toggler:focus, .navbar-default .navbar-toggler:hover {
    background-color: #<?php echo $cssAttributes['nvb_tgl_bgc']?>;
}
<?php endif;?>

<?php if (isset($cssAttributes['nvb_tgl_bc'])) : ?>
.navbar-light .navbar-toggler{
    border-color: #<?php echo $cssAttributes['nvb_tgl_bc']?>;
}
<?php endif;?>

<?php if (isset($cssAttributes['nvb_li_clr'])) : ?>
.navbar-light .navbar-nav>li>a{
    color: #<?php echo $cssAttributes['nvb_li_clr']?>;
}
<?php endif; ?>

<?php if (isset($cssAttributes['nvb_lih_clr'])) : ?>
.navbar-light .navbar-nav>li>a:focus, .navbar-default .navbar-nav>li>a:hover{
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



