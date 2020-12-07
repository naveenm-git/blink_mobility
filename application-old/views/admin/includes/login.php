<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Admin Login - <?php echo $siteTitle; ?></title>
		<link href="<?php echo base_url('images/logo/').$site_favicon;?>" rel="icon" type="image/png">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/styles/style.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugin/waves/waves.min.css">
		<!-- Toastr -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugin/toastr/toastr.css">
		<style>
			#single-wrapper {
				min-height: 100%;
				<?php if(!empty($background_image)){ ?>
				background: url('<?php echo base_url('images/logo/').$background_image; ?>') top center repeat;
				background-size: cover;
				<?php } ?>
				overflow: hidden;
				max-width: 100%;
				padding: 0px 15px 0px 15px;
			}
         @media (min-width: 1025px){
            .frm-single .frm-submit:hover {
                background: #65a844;
                opacity: 1;
            }
         }
         .frm-single .frm-input .frm-inp:focus{
             border-color: #65a844;
         }
         .frm-single .frm-input .frm-inp::placeholder {
           color: #65a844;
         }
         .frm-single .frm-input .frm-inp::-ms-input-placeholder {
           color: #65a844;
         }
         .frm-single .a-link {
             color: #65a844;
         }
         .frm-single .frm-input .frm-ico{
            color: #65a844;
         }
         .frm-single .a-link:hover {
             color: #063257;
         }
         .frm-single .frm-submit{
            background: #619647;
            border-radius: 5px;
         }
         .frm-single .frm-input .frm-inp {
             width: 100%;
             height: 40px;
             padding: 0px;
             padding-left: 30px;
             border: 1px solid #65a844;
             font-size: 14px;
             line-height: 38px;
             border-left: 3px solid #65a844;
             border-radius: 5px;
         }
		</style>
	</head>
	<body>
		<div id="single-wrapper">
			<form action="<?php echo base_url('do-login');?>" class="frm-single" id="login-form" data-toggle="validator" method="post" accept-charset="utf-8">
				<div class="inside">
				
					<div class="frm-title">
						<img src="<?php echo base_url('images/logo/').$site_logo; ?>" onerror="this.style.display='none'" alt="Admin" class="ico-img" style="height: 70px;">
					</div>
					
					<div class="frm-input">
						<input type="text" name="admin_email" placeholder="Email" data-error="Please enter a valid email" class="frm-inp" title="Please enter your email">
						<i class="fa fa-envelope frm-ico"></i>
					</div>
					
					<div class="frm-input">
						<input type="password" name="admin_password" placeholder="Password" data-error="Please enter password" title="Please enter your password" class="frm-inp">
						<i class="fa fa-lock frm-ico"></i>
					</div>
					
					<div class="clearfix margin-bottom-20">
						<div class="pull-right"><a href="<?php echo base_url('admin/forgot-password');?>" class="a-link"><i class="fa fa-unlock-alt"></i>Forgot password?</a></div>
					</div>
					<input type="hidden" name="logtype" id="logtype">
					<input type="hidden" name="objectid" id="objectid">
					
					<button type="submit" class="frm-submit">Login<i class="fa fa-arrow-circle-right"></i></button>
				</div>
			</form>
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
		$('#myModal').modal({
				show: false,
				backdrop: 'static',
				keyboard: false
		});

		$(document).on('submit', '.frm-single', function(e){
			toastr.remove();
			e.preventDefault();
			err=0;
			$.each($('.frm-inp'), function(i, v){
				if($(this).val()!=''){
					if($(this).attr('name') == 'admin_email'){
						var regex = /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
						if(!regex.test($(this).val())){
							err++;
							var $toastlast = toastr['error']($(this).data('error'), 'Oops!');
							return false;
						}
					}
				} else {
					err++;
					var $toastlast = toastr['error']($(this).data('error'), 'Oops!');
					return false;
				}
			});
			
			if(err==0){
				$(this)[0].submit();
			}
		});

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