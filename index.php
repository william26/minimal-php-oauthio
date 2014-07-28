<?php 

spl_autoload_register(function ($class) {
	if (file_exists('src/' . $class . '.php')) {
		include 'src/' . $class . '.php';	
	}
	if (file_exists('lib/' . $class . '.php')) {
		include 'lib/' . $class . '.php';	
	}
});

$obj = new OAuth_io\OAuth();

$obj->initialize('your_app_key', 'your_app_secret');

if (isset($_GET['state'])) {

	echo $obj->generateStateToken();
}
else if (isset($_GET['code'])) {

	$req = $obj->auth('facebook', array(
		"code" => $_GET['code']
	));
	$response = $req->get('/me');
	echo $response['name'];
}
else {

?>

<html>
	<head>
		<title>Retrieve my name</title>
	</head>
	<body>
		<h1>Retrieve my name from Facebook with OAuth.io</h1>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="./oauth.js"></script>
		<button class="state">Get my name from Facebook</button>
		<div>
			Result: <span class="myname"></span>
		</div>
		<p>
			This app demonstrates how to use OAuth.io without Composer, using a single
			PHP script - it doesn't really deserve a best practices award, but it works ;)
		</p>
		<p>
			Just remember to setup your OAuth.io keys in the two "initialize" methods (back-end and front-end).
		</p>
		<script>
		$(document).ready(function () {
			$('.state').click(function () {
				$.ajax({
					url: '?state'
				})
					.done(function (result) {
						OAuth.initialize('your_app_key');
						OAuth.popup('facebook', {
							state: result
						})
							.done(function (result) {
								$('.myname').html("Loading your name, please wait");
								$.ajax({
									url: '?code=' + result.code
								})
									.done(function (r) {
										$('.myname').html(r);
									})
									.fail(function (e) {
										console.log(e);
									});
							})
							.fail(function (e) {
								console.log(e);
							});
					})
			});
		});
		</script>
	</body>
</html>

<?php } ?>

