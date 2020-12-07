<?php $this->load->view(USERURL.'/templates/header'); ?>
<?php if(count($result)>0) $result = $result[0]; ?>
<script>
var autocomplete;
function initAutocomplete() {
   autocomplete = new google.maps.places.Autocomplete((document.getElementById('address')));
   google.maps.event.addListener(autocomplete, 'place_changed', function () {
      var place = autocomplete.getPlace();
      var lat = place.geometry.location.lat(); 
      var lat = place.geometry.location.lng();
      $('#lat').val(location_lat);
      $('#lng').val(lat);
   });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_api_key;?>&signed_in=true&libraries=places&callback=initAutocomplete"
async defer></script>

<div id="wrapper">
   <div class="main-content">
      <div class="row small-spacing">
         <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open(USERURL.'/'.$_user_id.'/favorite-address/save',$attributes); ?>
                  <input type="hidden" name="objectid" value="<?php echo ($form_mode)?(string)$result['_id']:''; ?>">
                  <div class="form-group">
                     <label class="col-sm-3 control-label" for="title">Title <span class="req">*</span></label>
                     <div class="col-sm-9">
                        <input name="title" id="title" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['title']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-sm-3 control-label" for="address">Address <span class="req">*</span></label>
                     <div class="col-sm-9">
                        <input name="address" id="address" type="text" class="form-control" placeholder="" value="<?php echo (count($result)>0)?$result['address']:''; ?>" required>
                        <div class="help-block with-errors"></div>
                     </div>
                  </div>
                  <input name="lat" id="lat" type="hidden" class="form-control" value="<?php echo (count($result)>0)?$result['location']['lat']:''; ?>" required/>
                  <input name="lng" id="lng" type="hidden" class="form-control" value="<?php echo (count($result)>0)?$result['location']['lng']:''; ?>" required/>
                  
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
    <?php $this->load->view(USERURL.'/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>