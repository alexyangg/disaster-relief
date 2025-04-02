<link rel="stylesheet" href="../styles/navbar.css">
<nav class="navbar">
    <a href="../pages/index.php">
        <h1>Disaster Relief Response</h1>
    </a>    
    <a href="contributor.php"> <button type="button"> Contribute </button></a>
    <a href="rc.php"> <button type="button"> Relief Center </button></a>
    <a href="donationPage.php"> <button type="button"> Donate! </button></a>
    <div class="buttons">
        <button>Login</button>
        <button>Signup</button>
        <form method="POST" action="">
			<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
			<button type="submit" name="btnClick">Reload Tables</button>
		</form>
    </div>
</nav>