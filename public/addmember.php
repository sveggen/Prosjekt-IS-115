<?php $title = "Add Member";
include('header.php'); ?>

<div class="box">
    <form action="" method="post">
        <div class="form-group">
            <h3>Add Member</h3>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="firstname">Firstname: </label>
                <input type="text" class="form-control" name="firstname" placeholder="Ola">
            </div>
            <div class="form-group ">
                <label for="lastname">Lastname: </label>
                <input type="text" class="form-control" name="lastname" placeholder="Nordmann">
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-primary btn-block" name="submit" type="submit">Add Member</button>
        </div>
</div>
<?php include('footer.php') ?>