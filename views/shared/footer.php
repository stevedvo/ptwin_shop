		<footer>
		</footer>

		<div id="modal" class="modal" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<form action="" data-controller="" data-action="" method="post" id="modalForm" enctype="multipart/form-data">
						<div class="modal-header">
							<div class="row">
								<h4 class="modal-title col-xs-10"></h4>
								<div class="col-xs-2">
									<button type="button" class="close text-right" data-dismiss="modal"><span><i class="fa fa-times"></i></span></button>
								</div>
							</div>
						</div>
						<div class="modal-body"></div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-sm btn-primary">Submit</button>
							<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>

	<script type="text/javascript">
		constants =
		{
			"SITEURL" : "<?= SITEURL; ?>",
			"DEFAULT_CONSUMPTION_INTERVAL" : "<?= DEFAULT_CONSUMPTION_INTERVAL; ?>",
			"CONSUMPTION_PERIODS" : ['<?= implode("','", CONSUMPTION_PERIODS); ?>'],
			"DEFAULT_CONSUMPTION_PERIOD" : "<?= DEFAULT_CONSUMPTION_PERIOD; ?>"
		};
	</script>

	<script type="text/javascript" src="<?= SITEURL; ?>/assets/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?= SITEURL; ?>/assets/js/script.js"></script>
</html>
