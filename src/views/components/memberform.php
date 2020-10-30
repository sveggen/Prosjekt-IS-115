
    <form action="" method="post">
    <div class="form-group">
        <h3>Add Member</h3>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="firstname">Firstname: </label>
            <input type="text" class="form-control" name="firstname" placeholder="Ola" 
             <?php include('../components/firstname_exists.php') ?> />
        </div>
        <div class="form-group ">
<label for="etternavn">Etternavn: </label>
 <input type="text" class="form-control" name="etternavn" placeholder="Nordmann" 
 <?php echo (!empty($_POST['etternavn'])) ? ('value = "'.$_POST["etternavn"].'"') : "value = \"\"";  ?> />
 <?php if (empty($_POST['etternavn']) && isset($_POST['submit'])){
   $mangler_verdier = true;
   ?>
  <small class="form-text text-danger">Fyll inn et etternavn.</small>
<?php }?>
</div>
    </div>
    <div class="form-group ">
        <label for="email">Email: </label>
        <input type="email" class="form-control" name="epost" placeholder="ola@mail.no" />
    </div>
    <div class="form-group">
        <label for="phonenumber">Phone number: </label>
        <input type="tel" class="form-control" name="phonenumber" pattern="[0-9]{8}" placeholder="12345678" />
    </div>
    <div class="form-group">
        <label for="dob">Date of birth: </label>
        <input type="date" class="form-control" min="1900-01-01" max="2010-01-01" name="dob" placeholder />
    </div>
    <div class="form-group">
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="mann" name="gender" value="male" class="custom-control-input" />
            <label class="custom-control-label" for="male">Male</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="dame" name="gender" value="female" class="custom-control-input" />
            <label class="custom-control-label" for="female">Female</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="annet" name="gender" value="other" class="custom-control-input" />
            <label class="custom-control-label" for="other">Other</label>
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-primary btn-block" name="submit" type="submit">Add Member</button>
    </div>
