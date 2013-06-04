<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?> || SCC</title>
	<?php echo Asset::css('bootstrap.css'); ?>
	<meta name="viewport" content="width=device-width">
	<style>
		body { margin: 60px; }
		.rawr {	margin-top: 10px; }
		.table th, .table td { padding: 4px; }
	</style>
</head>
<body>
<?php if ($current_user_id): ?>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="brand" href="<?php echo Uri::base(); ?>">SCC</a>
				<p class="navbar-text pull-right">Logged in as <a href="<?php echo Uri::create('accounts/logout'); ?>" class="navbar-link"><?php echo $current_user_name; ?></a>
            </p>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li class="<?php echo (Uri::segment(1) == 'dashboard' or Uri::segment(1) == '') ? 'active' : ''; ?>"><a href="<?php echo Uri::create('dashboard'); ?>">Dashboard</a></li>
						<li class="<?php echo (Uri::segment(1) == 'accounts') ? 'active' : ''; ?>"><a href="<?php echo Uri::create('accounts'); ?>">Accounts</a></li>
						<li class="<?php echo (Uri::segment(1) == 'hosts') ? 'active' : ''; ?>"><a href="<?php echo Uri::create('hosts'); ?>">Hosts</a></li>
						<li class="<?php echo (Uri::segment(1) == 'databases') ? 'active' : ''; ?>"><a href="<?php echo Uri::create('databases'); ?>">Databases</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
	<div class="container">
		<div class="row">
			<div class="span12">
	<?php if (Session::get_flash('success')): ?>
				<div class="alert alert-success">
					<strong>Success</strong>
					<p>
					<?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
					</p>
				</div>
	<?php endif; ?>
	<?php if (Session::get_flash('error')): ?>
				<div class="alert alert-error">
					<strong>Error</strong>
					<p>
					<?php echo implode('</p><p>', e((array) Session::get_flash('error'))); ?>
					</p>
				</div>
	<?php endif; ?>
			</div>
		</div>
<?php echo $content; ?>
		<footer>
			<p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
			<p>
				<a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
				<small>Version: <?php echo e(Fuel::VERSION); ?></small>
			</p>
		</footer>
	</div>
</body>
</html>