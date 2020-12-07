<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Forgot Password - <?php echo $siteTitle; ?></title>
		<link href="<?php echo $site_favicon;?>" rel="icon" type="image/png">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/styles/style.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugin/waves/waves.min.css">
		<!-- Toastr -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugin/toastr/toastr.css">
		<style>
			#single-wrapper {
				min-height: 100%;
				background: url('<?php echo base_url().'images/logo/'.$background_image; ?>') top center repeat;
				overflow: hidden;
				max-width: 100%;
				padding: 0px 15px 0px 15px;
				background-size: cover;
			}
		</style>
	</head>
	<body>
		<div id="single-wrapper">
			<?php if($pagename=='forgot_password'){ ?>
			<form action="<?php echo base_url('admin/submit-forgot-password');?>" class="frm-single" data-toggle="validator" method="post" accept-charset="utf-8">
				<div class="inside">
					
					<div class="frm-title">
						<img src="<?php echo base_url('images/logo/').$site_logo; ?>" onerror="this.style.display='none'" alt="Admin" class="ico-img" style="height: 70px;">
					</div>
					
					<div class="frm-input">
						<input type="text" name="admin_email" placeholder="Email" data-error="Please enter a valid email" class="frm-inp" title="Please enter your email">
						<i class="fa fa-envelope frm-ico"></i>
					</div>
					
					<div class="clearfix margin-bottom-20">
						<div class="pull-right"><a href="<?php echo base_url().'admin';?>" class="a-link"><i class="fa fa-sign-in"></i>Back to Login Page</a></div>
					</div>
					
					<button type="submit" class="frm-submit">Send New Password<i class="fa fa-arrow-circle-right"></i></button>
				</div>
			</form>
			<?php } ?>
			
			<?php if($pagename=='verify_code'){ ?>
			<form action="#" class="frm-single">
				<div class="inside">
					<div class="frm-title">
						<img src="<?php echo $site_logo; ?>" onerror="this.style.display='none'" alt="Admin" class="ico-img" style="height: 70px;">
					</div>
					<p class="text-center">New password sent to <strong><?php echo $forgot_email; ?></strong>.<br/>Please check your mail.</p>
					<div class="frm-footer">
						<a href="<?php echo base_url().'admin'; ?>" class="a-link"><i class="fa fa-sign-in"></i>Back to Login Page.</a>
					</div>
				</div>
			</form>
			<?php } ?>
		</div>
		<script src="<?php echo base_url(); ?>assets/scripts/jquery.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/scripts/modernizr.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugin/bootstrap/js/bootstrap.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugin/nprogress/nprogress.js"></script>
		<script src="<?php echo base_url(); ?>assets/plugin/waves/waves.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/scripts/main.min.js"></script>
		<!-- Toastr -->
		<script src="<?php echo base_url(); ?>assets/plugin/toastr/toastr.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/scripts/toastr.demo.min.js"></script>
		
		<script type="text/javascript">
		<?php if($pagename=='forgot_password'){ ?>
		$(document).on('submit', 'form', function(e){
			toastr.remove();
			$.each($('.frm-inp'), function(i, v){
				if($(this).val()!=''){
					if($(this).attr('name') == 'admin_email'){
						var regex = /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
						if(!regex.test($(this).val())){
							var $toastlast = toastr['error']('Invalid email', 'Oops!');
							e.preventDefault();
							return;
						}
					} else {
						$('form.').submit();
					}
				} else {
					var $toastlast = toastr['error']($(this).data('error'), 'Oops!');
					e.preventDefault();
					return;
				}
			});
		});
		<?php } ?>
		
		toastr.options = { "showDuration": 300, "hideDuration": 300, "timeOut": 1800, "showMethod": "fadeIn", "hideMethod": "fadeOut" };
		</script>
		<?php if($this->session->flashdata('sErrMSG') != '') { ?>
		<script type="text/javascript">
			<?php 
			$sErrMSGdecoded = base64_decode($this->session->flashdata('sErrMSG'));
			$sErrMSGKeydecoded = base64_decode($this->session->flashdata('sErrMSGKey'));
			$sErrMSGType = $this->session->flashdata('sErrMSGType')	?>
			msg = "<?php echo $sErrMSGdecoded; ?>";
			title = "<?php echo $sErrMSGKeydecoded; ?>";
			<?php if($sErrMSGType=='success'){ ?>	
				var $toast = toastr['<?php echo $sErrMSGType; ?>'](msg, title);
				$toastlast = $toast;
			<?php } ?>
			<?php if($sErrMSGType=='error'){ ?>	
				var $toast = toastr['<?php echo $sErrMSGType; ?>'](msg, title);
				$toastlast = $toast;
			<?php } ?>
		</script>
		<?php } ?>
	</body>
</html>