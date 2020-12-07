<?php $this->load->view('admin/templates/header'); ?>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/parking/save',$attributes); ?>
						<input type="hidden" name="objectid" value="<?php echo ($form_mode)?(string)$result['_id']:''; ?>">
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Station <span class="req">*</span></label>
							<div class="col-sm-9">
                        <select name="station_id" id="station_id" class="form-control" required>
                           <option value="">Select an station</option>
                           <?php if($stations->num_rows() > 0){ ?>
                           <?php foreach($stations->result() as $station){ ?>
                              <option value="<?php echo (string) $station->_id; ?>" <?php echo (count($result)>0)?(($result['station_id']==(string)$station->_id)?'selected':''):''; ?>><?php echo $station->name; ?></option>
                           <?php } ?>
                           <?php } ?>
                        </select>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Title <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="name" id="name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['name']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label">Status</label>
							<div class="col-sm-9">
								<div class="switch success margin-top-10">
									<input type="checkbox" name="status" id="status" <?php echo (count($result)>0 && $result['status']=='1')?'checked':''; ?> />
									<label for="status">&nbsp;</label>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="android_link">&nbsp;</label>
							<div class="col-sm-9">
								<input type="submit" value="Submit" class="btn btn-sm btn-success"/>
							</div>
						</div>
					</form>	
				</div>
			</div>
		</div>
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>