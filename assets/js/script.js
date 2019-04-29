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

		if (Object.keys(validation).length > 0)
		{
			$.each(validation, function(field, errMsg)
			{
				form.find("[name='"+field+"']").after("<p>"+errMsg+"</p>");
			});
		}
		else
		{
			var listName = form.find("[name='list-name']").val();

			// $.ajax(
			// {
			// 	type     : "POST",
			// 	url      : "/ajax.php",
			// 	dataType : "json",
			// 	data     :
			// 	{
			// 		action : "addList",
			// 		request : {list_name : listName}
			// 	}
			// }).done(function(data)
			// {
			// 	if (data)
			// 	{
			// 		var html =
			// 		'<tr>'+
			// 			'<input type="hidden" name="list-id" value="'+parseInt(data)+'" />'+
			// 			'<td><input type="submit" name="view-list" value="View" /></td>'+
			// 			'<td><input type="text" name="list-name" value="'+listName+'" /></td>'+
			// 			'<td><input type="submit" name="update-list" value="Update" /></td>'+
			// 		'</tr>';

			// 		$(".results-table").append(html);

			// 		toastr.success("New List successfully added");

			// 	}
			// 	else
			// 	{
			// 		toastr.error("An error occurred");
			// 	}
			// }).fail(function(data)
			// {
			// 	toastr.error("Could not perform request");
			// 	console.log(data);
			// });
		}
	});
}

function validateForm(form)
{
	var inputs = form.find("input, select, textarea");
	var result = {};

	if (inputs.length > 0)
	{
		$.each(inputs, function()
		{
			var $thisInput = $(this);
			var validation = $thisInput.data("validation");

			if (validation != "" && validation != undefined)
			{
				var criteria = validation.split("_");

				if (criteria.length > 0)
				{
					var criterion = [];

					for (var i = 0; i < criteria.length; i++)
					{
						criterion = criteria[i].split(":");

						switch (criterion[0])
						{
							case 'required':
							{
								if ($thisInput.val().length == 0)
								{
									result[$thisInput.attr("name")] = "This field is required";
								}
							}
							break;
							case 'max-length':
							{
								if ($thisInput.val().length > criterion[1])
								{
									result[$thisInput.attr("name")] = "Must be "+criterion[1]+" characters or less";
								}
							}
							break;
						}
					}
				}
			}
		});
	}

	return result;
}