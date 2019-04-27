$ = jQuery;

$(function()
{
	mamnageLists();
});

function mamnageLists()
{
	$(document).on("click", ".js-add-list", function()
	{
		var form = $(this).closest(".form");

		var validation = validateForm(form);

		if (Object.keys(validation).length == 0)
		{
			var listName = form.find("[name='list-name']").val();

			$.ajax(
			{
				type     : "POST",
				url      : "/ajax.php",
				dataType : "json",
				data     :
				{
					action : "addList",
					request : {list_name : listName}
				}
			}).done(function(data)
			{
				console.log("done");
				console.log(data);
			}).fail(function(data)
			{
				console.log("fail");
				console.log(data);
			});
		}
	});
}

function validateForm(form)
{
	var inputs = form.find("input, select, textarea");
	var result = {};

	// console.log(inputs);

	return result;
}