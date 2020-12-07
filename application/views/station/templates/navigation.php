<?php $station_id = $this->uri->segment(2); ?>
<?php $uri = $this->uri->segment(3); ?>
<?php $getdata = $this->input->get(); ?>
<div class="main-menu">
	<header class="header">
		<a href="<?php echo base_url('admin/dashboard'); ?>" class="logo"><img style="width: 90px;" src="<?php echo base_url('images/logo/').$site_logo; ?>" /></a>
		<button type="button" class="button-close fa fa-times js__menu_close"></button>
	</header>
	<div class="content">
		<div class="navigation">
			<ul class="menu js__accordion">
				<li class="<?php echo ($uri=='dashboard')?'current active':''; ?>">
					<a class="waves-effect" href="<?php echo base_url(STATIONURL.'/'.$station_id.'/dashboard'); ?>"><i class="menu-icon mdi mdi-home"></i><span>Dashboard</span></a>
				</li>
				
				<li class="<?php echo (in_array($uri,['parking']))?'current active':''; ?>">
					<a class="waves-effect" href="<?php echo base_url(STATIONURL.'/'.$station_id.'/parking'); ?>"><i class="menu-icon mdi mdi-parking"></i><span>Parking</span></a>
				</li>				
			</ul>
		</div>
	</div>
</div>