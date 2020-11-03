<?php $title = "Register";
include('./components/header.php') ?>


    <div class="box">

        <form action="" method="post">
            <div class="form-group">
                <h3>Register</h3>
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

            <?php

            function check_password($password)
            {
                $uppercase = preg_match('@[A-Z]@', $password);
                $lowercase = preg_match('@[a-z]@', $password);
                $number = preg_match('@[0-9]@', $password);

                if (($uppercase || $lowercase || $number || strlen($password) < 8)) {
                    return false;
                } else {
                    return true;
                }
            }

            if (isset($_POST['submit'])) {
                if (check_password($_POST['password']) == true) {
                    echo "password meets the requirements";

                    $options = [
                        'cost' => 9
                    ];
                    $hash = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);
                    $email = $_POST['email'];
                } else {
                    echo "password does not meet the requirements.";
                }
            }
            ?>


    </div>
<?php include('./components/footer.php') ?>