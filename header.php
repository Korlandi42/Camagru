
<?php  if (isset($_SESSION['id'])) { ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-custom">
  <a class="navbar-brand" href="index.php">Camagru</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="profil.php"><?php echo $_SESSION['username'] ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="montage.php">Montage</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href=logout.php>Logout</a>
      </li>
    </ul>
  </div>
</nav>

<?php } else { ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-custom">
  <a class="navbar-brand" href="index.php">Camagru</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="login.php">Login</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="register.php">Sign Up</a>
      </li>
    </ul>
  </div>
</nav>

<?php } ?>