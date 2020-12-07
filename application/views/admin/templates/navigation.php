<?php $uri = $this->uri->segment(2); ?>
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
					<a class="waves-effect" href="<?php echo base_url('admin/dashboard'); ?>"><i class="menu-icon mdi mdi-home"></i><span>Dashboard</span></a>
				</li>
				
				<li class="<?php echo (in_array($uri,['users-list', 'users-add', 'users-edit', 'users-view']))?'current active':''; ?>">
					<a class="waves-effect" href="<?php echo base_url('admin/users-list'); ?>"><i class="menu-icon fa fa-users"></i><span>Users</span></a>
				</li>
				
				<li class="<?php echo (in_array($uri,['partner-list', 'partner-add', 'partner-edit', 'partner-view']))?'current active':''; ?>">
					<a class="waves-effect" href="<?php echo base_url('admin/partner-list'); ?>"><i class="menu-icon fa fa-user"></i><span>Partner</span></a>
				</li>
				
				<li class="<?php echo (in_array($uri,['station-list', 'station-add', 'station-edit', 'station-view']))?'current active':''; ?>">
					<a class="waves-effect" href="<?php echo base_url('admin/station-list'); ?>"><i class="menu-icon mdi mdi-ev-station"></i><span>Station</span></a>
				</li>
				
				            
				<?php $menuArr = ['vehicle-list', 'vehicle-add', 'vehicle-edit', 'vehicle-view', 'make-list', 'make-add', 'make-edit', 'make-view', 'model-list', 'model-add', 'model-edit', 'model-view']; ?>
				<?php $className = (in_array($uri, $menuArr))?'current active':''; ?>
				<li class="<?php echo $className; ?>">
					<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon mdi mdi-car"></i><span>Manage Vehicle</span><span class="menu-arrow fa fa-angle-down"></span></a>
					<ul class="sub-menu js__content">
                  <li><a class="<?php echo (in_array($uri,['make-list', 'make-add', 'make-edit', 'make-view']))?'current':''; ?>" href="<?php echo base_url('admin/make-list'); ?>">Make</a></li>
                  <li><a class="<?php echo (in_array($uri,['model-list', 'model-add', 'model-edit', 'model-view']))?'current':''; ?>" href="<?php echo base_url('admin/model-list'); ?>">Model</a></li>
                  <li><a class="<?php echo (in_array($uri,['vehicle-list', 'vehicle-add', 'vehicle-edit', 'vehicle-view']))?'current':''; ?>" href="<?php echo base_url('admin/vehicle-list'); ?>">Vehicle</a></li>
					</ul>
				</li>
				
				<?php $menuArr = ['subscription-list', 'subscription-add', 'subscription-edit', 'subscription-view']; ?>
				<?php $className = (in_array($uri, $menuArr))?'current active':''; ?>
				<li class="<?php echo $className; ?>">
					<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-money"></i><span>Subscription</span><span class="menu-arrow fa fa-angle-down"></span></a>
					<ul class="sub-menu js__content">
						<li><a class="<?php echo (in_array($uri,['subscription-list', 'subscription-edit', 'subscription-view']))?'current':''; ?>" href="<?php echo base_url('admin/subscription-list'); ?>">Subscription List</a></li>
						<li><a class="<?php echo (in_array($uri,['subscription-add']))?'current':''; ?>" href="<?php echo base_url('admin/subscription-add'); ?>">Add New Subscription</a></li>
					</ul>
				</li>
				
				<?php $menuArr = ['cms-list', 'cms-add', 'cms-edit', 'cms-view']; ?>
				<?php $className = (in_array($uri, $menuArr))?'current active':''; ?>
				<li class="<?php echo $className; ?>">
					<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-code"></i><span>Static Pages</span><span class="menu-arrow fa fa-angle-down"></span></a>
					<ul class="sub-menu js__content">
						<li><a class="<?php echo (in_array($uri,['cms-list', 'cms-edit', 'cms-view']))?'current':''; ?>" href="<?php echo base_url('admin/cms-list'); ?>">Static Page List</a></li>
						<li><a class="<?php echo (in_array($uri,['cms-add']))?'current':''; ?>" href="<?php echo base_url('admin/cms-add'); ?>">Add New Static Page</a></li>
					</ul>
				</li>
				
				<?php $menuArr = ['email-template-list', 'email-template-add', 'email-template-edit', 'email-template-view', 'sms-template-list', 'sms-template-add', 'sms-template-edit', 'sms-template-view']; ?>
				<?php $className = (in_array($uri, $menuArr))?'current active':''; ?>
				<li class="<?php echo $className; ?>">
					<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-pencil-square-o"></i><span>Templates</span><span class="menu-arrow fa fa-angle-down"></span></a>
					<ul class="sub-menu js__content">
						<li><a class="<?php echo (in_array($uri,['email-template-list', 'email-template-edit', 'email-template-view']))?'current':''; ?>" href="<?php echo base_url('admin/email-template-list'); ?>">Email Template</a></li>
						<li><a class="<?php echo (in_array($uri,['email-template-add']))?'current':''; ?>" href="<?php echo base_url('admin/email-template-add'); ?>">Add New Email Template</a></li>
						<!--<li><a class="<?php echo (in_array($uri,['sms-template-list', 'sms-template-edit', 'sms-template-view']))?'current':''; ?>" href="<?php echo base_url('admin/sms-template-list'); ?>">SMS Template</a></li>
						<li><a class="<?php echo (in_array($uri,['sms-template-add']))?'current':''; ?>" href="<?php echo base_url('admin/sms-template-add'); ?>">Add New SMS Template</a></li>-->
					</ul>
				</li>				
			</ul>
		</div>
	</div>
</div>