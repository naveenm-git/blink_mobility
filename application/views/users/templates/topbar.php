<style>
.fixed-navbar .ico-item{
	margin: 0;
}
.fa-globe.mnu:before {
  margin-left: 7px;
}
.mnu{
	padding: 6px 0px;
	text-align: center;
	border-radius: 50px;
	line-height: normal !important;
}
</style>
<div class="fixed-navbar">
	<div class="pull-left">
		<button type="button" class="menu-mobile-button glyphicon glyphicon-menu-hamburger js__menu_mobile"></button>
		<h1 class="page-title"><?php echo strtoupper($siteTitle); ?></h1>
	</div>
	<div class="pull-right">
		<div class="ico-item">
			<strong style="margin-right:10px;font-size:14px;text-transform:uppercase;cursor:default;"><?php echo $session_user; ?></strong>
			<ul class="sub-ico-item">
				<?php if($user_type=='superadmin'){ ?>
				<li><a href="<?php echo base_url('admin/settings'); ?>" title="Admin Settings"><i class="fa fa-cog">&nbsp;</i> Settings</a></li>
				<?php } ?>
				<li><a class="sign-out" href="#" title="Logout"><i class="fa fa-power-off mnu">&nbsp;</i> Logout</a></li>
			</ul>
		</div>
	</div>
</div>