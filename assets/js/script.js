$ = jQuery;

$(function()
{
	globalFuncs();
	manageLists();
});

function globalFuncs()
{
	$(document).on("click", ".js-select-item", function()
	{
		$(this).parent().addClass("selected");
	});

	$(document).on("click", ".js-unselect-item", function()
	{
		$(this).parent().removeClass("selected");
	});
}

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
					action  : "addList",
					request : {'list_name' : listName}
				}
			}).done(function(data)
			{
				if (data)
				{
					var html = '<p><a href="/edit-list.php?id='+parseInt(data)+'">'+listName+'</a></p>';

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

	$(document).on("click", ".js-add-item-to-list", function()
	{
		var form = $(this).closest(".form");
		var selectedOption = form.find("select option:selected");
		var itemID = parseInt(selectedOption.data("item_id"));
		var listID = parseInt(form.find("[name='list-id']").val());

		$.ajax(
		{
			type     : "POST",
			url      : "/ajax.php",
			dataType : "json",
			data     :
			{
				action  : "addItemToList",
				request :
				{
					'item_id' : itemID,
					'list_id' : listID
				}
			}
		}).done(function(data)
		{
			if (data)
			{
				var html = '<p>'+selectedOption.text()+'<span class="btn btn-danger btn-sm js-remove-item-from-list" data-item_id="'+itemID+'">Move to another list</span></p>';

				$(".list-items-container").append(html);
				$(".list-items-container").find("p.no-results").remove();
				selectedOption.remove();

				toastr.success("Item successfully added to List");
			}
			else
			{
				toastr.error("Could not add Item to List");
			}
		}).fail(function(data)
		{
			toastr.error("Could not perform request");
			console.log(data);
		});
	});

	$(document).on("click", ".js-move-items-to-list", function()
	{
		var listItemsContainer = $(".list-items-container");
		var selectedItems = listItemsContainer.find(".selected");
		var form = $(this).closest(".form");
		var selectedOption = form.find("select option:selected");
		var listID = parseInt(selectedOption.data("list_id"));
		var itemIDs = [];

		if (selectedItems.length > 0)
		{
			$.each(selectedItems, function()
			{
				itemIDs.push(parseInt($(this).data("item_id")));
			});

			$.ajax(
			{
				type     : "POST",
				url      : "/ajax.php",
				dataType : "json",
				data     :
				{
					action  : "moveItemsToList",
					request :
					{
						'item_ids' : itemIDs,
						'list_id'  : listID
					}
				}
			}).done(function(data)
			{
				if (data)
				{
					// var html = '<p>'+selectedOption.text()+'<span class="btn btn-danger btn-sm js-remove-item-from-list" data-item_id="'+itemID+'">Move to another list</span></p>';

					// $(".list-items-container").append(html);
					// $(".list-items-container").find("p.no-results").remove();
					// selectedOption.remove();

					toastr.success("Items successfully added to List");
				}
				else
				{
					toastr.error("Could not add Items to List");
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