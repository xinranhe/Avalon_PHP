<html>
<body>
</body>
</html>
<?php
    include 'dbConfig.php';
    session_start();
    if( isset($_GET['username']) && isset($_GET['password']) )
    {
        if(auth($_GET['username'], $_GET['password']) )
        {
            // auth okay, setup session
            @ $_SESSION['user'] = $_GET['username'];
            @ $_SESSION['timeout'] = time();
            echo "Auth Succeed";
            // redirect to required page
            header( "Location: welcome.php" );
        } else {
            // didn't auth go back to loginform
           header( "Location: login.php" );
	    if(isset($_SESSION['user'])) unset($_SESSION['user']);
        }
    } else {
        // username and password not given so go back to login
        header( "Location: login.php" );
        if(isset($_SESSION['user'])) unset($_SESSION['user']);
    }
    function auth($userName, $password) {
        // connect to DB table to authenticate
        $con=getDBConnection();
        $sqlStr = "select * from User where UserName= ? and Password= ?";
        $stmt = $con->prepare($sqlStr);
        $stmt->bind_param("ss", $_GET['username'],$_GET['password']);
        $stmt->execute();
        $sqlResult = $stmt->get_result();
        return mysqli_num_rows($sqlResult)!=0;
    }
?>
