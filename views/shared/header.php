<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- additional options to content to prevent zooming on mobile devices, maximum-scale=1, user-scalable=no"-->
		<title><?= $page_title; ?></title>
		<script type="text/javascript" src="<?= SITEURL; ?>/jQuery/jquery-1.12.3.min.js"></script>
		<script type="text/javascript" src="<?= SITEURL; ?>/jQuery/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		<script type="text/javascript" src="<?= SITEURL; ?>/assets/toastr/toastr.min.js"></script>
		<script type="text/javascript" src="<?= SITEURL; ?>/assets/moment.js/moment-with-locales.min.js"></script>
		<link rel="stylesheet" type="text/css" href="<?= SITEURL; ?>/assets/bootstrap-3.3.7-dist/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="<?= SITEURL; ?>/jQuery/jquery-ui-1.11.4/jquery-ui.min.css" />
		<link rel="stylesheet" type="text/css" href="<?= SITEURL; ?>/assets/toastr/toastr.min.css" />
		<link rel="stylesheet" type="text/css" href="<?= SITEURL; ?>/assets/fontawesome/css/all.css" />
		<link rel="stylesheet" type="text/css" href="<?= SITEURL; ?>/style.css" />
	</head>

	<body>
		<header class="wrapper page-header">
			<div class="container">
				<div class="row">
					<div class="col-xs-9 page-heading-container">
						<h2 class="page-heading"><?= $page_title; ?></h2>
					</div>

					<div class="home-link-container text-right">
						<a href="<?= SITEURL; ?>/"><i class="fas fa-home"></i></a>
					</div>

					<div class="mobile-navigation-container text-right">
						<i class="fas fa-bars"></i>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<label for="quick-add">Quick Add: </label>
						<div class="form">
							<input type="text" id="quick-add" name="item-description" />
							<button class="btn btn-primary btn-sm js-quick-add-item">Add</button>
							<button class="btn btn-primary btn-sm js-quick-edit-item">Edit</button>
						</div>
					</div>
				</div>
			</div>
		</header>

		<nav class="container">
			<div class="row">
				<div class="mobile-navigation-container col-xs-12 pull-right text-right">
					<i class="fas fa-times"></i>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<ul>
						<li><a href="<?= SITEURL; ?>/">Home</a></li>
						<li><a href="<?= SITEURL; ?>/items/">Manage Items</a></li>
						<li><a href="<?= SITEURL; ?>/items/?view-by=list">View By List</a></li>
						<li><a href="<?= SITEURL; ?>/items/?view-by=department">View By Dept</a></li>
						<li><a href="<?= SITEURL; ?>/items/?view-by=primary_dept">Primary Depts</a></li>
						<li><a href="<?= SITEURL; ?>/items/?view-by=suggestions">Suggestions</a></li>
						<li><a href="<?= SITEURL; ?>/items/?view-by=muted-suggestions">Muted Suggestions</a></li>
						<li><a href="<?= SITEURL; ?>/lists/">Manage Lists</a></li>
						<li><a href="<?= SITEURL; ?>/departments/">Manage Depts</a></li>
						<li><a href="<?= SITEURL; ?>/orders/">Manage Orders</a></li>
						<li><a href="<?= SITEURL; ?>/packsizes/">Manage PackSizes</a></li>
					</ul>
				</div>
			</div>
		</nav>
