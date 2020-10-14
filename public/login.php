<?php $title = "Log in";
include('header.php'); ?>


<div class="box">

    <form action="" method="post">
        <div class="form-group">
            <h3>Log in</h3>
        </div>
        <div class="form-group">
            <label for="email">Email: </label>
            <input type="text" class="form-control" name="email" placeholder="email@email.com">
        </div>
        <div class="form-group ">
            <label for="password">Password: </label>
            <input type="password" class="form-control" name="password" placeholder="*******">
        </div>
        <div class="form-group">
            <button class="btn btn-primary btn-block" name="submit" type="submit">Log in</button>
        </div>





</div>
<?php include('footer.php') ?>