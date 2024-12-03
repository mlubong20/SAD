<?php 
// Guard
require_once '_guards.php';
Guard::guestOnly();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale System - Login</title>
    <link rel="stylesheet" type="text/css" href="./css/main.css">
    <link rel="stylesheet" type="text/css" href="./css/login.css">
    <link rel="stylesheet" type="text/css" href="./css/util.css">
</head>
<body>

    <!-- Login Card -->
    <div class="login card">
        <!-- Logo Section -->
       

        <div class="card-content">
            <form method="POST" action="api/login_controller.php">
                <?php displayFlashMessage('login') ?>

                <div class="form-control">
                    <label>Email</label>
                    <input 
                        type="text" 
                        name="email" 
                        placeholder="Enter your email here" 
                        required="true" 
                    />
                </div>

                <div class="form-control mt-16">
                    <label>Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Enter your password here" 
                        required="true" 
                    />
                </div>

                <div class="mt-16 flex justify-end">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
