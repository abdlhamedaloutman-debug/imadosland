<?php
session_start();

// === SIMPLE FILE-BASED KINGDOM SYSTEM (NO DATABASE) ===
$citizens_file = 'citizens.json';
if(!file_exists($citizens_file)) file_put_contents($citizens_file, json_encode([]));

$KING_USER = 'king';
$KING_PASS = 'imadosland123';

$citizens = json_decode(file_get_contents($citizens_file), true);
$message = '';

// LOGIN KING
if(isset($_POST['king_login'])){
  if($_POST['username']===$KING_USER && $_POST['password']===$KING_PASS){
    $_SESSION['king']=true;
  } else {
    $message = 'Invalid royal credentials.';
  }
}

// LOGOUT
if(isset($_GET['logout'])){
  session_destroy();
  header('Location: index.php');exit;
}

// APPLY CITIZEN
if(isset($_POST['apply'])){
  $citizens[] = [
    'name'=>htmlspecialchars($_POST['fullname']),
    'email'=>htmlspecialchars($_POST['email']),
    'country'=>htmlspecialchars($_POST['country']),
    'status'=>'Pending'
  ];
  file_put_contents($citizens_file, json_encode($citizens, JSON_PRETTY_PRINT));
  $message = 'Application submitted successfully!';
}

// APPROVE / REJECT
if(isset($_SESSION['king']) && isset($_GET['action'])){
  $id = intval($_GET['id']);
  if(isset($citizens[$id])){
    if($_GET['action']=='approve') $citizens[$id]['status']='Approved';
    if($_GET['action']=='reject') $citizens[$id]['status']='Rejected';
    file_put_contents($citizens_file, json_encode($citizens, JSON_PRETTY_PRINT));
  }
  header('Location: index.php');exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Kingdom of Imadosland</title>
<style>
body{margin:0;font-family:Arial;background:#0b1324;color:#fff}
header{background:#071530;padding:20px;border-bottom:3px solid gold;text-align:center}
.logo{font-size:34px;color:gold;font-weight:bold}
section{padding:40px;max-width:900px;margin:auto}
.card{background:#121c36;padding:25px;border-radius:15px;margin:20px 0}
input,button{padding:10px;margin:5px;width:100%}
button{background:gold;border:0;font-weight:bold;cursor:pointer}
table{width:100%;border-collapse:collapse}
th,td{border:1px solid #555;padding:8px;text-align:center}
.msg{background:#0f6b4f;padding:10px;margin:10px 0}
</style>
</head>
<body>

<header>
  <div class="logo">👑 Kingdom of Imadosland 👑</div>
  <p>Official Digital Monarchy of King Imad</p>
</header>

<section>

<?php if($message): ?>
<div class="msg"><?php echo $message; ?></div>
<?php endif; ?>

<div class="card">
<h2>Apply for Digital Citizenship</h2>
<form method="post">
<input name="fullname" placeholder="Full Name" required>
<input name="email" placeholder="Email" required>
<input name="country" placeholder="Country" required>
<button name="apply">Submit Application</button>
</form>
</div>

<div class="card">
<h2>Royal Administration</h2>

<?php if(!isset($_SESSION['king'])): ?>
<form method="post">
<input name="username" placeholder="King Username">
<input name="password" type="password" placeholder="King Password">
<button name="king_login">Royal Login</button>
</form>
<?php else: ?>
<p>Welcome, Your Majesty 👑 | <a href="?logout=1" style="color:gold">Logout</a></p>

<table>
<tr><th>#</th><th>Name</th><th>Email</th><th>Country</th><th>Status</th><th>Action</th></tr>
<?php foreach($citizens as $i=>$c): ?>
<tr>
<td><?php echo $i+1; ?></td>
<td><?php echo $c['name']; ?></td>
<td><?php echo $c['email']; ?></td>
<td><?php echo $c['country']; ?></td>
<td><?php echo $c['status']; ?></td>
<td>
<a href="?action=approve&id=<?php echo $i; ?>">Approve</a> |
<a href="?action=reject&id=<?php echo $i; ?>">Reject</a>
</td>
</tr>
<?php endforeach; ?>
</table>

<?php endif; ?>
</div>

</section>
</body>
</html>
