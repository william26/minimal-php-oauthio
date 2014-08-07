<?php
include ('./config.php');

spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    if (file_exists('src/' . $class . '.php')) {
        include 'src/' . $class . '.php';
    }
    if (file_exists('lib/' . $class . '.php')) {
        include 'lib/' . $class . '.php';
    }
});
$obj = new OAuth_io\OAuth();

$obj->initialize($config['key'], $config['secret']);

if (isset($_GET['state'])) {
    
    echo $obj->generateStateToken();
} else if (isset($_GET['code'])) {
    
    $req = $obj->auth('facebook', array(
        "code" => $_GET['code']
    ));
    $response = $req->get('/me');
    
    echo $response['name'];
} else {
?>

<html>
	<head>
		<title>Retrieve my name</title>
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	</head>
	<body>
	<div class="container">
		<h1>Testing OAuth.io PHP SDK without Composer</h1>
		<p>
			This app demonstrates how to use OAuth.io without Composer, using a single
			PHP script - it doesn't really deserve a best practices award, but it works ;)
		</p>
		<p>
			Just remember to copy <code>config.sample.php</code> to <code>config.php</code> and setup your OAuth.io keys in it.
		</p>
		<h3>Retrieve my name from Facebook with OAuth.io's popup</h3>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="./oauth.js"></script>
		<button class="state btn btn-success">Get my name from Facebook</button>
		<div>
			<strong>Result:</strong> <span class="myname_popup"></span>
		</div>
		
		<hr>
		<h3>Retrieve my name from Facebook with OAuth.io's redirect</h2>
		<button class="redirect_button btn btn-success">Redirect to Facebook to get my name</button>
		<div>
			<strong>Result:</strong> <span class="myname_redirect"></span>
		</div>
	</div>
	
		<script>
		$(document).ready(function () {
			OAuth.initialize("<?php echo $config['key'] ?>");
			$('.state').click(function () {
				$.ajax({
					url: '?state'
				})
					.done(function (result) {
						console.log ('popuping with code', result)
						OAuth.popup('facebook', {
							state: result
						})
							.done(function (result) {
								console.log(result);
								$('.myname_popup').html("Loading your name, please wait");
								$.ajax({
									url: '?code=' + result.code
								})
									.done(function (r) {
										$('.myname_popup').html(r);
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

			// Redirect method
			$('.redirect_button').click(function () {
				$.ajax({
					url: '?state'
				})
					.then(function (state) {
						OAuth.redirect('facebook', { state: state }, "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ?>");
					})
			});
			var callback = OAuth.callback('facebook');

			if (callback) {
				callback
					.then(function (result) {
						$('.myname_redirect').html("Loading your name, please wait");
							$.ajax({
								url: '?code=' + result.code
							})
								.done(function (r) {
									$('.myname_redirect').html(r);
								})
								.fail(function (e) {
									console.log(e);
								});
					})
					.fail(function (e) {
						console.log(e);
					});
			}

			

		});
		</script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	</body>
</html>

<?php
} ?>

