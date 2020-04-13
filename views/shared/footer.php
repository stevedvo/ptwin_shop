		<footer>
		</footer>
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

	<script type="text/javascript" src="<?= SITEURL; ?>/assets/js/script.js"></script>
</html>
