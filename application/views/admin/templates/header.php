<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<title><?php echo $heading.' - '.$siteTitle; ?></title>
	<link href="<?php echo base_url('images/logo/').$site_favicon;?>" rel="icon" type="image/png">
	<?php $this->load->view('admin/templates/supporting_css'); ?>
	
	<style>
		.petty-alert, .trail-alert{
			padding: 5px 9px;
			position: fixed;
			bottom: 1em;
			border: 1px solid transparent;
			right: 1em;
			box-shadow: 2px 3px 8px #3e3e3eba;
			background: #188ae2;
			color: #fff;
			font-size: 20px;
			border-radius: 50px;
			cursor: pointer;
			z-index: 9999;
		}
		.petty-alert:hover{
			color: #fbbc05;
			transform: scale(1.1);
		}
		.trail-alert:hover{
			color: #188ae2;
			transform: scale(1.1);
		}
		.petty-info, .trail-info{
			display: none;
			max-width: 20%;
			max-height: 75px;
			position: fixed;
			bottom: 5em;
			border: 1px solid transparent;
			right: 2em;
			box-shadow: 2px 3px 8px #3e3e3eba;
			background: #f7f7f7;
			color: #000;
			font-size: 14px;
			border-radius: 5px;
			z-index: 9999;
		}
		.trail-alert{
			bottom: 1em;
			border: 1px solid transparent;
			right: 4em;
			box-shadow: 2px 3px 8px #3e3e3eba;
			background: #fbbc05;
			color: #fff;
		}
		.trail-info{
			border: 1px solid transparent;
			right: 6em;
			box-shadow: 2px 3px 8px #3e3e3eba;
			background: #f7f7f7;
			color: #000;
		}
	</style>
</head>
<body>
<?php $this->load->view('admin/templates/navigation'); ?>
<?php $this->load->view('admin/templates/topbar'); ?>
