  <!-- Bootstrap Modal for Registration -->
  <div class="modal fade" id="registrationModal" tabindex="-1" role="dialog" aria-labelledby="registrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registrationModalLabel">Contribution Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Registration Form -->
          <form @submit.prevent="submitRegistrationForm">

          <div class="form-group  " id="loading" style="display:none;">
            <div class="col-md-12">
              <center>
              <strong>
                <img src="<?php echo base_url() ?>assets/imgs/loading.gif" alt="CMULOGO" style="width:158px;height:154px;">
                </strong>
                </center>
             </div> 
          </div> 
          <div class="alert alert-danger messagebox" style="display: none;" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            <a style="color:black;"id="message_error" disabled >
              <b>You have following error(s):</b> 
              <ul id="error_content">
                 
              </ul>
            </a>


          </div> 
          <div class="custom-control custom-checkbox custom-control-solid">
              <input class="custom-control-input" id="createAnnouncementCheckbox" name="createannoucement" type="checkbox" value="createannoucement" v-model="formData.createannoucement">
              <label class="custom-control-label" for="createAnnouncementCheckbox">Create an Announcement</label>
          </div>
          <div class="custom-control custom-checkbox custom-control-solid">
              <input class="custom-control-input" id="customCheckSolid1" class="sendannouncement" type="checkbox" value="sendannouncement" v-model="formData.sendannouncement" >
              <label class="custom-control-label" for="customCheckSolid1">Send Announcement Through email;</label>
          </div>

          <div class="custom-control custom-checkbox custom-control-solid">
              <input class="custom-control-input" id="applyRecordCheckbox" type="checkbox" value="applyrecord" v-model="formData.applyrecord" name="applyrecord">
              <label class="custom-control-label" for="applyRecordCheckbox">Apply this record to all active members</label>
          </div>


          <div>
            <label for="contributionname">Name of Contribution:</label>
            <input type="text" id="contributionname" class="form-control" name="contributionname"  v-model="formData.contributionname" required>
            <span v-if="errors.contributionname" class="error">{{ errors.contributionname }}</span>
          </div>


          <div>
            <label for="amountofcontribution">Amount of Contribution:</label>
            <input type="number" id="amountofcontribution" name="amountofcontribution" class="form-control" v-model="formData.amountofcontribution" required>
            <span v-if="errors.amountofcontribution" class="error">{{ errors.amountofcontribution }}</span>
          </div>
          
          

          <div>
            <label for="desccontribution">Description of Contribution:</label>
            <input type="text" id="desccontribution" name="desccontribution" class="form-control" v-model="formData.desccontribution" >
            <span v-if="errors.desccontribution" class="error">{{ errors.desccontribution }}</span>
          </div>

          

        
          <button type="submit" class="btn btn-primary mt-3 submit_form_btn" id="submit_form_btn">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>
  <!-- End of Bootstrap Modal for Registration -->