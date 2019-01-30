var $ = jQuery.noConflict();

$(document).ready(function($)
{
	removeDepartment();
});

$(window).on('resize', function()
{

});

$(window).on('scroll', function()
{

});

function removeDepartment()
{
	$(document).on("click", "input[name='edit-item']", function(e)
	{
		var $this = $(this);

		if ($this.val() == "Remove Dept")
		{
			e.preventDefault();
			var $parent = $this.closest("tr");
			var itemID = $parent.data('item');
			var deptID = $parent.data('dept');

			$.ajax(
			{
				type     : 'POST',
				url      : 'ajax.php',
				dataType : 'json',
				data :
				{
					'action'  : 'removeDepartment',
					'request' :
					{
						'item_id' : itemID,
						'dept_id' : deptID
					}
				}
			}).done(function(result)
			{
				if (result)
				{
					$parent.fadeOut(400, function()
					{
						$parent.remove();
					});
				}
			}).fail(function(result)
			{
				console.log(result);
			});
		}
	});
}