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
		<link rel="stylesheet" type="text/css" href="<?= SITEURL; ?>/style.css" />
	</head>

	<body>
		<header class="wrapper page-header">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<h1><?= $page_title; ?></h1>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<a class="btn btn-primary" href="<?= SITEURL; ?>/">Home</a>
						<a class="btn btn-primary" href="<?= SITEURL; ?>/items/">Manage Items</a>
						<a class="btn btn-primary" href="<?= SITEURL; ?>/items/?view-by=list">View By List</a>
						<a class="btn btn-primary" href="<?= SITEURL; ?>/items/?view-by=department">View By Dept</a>
						<a class="btn btn-primary" href="<?= SITEURL; ?>/items/?view-by=primary_dept">Primary Depts</a>
						<a class="btn btn-primary" href="<?= SITEURL; ?>/items/?view-by=suggestions">Suggestions</a>
						<a class="btn btn-primary" href="<?= SITEURL; ?>/lists/">Manage Lists</a>
						<a class="btn btn-primary" href="<?= SITEURL; ?>/departments/">Manage Depts</a>
						<a class="btn btn-primary" href="<?= SITEURL; ?>/orders/">Manage Orders</a>
					</div>
					<hr/>
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
