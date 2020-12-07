<?php $user_id = $this->uri->segment(2); ?>
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
					<a class="waves-effect" href="<?php echo base_url(USERURL.'/'.$user_id.'/dashboard'); ?>"><i class="menu-icon mdi mdi-home"></i><span>Dashboard</span></a>
				</li>
				
				<li class="<?php echo (in_array($uri,['account']))?'current active':''; ?>">
					<a class="waves-effect" href="<?php echo base_url(USERURL.'/'.$user_id.'/account'); ?>"><i class="menu-icon fa fa-users"></i><span>Account</span></a>
				</li>
				
				<li class="<?php echo (in_array($uri,['documents']))?'current active':''; ?>">
					<a class="waves-effect" href="<?php echo base_url(USERURL.'/'.$user_id.'/documents'); ?>"><i class="menu-icon mdi mdi-folder-multiple-outline"></i><span>Documents</span></a>
				</li>
				
				<li class="<?php echo (in_array($uri,['subscription']))?'current active':''; ?>">
					<a class="waves-effect" href="<?php echo base_url(USERURL.'/'.$user_id.'/subscription'); ?>"><i class="menu-icon fa fa-money"></i><span>Subscription</span></a>
				</li>
				
				<li class="<?php echo (in_array($uri,['favorite-address']))?'current active':''; ?>">
					<a class="waves-effect" href="<?php echo base_url(USERURL.'/'.$user_id.'/favorite-address'); ?>"><i class="menu-icon fa fa-heart"></i><span>Favorite Address</span></a>
				</li>
				
			</ul>
		</div>
	</div>
</div>