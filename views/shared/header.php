<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- additional options to content to prevent zooming on mobile devices, maximum-scale=1, user-scalable=no"-->
		<title><?= $page_title; ?></title>
		<script type="text/javascript" src="/jQuery/jquery-1.12.3.min.js"></script>
		<script type="text/javascript" src="/jQuery/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		<script type="text/javascript" src="/assets/toastr/toastr.min.js"></script>
		<link rel="stylesheet" type="text/css" href="/assets/bootstrap-3.3.7-dist/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="/jQuery/jquery-ui-1.11.4/jquery-ui.min.css" />
		<link rel="stylesheet" type="text/css" href="/assets/toastr/toastr.min.css" />
		<link rel="stylesheet" type="text/css" href="/style.css" />
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
						<a class="btn btn-primary" href="/">Home</a>
						<a class="btn btn-primary" href="/items/">Manage Items</a>
						<a class="btn btn-primary" href="view-by-freq.php">View By Freq</a>
						<a class="btn btn-primary" href="/lists/">Manage Lists</a>
						<a class="btn btn-primary" href="view-by-dept.php">View By Dept</a>
						<a class="btn btn-primary" href="/departments/">Manage Depts</a>
					</div>
					<hr/>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<div class="ui-widget">
							<label for="quick-add">Quick Add: </label>
							<div class="form">
								<input type="text" id="quick-add" name="item-description" />
								<button class="btn btn-primary btn-sm js-quick-add-item">Add</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
