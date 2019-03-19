<head>
    <meta charset="utf-8">
    <title>Change Password</title>
    <link href="/css/home.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
</head>

<body class="loggedin">
    <nav class="navtop">
        <div>
            <h1>Website Title</h1>
            <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </nav>
    <div class="content">
        <h2>Change Password</h2>
        <form action="updatepassword.php" method="POST">
            <p>New password</p>
            <input type="password" name="password" placeholder="Password">
            <input type="submit">
        </form>
    </div>
</body>

</html> 