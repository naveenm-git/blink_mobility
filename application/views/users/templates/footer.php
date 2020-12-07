	<footer class="footer">
		<ul class="list-inline">
			<li><?php echo $footer; ?></li>
		</ul>
	</footer>
	<script src="<?php echo base_url(); ?>assets/js/wow.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/scripts/jquery.min.js"></script>
	
	<script src="<?php echo base_url(); ?>assets/scripts/modernizr.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/nprogress/nprogress.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/sweet-alert/sweetalert.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/waves/waves.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/fullscreen/jquery.fullscreen-min.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/chart/loader.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/chart/chartjs/Chart.bundle.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/scripts/chart.chartjs.init.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/moment/moment.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/fullcalendar/fullcalendar.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/scripts/fullcalendar.init.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/chart/sparkline/jquery.sparkline.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/scripts/chart.sparkline.init.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/scripts/main.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/color-switcher/color-switcher.min.js"></script>
	
	<!-- Form Wizard -->
	<script src="<?php echo base_url(); ?>assets/plugin/form-wizard/prettify.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/form-wizard/jquery.bootstrap.wizard.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/jquery-validation/jquery.validate.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/scripts/form.wizard.init.min.js"></script>
	
	<!-- Data Tables -->
	<script src="<?php echo base_url(); ?>assets/plugin/datatables/media/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/datatables/media/js/dataTables.bootstrap.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/datatables/extensions/Responsive/js/responsive.bootstrap.min.js"></script>
	<!-- Toastr -->
	<script src="<?php echo base_url(); ?>assets/plugin/toastr/toastr.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/scripts/toastr.demo.min.js"></script>
	<!-- Date Time Picker -->
	<script src="<?php echo base_url(); ?>assets/plugin/datetimepicker/bootstrap-datetimepicker.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugin/daterangepicker/daterangepicker.js"></script>
	<!-- Validator -->
	<script src="<?php echo base_url(); ?>assets/plugin/validator/validator.min.js"></script>
	<!-- Responsive Table -->
	<script src="<?php echo base_url(); ?>assets/plugin/RWD-table-pattern/js/rwd-table.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/scripts/rwd.demo.min.js"></script>
	
	<!-- Select2 -->
	<link href="<?php echo base_url(); ?>assets/plugin/select2/css/select2.min.css" rel="stylesheet" />
	<script src="<?php echo base_url(); ?>assets/plugin/select2/js/select2.min.js"></script>
	
	<!-- TinyMCE Editor -->
	<style>
		div.mce-fullscreen {
			position: fixed;
			left: 5.5em;
			z-index: 1050;
			top: 51px !important;
			width: 94%;
		}
		.mce-notification {display:none!important;}
	</style>
	<script src="<?php echo base_url(); ?>assets/plugin/tinymce-4.9.9/js/tinymce/tinymce.min.js"></script>
	<!--<script src="https://cdn.tiny.cloud/1/6b989doxm51kfo2d6uh598j5vzovg9gsvu6qkyqvbjqhcpk9/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>-->

	<script>
		tinymce.init({
			selector: '.mceEditor',
			plugins: 'a11ychecker advcode casechange formatpainter linkchecker autolink lists checklist media mediaembed pageembed permanentpen powerpaste table advtable tinycomments tinymcespellchecker',
			toolbar: 'a11ycheck addcomment showcomments casechange checklist code formatpainter pageembed permanentpen table',
			toolbar_mode: 'floating',
			tinycomments_mode: 'embedded',
			tinycomments_author: 'Author name',
		});
	</script>

	<!-- <script>
		tinymce.init({
			selector: ".mceEditor",
			setup: function(editor) {
				editor.on('change', function(e) {
					editor.save();
					$(".mceEditor").trigger('change');
				});
			},
			height: 250,
			plugins: ["link image code"],
			toolbar: "insertfile undo redo | styleselect | bold italic underline blockquote | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | fullscreen | link image",
			relative_urls : false,
			remove_script_host : false,
			convert_urls : true,
			images_upload_handler: function (blobInfo, success, failure) {
				var xhr, formData;
				xhr = new XMLHttpRequest();
				xhr.withCredentials = false;
				xhr.open('POST', '<?php echo base_url();?>upload.php');
				xhr.onload = function() {
					var json;
					console.log(xhr.status);
					if (xhr.status != 200) {
						failure('HTTP Error: ' + xhr.status);
						return;
					}
					json = JSON.parse(xhr.responseText);

					if (!json || typeof json.location != 'string') {
						failure('Invalid JSON: ' + xhr.responseText);
						return;
					}
					success(json.location);
				};
				formData = new FormData();
				if( typeof(blobInfo.blob().name) !== undefined )
					fileName = blobInfo.blob().name;
				else
					fileName = blobInfo.filename();
				formData.append('file', blobInfo.blob(), fileName);
				formData.append('URL', '<?php echo base_url();?>');
				xhr.send(formData);
			}
		});
	</script> -->
	
	<script type="text/javascript">
		var baseurl = "<?php echo base_url(); ?>";
	</script>
	
	<script type="text/javascript">
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
  <?php if($sErrMSGType =='success'){ ?>
			var $toast = toastr['<?php echo $sErrMSGType; ?>'](msg, title); // Wire up an event handler to a button in the toast, if it exists
			$toastlast = $toast;
  <?php } ?>
  <?php if($sErrMSGType=='error'){ ?>	
			var $toast = toastr['<?php echo $sErrMSGType; ?>'](msg, title); // Wire up an event handler to a button in the toast, if it exists
			$toastlast = $toast;
  <?php } ?>
</script>
<?php } ?>

<script>
function error_msg(msg){
	var $toast = toastr['error'](msg, 'Oops!');
	$toastlast = $toast;
}

$(document).ready(function(){
	$(".sign-out").on("click", function(e) {
		return e.preventDefault(), swal({
				title: "Logout?",
				text: "Are you sure you want to logout?",
				type: "warning",
				showCancelButton: !0,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, I'm out!",
				cancelButtonText: "No, stay here!",
				closeOnConfirm: !1,
				closeOnCancel: !0,
				confirmButtonColor: "#f60e0e"
		}, function(e) {
			if(e){
				window.location = "<?php echo base_url(); ?>logout";
			}
		}), !1
	})
	
	<?php if($user_type!='superadmin'){ ?>
	$(".subadmin-cpass").on("click", function(e) {
		href = $(this).data('href');
		return e.preventDefault(), swal({
				title: "Change Password!",
				text: "Are you sure you want to change password?",
				type: "warning",
				showCancelButton: !0,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, Go ahead!",
				cancelButtonText: "No, Cancel!",
				closeOnConfirm: !1,
				closeOnCancel: !0,
				confirmButtonColor: "#f60e0e"
		}, function(e) {
			if(e){
				window.location = href;
			}
		}), !1
	})
	<?php } ?>
	
	$( ".petty-alert" ).hover(function(){ $('.petty-info').fadeIn('slow'); }, function() { $('.petty-info').fadeOut('slow'); });
	$( ".trail-alert" ).hover(function(){ $('.trail-info').fadeIn('slow'); }, function() { $('.trail-info').fadeOut('slow'); });
});
</script>
<script>
              new WOW().init();
              </script>
</body>
</html>