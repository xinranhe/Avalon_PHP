<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> 	<html lang="en"> <!--<![endif]-->
<head>

    <!-- General Metas -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">	<!-- Force Latest IE rendering engine -->
    <title>Login Form</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Stylesheets -->
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/skeleton.css">
    <link rel="stylesheet" href="./css/layout.css">
    <link rel="stylesheet" href="generalBody.css">

</head>
<?php
    function outputMsg() {
        if(isset($_GET['msg'])) {
            echo urldecode($_GET['msg']);
        } else {
            echo "Please log in with your username and password.";
        }
    }
?>
<body>

<div class="notice">
    <a href="" class="close">close</a>
    <p class="warn"><?php outputMsg(); ?></p>
</div>



<!-- Primary Page Layout -->

<div class="container">

    <div class="form-bg">
        <form method="get" action="authMain.php">
            <h2>Login</h2>
            <p><input type="text" placeholder="Username" name="username"></p>
            <p><input type="password" placeholder="Password" name="password"></p>
            <button type="submit"></button>
            <form>
    </div>


</div><!-- container -->


<!-- End Document -->
</body>
</html>