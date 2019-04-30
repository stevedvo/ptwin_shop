$ = jQuery;

$(function()
{
	manageLists();
});

function manageLists()
{
	$(document).on("click", ".js-add-list", function()
	{
		var form = $(this).closest(".form");

		form.find("p.error-message").remove();

		var validation = validateForm(form);

		if (Object.keys(validation).length > 0)
		{
			$.each(validation, function(field, errMsg)
			{
				form.find("[name='"+field+"']").addClass("input-error").after("<p class='error-message'>"+errMsg+"</p>");
			});

			toastr.error("There were validation failures");
		}
		else
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
				if (data)
				{
					var html = '<p><a href="/edit-list/?id='+parseInt(data)+'">'+listName+'</a></p>';

					$(".results-container").append(html);
					$(".results-container").find("p.no-results").remove();
					form.find(".input-error").removeClass("input-error");
					form.find("[name='list-name']").val("");

					toastr.success("New List successfully added");

				}
				else
				{
					toastr.error("Could not save List");
				}
			}).fail(function(data)
			{
				toastr.error("Could not perform request");
				console.log(data);
			});
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
									if (result[$thisInput.attr("name")] == undefined)
									{
										result[$thisInput.attr("name")] = "This field is required. ";
									}
									else
									{
										result[$thisInput.attr("name")]+= "This field is required. ";
									}
								}
							}
							break;
							case 'min-length':
							{
								if ($thisInput.val().length < criterion[1])
								{
									if (result[$thisInput.attr("name")] == undefined)
									{
										result[$thisInput.attr("name")] = "Must be "+criterion[1]+" characters or more. ";
									}
									else
									{
										result[$thisInput.attr("name")]+= "Must be "+criterion[1]+" characters or more. ";
									}
								}
							}
							break;
							case 'max-length':
							{
								if ($thisInput.val().length > criterion[1])
								{
									if (result[$thisInput.attr("name")] == undefined)
									{
										result[$thisInput.attr("name")] = "Must be "+criterion[1]+" characters or less. ";
									}
									else
									{
										result[$thisInput.attr("name")]+= "Must be "+criterion[1]+" characters or less. ";
									}
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