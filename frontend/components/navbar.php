<link rel="stylesheet" href="../styles/navbar.css">
<nav class="navbar">
    <a href="../pages/index.php">
        <h1 class="title">Disaster Relief Response</h1>
    </a>    
    <a href="contributor.php"> <button type="button"> Contribute</button></a>
    <a href="rc.php"> <button type="button"> Relief Center (Admin Page)</button></a>
    <a href="donationPage.php"> <button type="button"> Donate!</button></a>
    <div class="buttons">
        <button>Login</button>
        <button>Signup</button>
        <form method="POST" action="">
			<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
			<button class="reset_button" type="submit" name="btnClick">Reload Tables</button>
		</form>
    </div>
</nav>