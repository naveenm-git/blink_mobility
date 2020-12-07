<?php $this->load->view('admin/templates/header'); ?>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">			
			<div class="col-sm-6 col-lg-3 col-xs-12">
				<a href="<?php echo base_url().'admin/users-list';?>">
               <div class="box-content">
                  <div class="content widget-stat">
                     <div class="left-content">
                        <h4 class="counter text-success">Active Users</h4>
                     </div>
                     <div class="right-content">
                        <h2 class="counter text-success"><?php echo $activeUsers; ?></h2>
                     </div>
                  </div>
               </div>
            </a>
			</div>
			
			<div class="col-sm-6 col-lg-3 col-xs-12">
				<a href="<?php echo base_url().'admin/station-list';?>">
               <div class="box-content">
                  <div class="content widget-stat">
                     <div class="left-content">
                        <h4 class="counter text-primary">Stations</h4>
                     </div>
                     <div class="right-content">
                        <h2 class="counter text-primary"><?php echo $activeStations; ?></h2>
                     </div>
                  </div>
               </div>
            </a>
			</div>
			
			<div class="col-sm-6 col-lg-3 col-xs-12">
				<a href="<?php echo base_url().'admin/vehicle-list';?>">
				<div class="box-content">
					<div class="content widget-stat">
						<div class="left-content">
							<h4 class="counter text-warning">Vehicles</h4>
						</div>
						<div class="right-content">
							<h2 class="counter text-warning"><?php echo $activeVehicles; ?></h2>
						</div>
					</div>
				</div>
            </a>
			</div>
			
			<div class="col-sm-6 col-lg-3 col-xs-12">
				<a href="<?php echo base_url().'admin/subscription-list';?>">
				<div class="box-content">
					<div class="content widget-stat">
						<div class="left-content">
							<h4 class="counter text-danger">Subscription Plans</h4>
						</div>
						<div class="right-content">
							<h2 class="counter text-danger"><?php echo $activePlans; ?></h2>
						</div>
					</div>
				</div>
            </a>
			</div>
      </div>
		
		<!--<div class="row small-spacing">
			<div class="col-lg-12 col-xs-12">
				<div class="box-content">
					<div class="col-lg-10 col-xs-12">
					<input type="text" class="form-control rangepicker" placeholder="Select a daterange to filter"/>
					</div>
					<div class="col-lg-2 col-xs-12">
					<button class="btn btn-sm btn-primary pull-right" id="randomizeData">Filter</button>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-xs-12">
				<div class="box-content">
					<h4 class="box-title">Events - Type wise data</h4>
					<canvas id="bar-chart" class="chartjs-chart" width="480" height="320"></canvas>
				</div>
			</div>
			
			<div class="col-lg-6 col-xs-12">
				<div class="box-content">
					<h4 class="box-title">Articles - Category wise data</h4>
					<canvas id="donut-chart" class="chartjs-chart" width="475" height="316"></canvas>
				</div>
			</div>
		</div>-->
		
		<?php $this->load->view('admin/templates/footer'); ?>
		<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
		<script>
		$(document).ready(function(){

			$('.rangepicker').daterangepicker({
				locale: {
					cancelLabel: 'Cancel',
					format: 'DD/MM/YYYY'
				}
			});
			$('.rangepicker').val('');
			
			dlabel=[];dvalue=[];
			<?php for($dl=0;$dl<count($donutchart['label']);$dl++){ ?>
			dlabel.push("<?php echo $donutchart['label'][$dl]; ?>");
			dvalue.push("<?php echo $donutchart['value'][$dl]; ?>");
			<?php } ?>
			
			blabel=[];bvalue=[];
			<?php for($bl=0;$bl<count($barchart['label']);$bl++){ ?>
			blabel.push("<?php echo $barchart['label'][$bl]; ?>");
			bvalue.push("<?php echo $barchart['value'][$bl]; ?>");
			<?php } ?>
			
			var donut_config = {
				type: 'doughnut',
				data: {
					datasets: [{
						data: dvalue,
						backgroundColor: [
							'rgba(255, 99, 132, 0.4)',
							'rgba(54, 162, 235, 0.4)',
							'rgba(255, 206, 86, 0.4)',
							'rgba(75, 192, 192, 0.4)',
							'rgba(153, 102, 255, 0.4)',
							'rgba(255, 159, 64, 0.4)'
						]
					}],
					labels: dlabel
				},
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					animation: {
						animateScale: true,
						animateRotate: true
					}
				}
			};
						
			var bar_config = {
				type: 'bar',
				data: {
					labels: blabel,
					datasets: [{
						label: 'Total',
						backgroundColor: [
							'rgba(255, 99, 132, 0.4)',
							'rgba(54, 162, 235, 0.4)',
							'rgba(255, 206, 86, 0.4)',
							'rgba(75, 192, 192, 0.4)',
							'rgba(153, 102, 255, 0.4)',
							'rgba(255, 159, 64, 0.4)'
						],
						borderColor: "rgb(0, 0, 0, 0.1)",
						borderWidth: 1,
						hoverBackgroundColor: [
							'rgba(255, 99, 132, 0.6)',
							'rgba(54, 162, 235, 0.6)',
							'rgba(255, 206, 86, 0.6)',
							'rgba(75, 192, 192, 0.6)',
							'rgba(153, 102, 255, 0.6)',
							'rgba(255, 159, 64, 0.6)'
						],
						hoverBorderColor: "rgb(0, 0, 0, 0.1)",
						data: bvalue
					}]
				},
				options: {
					hover: {
						mode: 'label'
					},
					responsive: true,
					scales: {
						xAxes: [{
							ticks: {
								beginAtZero:true
							},
						}],
						yAxes: [{
							ticks: {
								beginAtZero:true,
								userCallback: function(label, index, labels) {
									if (Math.floor(label) === label) {
										return label;
									}
								 }
							}
						}],
					},

				}
			};
		
			var bar = document.getElementById('bar-chart').getContext("2d");
			var barchart = new Chart(bar, bar_config);
		
			var donut = document.getElementById('donut-chart').getContext('2d');
			var doughnut = new Chart(donut, donut_config);

			$('#randomizeData').on('click', function() {
				$.ajax({
					url: '<?php echo base_url(); ?>admin/admin/dashboard_ajax',
					type: 'POST',
					data: {date: $('.rangepicker').val()},
					success: function(d){
						d=JSON.parse(d);
						console.log(d);
						donut_config.data.labels = d.data.donutchart.label;
						donut_config.data.datasets = [{
							data: d.data.donutchart.value,
							backgroundColor: [
								'lightblue', 'lightgreen', 'lightpink',
								"#f9c851",
								"#3ac9d6",
								"#ebeff2",
								"#fcffcc"
							],
							label: d.data.donutchart.label
						}];
						bar_config.data.labels = d.data.barchart.label;
						bar_config.data.datasets = [{
							label: 'Total',
							backgroundColor: [
								"lightblue",
								"#f9c851",
								"#3ac9d6",
								"#ebeff2",
								"#fcffcc"
							],
							borderColor: "rgb(0, 0, 0, 0.1)",
							borderWidth: 1,
							hoverBackgroundColor: [
								"lightblue",
								"#f9c851",
								"#3ac9d6",
								"#ebeff2",
								"#fcffcc"
							],
							hoverBorderColor: "rgb(0, 0, 0, 0.1)",
							data: d.data.barchart.value
						}];
					
						barchart.update();
						doughnut.update();
					}
				});
			});
		});
			
		function randomScalingFactor() {
			return Math.round(Math.random() * 100);
		}
		</script>
	</div>
</div>