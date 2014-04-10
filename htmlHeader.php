<div id="headerContainer" style="width: 100%; margin: 0; padding: 0;
    position: relative; background: #000000; color: white; height: 40px;">
    <div style="padding: 10px; font-size: 30px;">
        <span style="float: left; margin-right: 30px;">Avalon</span>
        <?php
            if(isset($_SESSION['user'])) {
                echo '<span style="font-size:20px; padding: 10px; float: left;">User : ' . $_SESSION['user'] . '</span>';
            }
        ?>
        <?php
            if(isset($_SESSION['user'])) {
                echo '<span style="float: right; margin-right: 10px;"><a href="logout.php" class="headerA">Log Out</a></span>';
            }
            else {
                echo '<span style="float: right; margin-right: 10px;"><a href="login.php" class="headerA">Log In</a></span>';
            }
        ?>
        <span style="float: right; margin-right: 10px;"><a href="javascript:history.go(0)" class="headerA">Refresh</a></span>
    </div>
</div>